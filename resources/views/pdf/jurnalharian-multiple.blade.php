<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 portrait;
        }

        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #2c3e50;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #2c3e50;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            font-size: 12px;
            color: #7f8c8d;
        }

        .company-info {
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }

        .company-name {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
            page-break-inside: auto;
        }

        th {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-size: 11px;
            color: #7f8c8d;
            text-align: center;
        }

        .summary {
            margin-top: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 13px;
        }

        .total-row {
            font-weight: bold;
            background-color: #e9ecef !important;
        }

        .logo {
            height: 60px;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>
    <!-- Header dengan logo perusahaan -->
    <div class="header">
        <!-- Jika punya logo, tambahkan ini -->
        <!-- <img src="{{ public_path('images/logo.png') }}" class="logo" alt="Company Logo"> -->

        <div class="company-info">
            <div class="company-name">NAMA PERUSAHAAN ANDA</div>
            <div>Alamat Perusahaan, Kota, Kode Pos</div>
            <div>Telp: (021) 12345678 | Email: info@perusahaan.com</div>
        </div>

        <h1>LAPORAN JURNAL HARIAN</h1>
        <p>Periode: {{ request()->input('dari_tanggal') ? \Carbon\Carbon::parse(request()->input('dari_tanggal'))->format('d/m/Y') : 'Semua' }}
            - {{ request()->input('sampai_tanggal') ? \Carbon\Carbon::parse(request()->input('sampai_tanggal'))->format('d/m/Y') : 'Tanggal' }}</p>
    </div>

    <!-- Tabel data -->
    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="12%">Tanggal</th>
                <th width="15%">Nomor Jurnal</th>
                <th width="20%">Account</th>
                <th width="18%">Departemen</th>
                <th width="15%" class="text-right">Debit (Rp)</th>
                <th width="15%" class="text-right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($record->jh_tanggal)->format('d/m/Y') }}</td>
                <td>{{ $record->jh_nomor_jurnal }}</td>
                <td>{{ $record->jh_code_account }} - {{ $record->jh_nama_account }}</td>
                <td>{{ $record->jh_departemen }}</td>
                <td class="text-right">{{ number_format($record->jh_dr, 2) }}</td>
                <td class="text-right">{{ number_format($record->jh_cr, 2) }}</td>
            </tr>
            @endforeach

            <!-- Baris total -->
            <tr class="total-row">
                <td colspan="5" class="text-right"><strong>TOTAL</strong></td>
                <td class="text-right"><strong>{{ number_format($records->sum('jh_dr'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format($records->sum('jh_cr'), 2) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Summary section -->
    <div class="summary">
        <div><strong>Total Transaksi:</strong> {{ count($records) }} entri</div>
        <div><strong>Total Debit:</strong> Rp {{ number_format($records->sum('jh_dr'), 2) }}</div>
        <div><strong>Total Kredit:</strong> Rp {{ number_format($records->sum('jh_cr'), 2) }}</div>
        @if(abs($records->sum('jh_dr') - $records->sum('jh_cr')) > 0.01)
        <div style="color: #e74c3c;"><strong>Catatan:</strong> Total debit dan kredit tidak balance!</div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name ?? 'System' }}</p>
        <p>Halaman <span class="page-number"></span> dari <span class="page-count"></span></p>
    </div>

    <!-- Script untuk nomor halaman -->
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 20;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>
