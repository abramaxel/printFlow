<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tagihan PrintOrder</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333333; background-color: #f9fafb; margin: 0; padding: 0;">
    
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <!-- Header -->
        <div style="background-color: #2563eb; padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px;">🖨️ PrintOrder</h1>
            <p style="color: #bfdbfe; margin: 8px 0 0; font-size: 14px;">Tagihan Pesanan Digital Printing</p>
        </div>

        <!-- Konten Utama -->
        <div style="padding: 32px 24px;">
            <h2 style="color: #1f2937; margin-top: 0; font-size: 20px;">Halo {{ $invoice->order->user->name }},</h2>
            
            <p style="color: #4b5563; font-size: 15px;">
                Terima kasih telah mempercayakan kebutuhan cetak Anda kepada kami. Berikut adalah ringkasan tagihan untuk pesanan Anda:
            </p>

            <!-- Kotak Ringkasan Invoice -->
            <div style="background-color: #f3f4f6; border-left: 4px solid #2563eb; padding: 16px; border-radius: 4px; margin: 24px 0;">
                <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">No. Invoice</p>
                <p style="margin: 0 0 16px; font-size: 16px; font-weight: bold; color: #1f2937;">{{ $invoice->order->invoice_number }}</p>
                
                <p style="margin: 0 0 8px; font-size: 13px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Total Tagihan</p>
                <p style="margin: 0; font-size: 24px; font-weight: bold; color: #2563eb;">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</p>
            </div>

            <p style="color: #4b5563; font-size: 15px;">
                Silakan lakukan pembayaran sesuai dengan detail rekening yang tertera pada <strong>lampiran PDF</strong> di email ini.
            </p>

            <!-- Tombol Aksi (Dengan Proteksi Token) -->
            @if($hasToken)
                <div style="margin: 32px 0; text-align: center;">
                    <p style="color: #4b5563; font-size: 15px; margin-bottom: 16px;">
                        Sudah melakukan transfer? Upload bukti pembayaran Anda dengan mudah melalui tautan di bawah ini (Tanpa perlu login):
                    </p>
                    <a href="{{ $magicLink }}" 
                       style="display: inline-block; background-color: #2563eb; color: #ffffff; padding: 14px 28px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 16px;">
                       📤 Upload Bukti Transfer
                    </a>
                    <p style="color: #9ca3af; font-size: 12px; margin-top: 12px;">
                        *Tautan ini bersifat rahasia dan hanya berlaku untuk pesanan ini.
                    </p>
                </div>
            @else
                <!-- Tampilan Jika Token Belum Ada (Fallback Aman) -->
                <div style="background-color: #fef2f2; border: 1px solid #fecaca; border-radius: 6px; padding: 16px; margin: 24px 0;">
                    <p style="color: #b91c1c; margin: 0; font-size: 14px; font-weight: bold;">⚠️ Perhatian</p>
                    <p style="color: #991b1b; margin: 8px 0 0; font-size: 14px;">
                        Link upload bukti transfer belum tersedia. Silakan hubungi Admin kami melalui WhatsApp untuk informasi lebih lanjut.
                    </p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #9ca3af; font-size: 12px; margin: 0;">
                © 2026 PrintOrder. All rights reserved.<br>
                Jika Anda memiliki pertanyaan, silakan balas email ini.
            </p>
        </div>

    </div>

</body>
</html>