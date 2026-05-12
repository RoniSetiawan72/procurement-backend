<!DOCTYPE html>
<html>
<head>
    <title>Purchase Order</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <h2>Halo, {{ $po->vendor->name }}</h2>

    <p>Bersama email ini, kami dari Tim Procurement melampirkan dokumen <strong>Purchase Order (PO)</strong> resmi dengan detail sebagai berikut:</p>

    <ul>
        <li><strong>Nomor PO:</strong> {{ $po->po_number }}</li>
        <li><strong>Tanggal Dibuat:</strong> {{ $po->created_at->format('d M Y') }}</li>
        <li><strong>Total Nilai:</strong> Rp {{ number_format($po->actual_total_cost, 0, ',', '.') }}</li>
        <li><strong>Batas Maksimal Pengiriman:</strong> {{ \Carbon\Carbon::parse($po->expected_delivery_date)->format('d M Y') }}</li>
    </ul>

    <p>Mohon agar pesanan ini dapat diproses sesuai dengan dokumen PDF yang terlampir. Jika ada pertanyaan, jangan ragu untuk membalas email ini.</p>

    <p>Terima kasih atas kerja samanya.</p>

    <br>
    <p>Hormat Kami,</p>
    <p><strong>Tim Procurement</strong></p>
</body>
</html>
