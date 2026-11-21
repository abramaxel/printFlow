<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ShopSettings
{
    public const CACHE_KEY = 'shop.settings.values';

    /** Kanonik => kemungkinan nama kolom yang sudah dipakai versi lama. */
    public const FIELDS = [
        'shop_name'            => ['shop_name', 'name', 'store_name'],
        'shop_phone'           => ['shop_phone', 'phone', 'whatsapp', 'shop_whatsapp'],
        'shop_email'           => ['shop_email', 'email', 'store_email'],
        'shop_address'         => ['shop_address', 'address', 'store_address'],
        'shop_hours'           => ['shop_hours', 'hours', 'operational_hours'],
        'bank_name'            => ['bank_name', 'bank', 'payment_bank'],
        'bank_account'         => ['bank_account', 'account_number', 'bank_number'],
        'bank_holder'          => ['bank_holder', 'account_name', 'bank_account_name'],
        'invoice_footer'       => ['invoice_footer', 'footer', 'invoice_note'],
        'payment_instructions' => ['payment_instructions', 'payment_note', 'payment_notes'],
    ];

    public const DEFAULTS = [
        'shop_name'            => '',
        'shop_phone'           => '',
        'shop_email'           => '',
        'shop_address'         => '',
        'shop_hours'           => 'Senin–Sabtu, 08.00–17.00',
        'bank_name'            => '',
        'bank_account'         => '',
        'bank_holder'          => '',
        'invoice_footer'       => 'Terima kasih atas kepercayaan Anda.',
        'payment_instructions' => 'Transfer sesuai nominal tagihan, lalu unggah bukti pembayaran pada halaman ini.',
    ];

    protected static ?array $cols = null;
    protected static ?array $resolved = null;

    public static function columns(): array
    {
        if (self::$cols === null) {
            try {
                self::$cols = Schema::hasTable('shop_settings') ? Schema::getColumnListing('shop_settings') : [];
            } catch (\Throwable $e) {
                self::$cols = [];
            }
        }
        return self::$cols;
    }

    public static function isKv(): bool
    {
        $c = self::columns();
        return in_array('key', $c, true) && in_array('value', $c, true);
    }

    public static function resolved(): array
    {
        if (self::$resolved === null) {
            $c = self::columns();
            $out = [];
            foreach (self::FIELDS as $canon => $aliases) {
                $found = null;
                foreach ($aliases as $a) {
                    if (in_array($a, $c, true)) { $found = $a; break; }
                }
                $out[$canon] = $found;
            }
            self::$resolved = $out;
        }
        return self::$resolved;
    }

    public static function all(): array
    {
        try {
            $values = Cache::rememberForever(self::CACHE_KEY, fn () => self::fetch());
        } catch (\Throwable $e) {
            $values = self::fetch();
        }

        $out = self::DEFAULTS;
        foreach ($values as $k => $v) {
            if ($v !== null && array_key_exists($k, $out)) $out[$k] = (string) $v;
        }
        return $out;
    }

    protected static function fetch(): array
    {
        $cols = self::columns();
        if (empty($cols)) return [];

        try {
            if (self::isKv()) {
                return DB::table('shop_settings')->pluck('value', 'key')->all();
            }
            $row = DB::table('shop_settings')->orderBy('id')->first();
            if (!$row) return [];
            $raw = (array) $row;
            $out = [];
            foreach (self::resolved() as $canon => $col) {
                if ($col && array_key_exists($col, $raw)) $out[$canon] = $raw[$col];
            }
            return $out;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function put(array $data): void
    {
        $cols = self::columns();
        if (empty($cols)) return;

        $hasUpdated = in_array('updated_at', $cols, true);
        $hasCreated = in_array('created_at', $cols, true);

        if (self::isKv()) {
            foreach ($data as $k => $v) {
                $payload = ['value' => (string) $v];
                if ($hasUpdated) $payload['updated_at'] = now();

                $exists = DB::table('shop_settings')->where('key', $k)->exists();
                if ($exists) {
                    DB::table('shop_settings')->where('key', $k)->update($payload);
                } else {
                    $payload['key'] = $k;
                    if ($hasCreated) $payload['created_at'] = now();
                    DB::table('shop_settings')->insert($payload);
                }
            }
        } else {
            $payload = [];
            foreach (self::resolved() as $canon => $col) {
                if ($col && array_key_exists($canon, $data)) $payload[$col] = (string) $data[$canon];
            }
            if (empty($payload)) return;
            if ($hasUpdated) $payload['updated_at'] = now();

            $row = DB::table('shop_settings')->orderBy('id')->first();
            if ($row) {
                DB::table('shop_settings')->where('id', $row->id)->update($payload);
            } else {
                if ($hasCreated) $payload['created_at'] = now();
                DB::table('shop_settings')->insert($payload);
            }
        }

        self::flush();
    }

    public static function flush(): void
    {
        try { Cache::forget(self::CACHE_KEY); } catch (\Throwable $e) {}
        self::$cols = null;
        self::$resolved = null;
    }

    public static function wa(string $raw): string
    {
        $d = preg_replace('/\D+/', '', $raw) ?? '';
        if ($d === '') return '';
        if (str_starts_with($d, '0')) $d = '62' . substr($d, 1);
        elseif (str_starts_with($d, '8')) $d = '62' . $d;
        return $d;
    }
}