<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function show($token)
    {
        $invoice = Invoice::with(['order.user', 'order.items.service', 'order.items.material'])
            ->where('payment_token', $token)
            ->firstOrFail();

        // 🛡️ GUARD 1: Invoice dibatalkan → tampilkan halaman khusus
        if ($invoice->isCancelled()) {
            return response()->view('payments.cancelled', compact('invoice'), 410);
        }

        // 🛡️ GUARD 2: Pesanan sudah selesai → tidak terima pembayaran baru
        if ($invoice->order && $invoice->order->status === 'completed') {
            return response()->view('payments.completed', compact('invoice'));
        }

        return view('payments.show', compact('invoice'));
    }

    public function upload(Request $request, $token)
    {
        $invoice = Invoice::where('payment_token', $token)->firstOrFail();

        // 🛡️ GUARD: Tolak upload untuk invoice dibatalkan/selesai
        if ($invoice->isCancelled()) {
            return back()->withErrors(['proof' => 'Invoice ini telah dibatalkan dan tidak menerima pembayaran.']);
        }

        if ($invoice->order && $invoice->order->status === 'completed') {
            return back()->withErrors(['proof' => 'Pesanan sudah selesai dan tidak menerima pembayaran baru.']);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'proof' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'payment_method' => 'required|string|max:50',
        ]);

        $proofPath = $request->file('proof')->store('payment_proofs', 'public');

        Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->order->user_id,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'proof_file' => [$proofPath],
            'status' => 'pending',
        ]);

        // Kirim notifikasi ke semua staff internal
        notify('internal', new \App\Notifications\PaymentPendingNotification(
            invoiceNumber: $invoice->order->invoice_number ?? '#' . $invoice->id,
            amount: $request->amount,
            orderId: $invoice->order_id
        ));

        return back()->with('success', '✅ Bukti pembayaran berhasil diupload! Admin akan memverifikasi dalam 1x24 jam.');
    }
}