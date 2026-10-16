<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function download($id)
    {
        // Ambil data invoice beserta relasinya
        $invoice = Invoice::with(['order.user', 'order.items.service', 'order.items.material', 'order.items.size'])
            ->findOrFail($id);

        // Load view dan generate PDF
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
                  ->setPaper('a4', 'portrait'); // Pastikan ukuran A4
        
        $filename = 'Invoice-' . $invoice->order->invoice_number . '.pdf';

        // 🔥 PERBAIKAN DI SINI: 
        // stream() akan membuka PDF di tab baru browser (bisa langsung di-print)
        // download() akan memaksa browser langsung mengunduh file
        return $pdf->stream($filename);
    }
}