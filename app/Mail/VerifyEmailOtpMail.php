<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VerifyEmailOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code, public string $recipientName)
    {
    }

    public function envelope(): Envelope
    {
        $brand = (string) config('app.brand.name', config('app.name', 'FC Murni Jaya'));
        return new Envelope(subject: 'Kode Verifikasi Pendaftaran — ' . $brand);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verify-otp', with: [
            'code' => $this->code,
            'name' => $this->recipientName,
            'minutes' => (int) round((int) config('otp.ttl', 600) / 60),
            'brand' => (string) config('app.brand.name', config('app.name', 'FC Murni Jaya')),
            'support' => (string) config('app.brand.whatsapp', ''),
        ]);
    }
}