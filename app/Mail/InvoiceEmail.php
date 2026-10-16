<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tagihan Pesanan Anda - ' . $this->invoice->order->invoice_number,
        );
    }

    public function content(): Content
    {
        // Generate PDF untuk dilampirkan
        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $this->invoice])->output();
        $filename = 'Invoice-' . $this->invoice->order->invoice_number . '.pdf';

        // 🔥 PERBAIKAN DI SINI: Cek apakah token ada sebelum membuat route
        $magicLink = $this->invoice->payment_token 
            ? route('payment.show', $this->invoice->payment_token) 
            : '#'; // Fallback jika token kosong

        return new Content(
            view: 'emails.invoice',
            with: [
                'magicLink' => $magicLink,
                'filename' => $filename,
                'pdfData' => $pdf,
                'hasToken' => !is_null($this->invoice->payment_token), // Kirim flag ke view
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}