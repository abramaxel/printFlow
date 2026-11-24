<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public User $user)
    {
    }

    public function envelope(): Envelope
    {
        $brand = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
        return new Envelope(subject: 'Kode Pemulihan Kata Sandi — ' . $brand);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.reset-otp', with: [
            'code' => $this->code,
            'name' => $this->user->name,
            'minutes' => (int) round((int) config('otp.ttl', 600) / 60),
            'brand' => (string) config('app.brand.name', config('app.name', 'FC Murni Jaya')),
            'support' => (string) config('app.brand.whatsapp', ''),
        ]);
    }
}