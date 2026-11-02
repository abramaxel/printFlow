<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function download(Request $request, $invoiceId)
    {
        $invoice = Invoice::with(['order.user', 'order.items.service', 'order.items.material', 'order.items.size'])->findOrFail($invoiceId);
        $user = $request->user();

        // 🛡️ GUARD IDOR: Cegah customer A download invoice customer B
        if ($user->role === 'customer') {
            if ($invoice->order->user_id !== $user->id) {
                abort(403, 'Anda tidak memiliki akses ke invoice ini.');
            }
        }

        // Guard tambahan: invoice yang dibatalkan tidak boleh didownload
        if ($invoice->isCancelled()) {
            abort(403, 'Invoice ini telah dibatalkan dan tidak dapat diunduh.');
        }

        $pdf = Pdf::loadView('invoices.invoice', compact('invoice'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Invoice-' . ($invoice->order->invoice_number ?? $invoice->id) . '.pdf');
    }
}