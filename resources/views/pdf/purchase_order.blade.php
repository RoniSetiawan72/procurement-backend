<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $po->po_number }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #2c3e50; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .items-table th { background-color: #f4f6f8; }
        .total-row th { text-align: right; }
        .footer { text-align: center; font-size: 10px; margin-top: 50px; color: #7f8c8d; }
        .signature-box { float: right; width: 200px; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>PURCHASE ORDER (PO)</h1>
        <p>PT. Sistem Pengadaan Maju Jaya | Jl. Teknologi No. 123, Jakarta Raya</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <strong>Kepada Vendor:</strong><br>
                {{ $po->vendor->name }}<br>
                {{ $po->vendor->address }}<br>
                {{ $po->vendor->email }}
            </td>
            <td width="50%" style="text-align: right;">
                <strong>No. PO:</strong> {{ $po->po_number }}<br>
                <strong>Tanggal PO:</strong> {{ $po->created_at->format('d M Y') }}<br>
                <strong>Tgl. Pengiriman (Max):</strong> {{ \Carbon\Carbon::parse($po->expected_delivery_date)->format('d M Y') }}<br>
                <strong>Referensi PR:</strong> {{ $po->purchaseRequisition->pr_number ?? '-' }}
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Satuan</th>
                <th>Harga Satuan</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->uom }}</td>
                <td>Rp {{ number_format($item->actual_unit_price, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->quantity * $item->actual_unit_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="5">TOTAL KESELURUHAN</th>
                <th>Rp {{ number_format($po->actual_total_cost, 0, ',', '.') }}</th>
            </tr>
        </tfoot>
    </table>

    <p><strong>Catatan:</strong><br> {{ $po->notes ?? 'Tidak ada catatan tambahan.' }}</p>

    <div class="signature-box">
        <p>Hormat Kami,</p>
        <br><br><br>
        <p><strong>{{ $po->creator->name ?? 'Tim Procurement' }}</strong><br>Procurement Officer</p>
    </div>

    <div style="clear: both;"></div>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Procurement Terpadu.
    </div>

</body>
</html>
