<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    private const INTERNAL_ROLES = ['owner', 'super_admin', 'admin', 'cs', 'produksi', 'production'];

    /**
     * Preview PDF inline di tab browser.
     * Dipakai route admin (internal) MAUPUN route customer (pemilik invoice).
     */
    public function preview(Invoice $invoice)
    {
        $this->authorizeAccess($invoice);

        return $this->buildPdf($invoice)->stream($this->filename($invoice));
    }

    /**
     * Unduh PDF sebagai file.
     */
    public function download(Invoice $invoice)
    {
        $this->authorizeAccess($invoice);

        return $this->buildPdf($invoice)->download($this->filename($invoice));
    }

    /**
     * Guard: staff internal ATAU pemilik invoice. Invoice batal ditolak.
     */
    private function authorizeAccess(Invoice $invoice): void
    {
        $user = auth()->user();

        if (!$user) {
            abort(403, 'Silakan login terlebih dahulu.');
        }

        $isInternal = in_array($user->role, self::INTERNAL_ROLES, true);
        $isOwner    = $invoice->order && (int) $invoice->order->user_id === (int) $user->id;

        if (!$isInternal && !$isOwner) {
            abort(403, 'Anda tidak berhak mengakses invoice ini.');
        }

        if (method_exists($invoice, 'isCancelled') && $invoice->isCancelled()) {
            abort(403, 'Invoice ini telah dibatalkan dan tidak dapat diakses.');
        }
    }

    private function buildPdf(Invoice $invoice)
    {
        // Eager-load sekali agar PDF tidak memicu N+1 query
        $invoice->load(['order.user', 'order.items.service', 'order.items.material', 'payments']);

        return Pdf::loadView('invoices.pdf', ['invoice' => $invoice])
            ->setPaper('a4', 'portrait');
    }

    private function filename(Invoice $invoice): string
    {
        $number = $invoice->order->invoice_number ?? ('INV-' . $invoice->id);

        return $number . '.pdf';
    }
}