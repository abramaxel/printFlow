<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    // 1. Menampilkan Halaman Magic Link (Tanpa Login)
    public function show($token)
    {
        $invoice = Invoice::with(['order.user', 'order.items.service', 'order.items.material', 'order.items.size'])
            ->where('payment_token', $token)
            ->firstOrFail();

        // Ambil info rekening dari database (atau gunakan default jika belum ada)
        $bankName = ShopSetting::where('key', 'bank_name')->first()?->value ?? 'BCA';
        $bankAccount = ShopSetting::where('key', 'bank_account')->first()?->value ?? '1234567890';
        $bankHolder = ShopSetting::where('key', 'bank_holder')->first()?->value ?? 'PrintOrder Official';

        return view('payments.magic-link', compact('invoice', 'bankName', 'bankAccount', 'bankHolder'));
    }

    // 2. Memproses Upload Bukti Transfer
    public function upload(Request $request, $token)
    {
        $invoice = Invoice::with('order.user')->where('payment_token', $token)->firstOrFail();

        // Cek apakah sudah ada pembayaran pending
        $hasPendingPayment = $invoice->payments()->where('status', 'pending')->exists();
        
        if ($hasPendingPayment) {
            return back()->with('error', 'Anda sudah mengupload bukti transfer. Mohon tunggu verifikasi dari admin.');
        }

        $request->validate([
            'proof_files.*' => 'required|file|mimes:jpg,jpeg,png,pdf,zip|max:20480', 
        ]);

        // Simpan File
        $filePaths = [];
        if ($request->hasFile('proof_files')) {
            foreach ($request->file('proof_files') as $file) {
                $filePaths[] = $file->store('proofs', 'public');
            }
        }

        // Tentukan Nominal dari Schedule (yang masih pending)
        $amount = 0;
        $paymentDescription = 'Transfer Bank';
        
        if ($invoice->payment_schedule) {
            foreach ($invoice->payment_schedule as $schedule) {
                if ($schedule['status'] === 'pending') {
                    $amount = $schedule['amount'];
                    $paymentDescription = $schedule['description'];
                    break;
                }
            }
        }

        // Jika tidak ada schedule pending, pakai total_amount
        if ($amount === 0) {
            $amount = $invoice->total_amount - ($invoice->total_paid ?? 0);
            $paymentDescription = 'Pelunasan Sisa Tagihan';
        }

        // Buat Payment
        Payment::create([
            'invoice_id' => $invoice->id,
            'user_id' => $invoice->order->user_id,
            'amount' => $amount,
            'payment_method' => 'Transfer Bank ' . $request->bank_name_hidden . ' - ' . $paymentDescription,
            'proof_file' => $filePaths, 
            'status' => 'pending',
        ]);

        $invoice->update(['status' => 'unpaid']); 
        $invoice->order->update(['status' => 'unpaid']);

        return back()->with('success', 'Bukti transfer berhasil diupload! Menunggu verifikasi Admin.');
    }
}