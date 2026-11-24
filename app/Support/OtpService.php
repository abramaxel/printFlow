<?php

namespace App\Support;

use App\Mail\ResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OtpService
{
    public static function whatsappReady(): bool
    {
        return (bool) config('otp.whatsapp.enabled') && trim((string) config('otp.whatsapp.token')) !== '';
    }

    public static function normalizePhone(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if ($d === '') return '';
        $cc = (string) config('otp.whatsapp.country_code', '62');
        if (str_starts_with($d, '0')) $d = $cc . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = $cc . $d;
        return $d;
    }

    public static function maskEmail(string $email): string
    {
        if (!str_contains($email, '@')) return $email;
        [$name, $domain] = explode('@', $email, 2);
        $len = mb_strlen($name);
        $head = mb_substr($name, 0, min(2, $len));
        return $head . str_repeat('•', max(3, $len - 2)) . '@' . $domain;
    }

    public static function maskPhone(string $phone): string
    {
        $d = self::normalizePhone($phone);
        if (mb_strlen($d) < 6) return $d;
        return substr($d, 0, 4) . str_repeat('•', max(3, strlen($d) - 7)) . substr($d, -3);
    }

    public static function findUser(string $identifier): ?User
    {
        $id = trim($identifier);
        if ($id === '') return null;

        if (str_contains($id, '@')) {
            return User::whereRaw('LOWER(email) = ?', [mb_strtolower($id)])->first();
        }

        $phone = self::normalizePhone($id);
        if ($phone === '') return null;

        $tail = substr($phone, -9);

        return User::whereNotNull('phone')
            ->get()
            ->first(function ($u) use ($phone, $tail) {
                $p = self::normalizePhone((string) $u->phone);
                return $p !== '' && ($p === $phone || str_ends_with($p, $tail));
            });
    }

    public static function throttleHit(string $identifier): bool
    {
        $since = now()->subSeconds((int) config('otp.throttle_window', 900));
        $count = PasswordResetOtp::where('identifier', $identifier)
            ->where('created_at', '>=', $since)
            ->count();

        return $count >= (int) config('otp.throttle_max', 5);
    }

    public static function cooldownRemaining(string $identifier): int
    {
        $last = PasswordResetOtp::where('identifier', $identifier)->latest('id')->first();
        if (!$last) return 0;
        $elapsed = now()->timestamp - $last->created_at->timestamp;
        return max(0, (int) config('otp.resend_cooldown', 60) - $elapsed);
    }

    public static function issue(User $user, string $channel, string $identifier): array
    {
        $length = (int) config('otp.length', 6);
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= (string) random_int(0, 9);
        }

        $destination = $channel === 'whatsapp'
            ? self::normalizePhone((string) $user->phone)
            : (string) $user->email;

        if ($destination === '') {
            return ['ok' => false, 'message' => 'Tujuan pengiriman tidak tersedia pada akun ini.'];
        }

        PasswordResetOtp::where('user_id', $user->id)
            ->whereNull('consumed_at')
            ->update(['consumed_at' => now()]);

        $record = PasswordResetOtp::create([
            'user_id' => $user->id,
            'identifier' => $identifier,
            'channel' => $channel,
            'destination' => $destination,
            'otp_hash' => Hash::make($code),
            'attempts' => 0,
            'expires_at' => now()->addSeconds((int) config('otp.ttl', 600)),
            'ip' => request()->ip(),
        ]);

        $sent = $channel === 'whatsapp'
            ? self::sendWhatsapp($destination, $code, $user)
            : self::sendEmail($destination, $code, $user);

        if (!$sent['ok']) {
            $record->delete();
            return ['ok' => false, 'message' => $sent['message']];
        }

        return [
            'ok' => true,
            'record' => $record,
            'masked' => $channel === 'whatsapp' ? self::maskPhone($destination) : self::maskEmail($destination),
        ];
    }

    private static function sendEmail(string $to, string $code, User $user): array
    {
        try {
            Mail::to($to)->send(new ResetOtpMail($code, $user));
            return ['ok' => true, 'message' => ''];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()];
        }
    }

    private static function sendWhatsapp(string $to, string $code, User $user): array
    {
        if (!self::whatsappReady()) {
            return ['ok' => false, 'message' => 'Gateway WhatsApp belum dikonfigurasi.'];
        }

        $brand = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
        $minutes = (int) round((int) config('otp.ttl', 600) / 60);

        $text = "Kode pemulihan kata sandi {$brand}: *{$code}*\n"
            . "Berlaku {$minutes} menit. Jangan bagikan kode ini kepada siapa pun, termasuk pihak yang mengaku admin.";

        try {
            $res = Http::withHeaders(['Authorization' => (string) config('otp.whatsapp.token')])
                ->timeout(15)
                ->asForm()
                ->post((string) config('otp.whatsapp.endpoint'), [
                    'target' => $to,
                    'message' => $text,
                    'countryCode' => (string) config('otp.whatsapp.country_code', '62'),
                ]);

            if ($res->successful() && ($res->json('status') === true || $res->json('status') === 'true')) {
                return ['ok' => true, 'message' => ''];
            }

            return ['ok' => false, 'message' => 'Gateway WhatsApp menolak pengiriman: ' . ($res->json('reason') ?? $res->status())];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Gagal menghubungi gateway WhatsApp: ' . $e->getMessage()];
        }
    }

    public static function verify(PasswordResetOtp $record, string $code): array
    {
        if (!$record->isUsable()) {
            return ['ok' => false, 'message' => 'Kode sudah kedaluwarsa. Minta kode baru.'];
        }

        $max = (int) config('otp.max_attempts', 5);
        if ($record->attempts >= $max) {
            return ['ok' => false, 'message' => 'Percobaan habis. Minta kode baru.'];
        }

        $record->increment('attempts');

        if (!Hash::check($code, $record->otp_hash)) {
            $left = max(0, $max - $record->fresh()->attempts);
            return ['ok' => false, 'message' => "Kode salah. Sisa {$left} percobaan."];
        }

        $token = Str::random(64);
        $record->forceFill([
            'verified_at' => now(),
            'reset_token' => hash('sha256', $token),
        ])->save();

        return ['ok' => true, 'token' => $token];
    }

    public static function consume(string $token): ?PasswordResetOtp
    {
        $rec = PasswordResetOtp::where('reset_token', hash('sha256', $token))
            ->whereNull('consumed_at')
            ->first();

        if (!$rec || !$rec->verified_at) return null;
        if ($rec->verified_at->addMinutes(15)->isPast()) return null;

        return $rec;
    }
}