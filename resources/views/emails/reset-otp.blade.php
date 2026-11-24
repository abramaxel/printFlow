<!DOCTYPE html>
<html lang="id">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#e2edf0;font-family:'Segoe UI',Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#e2edf0;padding:28px 12px;">
    <tr><td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:520px;background:#ffffff;border-radius:20px;overflow:hidden;border:1px solid #cfe0e4;">
            <tr>
                <td style="background:#0a3f33;padding:26px 30px;">
                    <p style="margin:0;color:#c8ec3f;font-size:11px;letter-spacing:.22em;text-transform:uppercase;font-weight:700;">Pemulihan Akun</p>
                    <p style="margin:8px 0 0;color:#ffffff;font-size:22px;font-weight:800;">{{ $brand }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding:30px;">
                    <p style="margin:0 0 6px;color:#0a3f33;font-size:16px;font-weight:700;">Halo, {{ $name }}</p>
                    <p style="margin:0 0 22px;color:#4b6b63;font-size:14px;line-height:1.6;">
                        Kami menerima permintaan pengaturan ulang kata sandi. Gunakan kode berikut untuk melanjutkan.
                    </p>

                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                        <tr><td align="center" style="background:#f2f7f8;border:2px dashed #1d6247;border-radius:16px;padding:22px;">
                            <p style="margin:0 0 8px;color:#6b8b83;font-size:10px;letter-spacing:.2em;text-transform:uppercase;font-weight:700;">Kode Verifikasi</p>
                            <p style="margin:0;color:#0a3f33;font-size:38px;font-weight:800;letter-spacing:.32em;font-family:'Courier New',monospace;">{{ $code }}</p>
                        </td></tr>
                    </table>

                    <p style="margin:20px 0 0;color:#4b6b63;font-size:13px;line-height:1.6;">
                        Kode berlaku <strong>{{ $minutes }} menit</strong> dan hanya dapat dipakai satu kali.
                    </p>

                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                        <tr><td style="background:#fff7ed;border-left:4px solid #f59e0b;border-radius:10px;padding:14px 16px;">
                            <p style="margin:0;color:#92400e;font-size:12.5px;line-height:1.6;">
                                <strong>Jangan bagikan kode ini.</strong> Tim {{ $brand }} tidak pernah meminta kode OTP melalui telepon, chat, atau email.
                                Bila Anda tidak merasa meminta, abaikan pesan ini — kata sandi Anda tetap aman.
                            </p>
                        </td></tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="background:#f2f7f8;padding:18px 30px;border-top:1px solid #cfe0e4;">
                    <p style="margin:0;color:#6b8b83;font-size:11.5px;line-height:1.6;">
                        Butuh bantuan? Hubungi admin{{ $support !== '' ? ' di ' . $support : '' }}.
                    </p>
                </td>
            </tr>
        </table>
    </td></tr>
</table>
</body>
</html>