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
        }

        th {
            background-color: #2c3e50;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
            vertical-align: top;
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

        .detail-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .detail-item {
            margin-bottom: 8px;
            display: flex;
        }

        .detail-label {
            font-weight: bold;
            width: 120px;
            color: #2c3e50;
        }

        .logo {
            height: 60px;
            margin-bottom: 10px;
        }

        /* Tanda Tangan */
        .signature-container {
            width: 100%;
            margin: 40px auto 20px;
            max-width: 600px;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .signature-table th {
            text-align: left;
            padding: 8px;
            background-color: #2c3e50;
            border: 1px solid #ddd;
            font-weight: bold;
        }

        .signature-table td {
            padding: 5px;
            border: 1px solid #ddd;
            vertical-align: bottom;
            text-align: left;
        }

        .signature-line {
            border-top: 1px solid #ffffff;
            margin: 0 auto 3px;
            width: 10%;
        }

        .signature-detail {
            margin-bottom: 8px;
            text-align: left;
            padding-left: 20%;
            font-size: 8;
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
        <p>Nomor: {{ $record->jh_nomor_jurnal }} | Tanggal: {{ \Carbon\Carbon::parse($record->jh_tanggal)->format('d/m/Y') }}</p>
    </div>

    <!-- Tabel data -->
    <table>
        <thead>
            <tr>
                <th width="40%">Account</th>
                <th width="30%">Departemen</th>
                <th width="15%" class="text-right">Debit (Rp)</th>
                <th width="15%" class="text-right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $record->jh_code_account }} - {{ $record->jh_nama_account }}</td>
                <td>{{ $record->jh_departemen }}</td>
                <td class="text-right">{{ number_format($record->jh_dr, 2) }}</td>
                <td class="text-right">{{ number_format($record->jh_cr, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Detail transaksi -->
    <div class="detail-section">
        <div class="detail-item">
            <span class="detail-label">Nomor Dokumen:</span>
            <span>{{ $record->jh_nomor_dokumen ?? '-' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Pemohon:</span>
            <span>{{ $record->jh_pemohon }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Keterangan:</span>
            <span>{{ $record->jh_keterangan }}</span>
        </div>
    </div>

    <!-- Bagian Tanda Tangan -->
    <div class="signature-container">
        <table class="signature-table">
            <thead>
                <tr>
                    <th width="50%">Disetujui Oleh</th>
                    <th width="50%">Dibuat Oleh</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <br>
                        <br>
                        <div class="signature-line"></div>
                        <div class="signature-detail">Nama: ___________________</div>
                        <div class="signature-detail">Jabatan: _________________</div>
                        <div class="signature-detail">Tanggal: {{ now()->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div class="signature-line"></div>
                        <div class="signature-detail">Nama: {{ $record->jh_pemohon ?? '___________________' }}</div>
                        <div class="signature-detail">Jabatan: _________________</div>
                        <div class="signature-detail">Tanggal: {{ now()->format('d/m/Y') }}</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
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
