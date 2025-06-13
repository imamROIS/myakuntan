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
            font-family: Arial, sans-serif;
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
        }

        td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .detail-section {
            margin-top: 20px;
        }

        .detail-item {
            margin-bottom: 8px;
            display: flex;
        }

        .detail-label {
            font-weight: bold;
            width: 150px;
            color: #2c3e50;
        }

        .footer {
            margin-top: 30px;
            font-size: 11px;
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">NAMA PERUSAHAAN ANDA</div>
            <div>Alamat Perusahaan, Kota, Kode Pos</div>
        </div>
        <h1>TRANSAKSI JURNAL</h1>
        <p>Akun: {{ $coa->coa_code }} - {{ $coa->coa_name }}</p>
    </div>

    <div class="detail-section">
        <div class="detail-item">
            <span class="detail-label">Nomor Jurnal:</span>
            <span>{{ $record->jh_nomor_jurnal }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Tanggal:</span>
            <span>{{ \Carbon\Carbon::parse($record->jh_tanggal)->format('d/m/Y') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Nomor Dokumen:</span>
            <span>{{ $record->jh_nomor_dokumen ?? '-' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Departemen:</span>
            <span>{{ $record->jh_departemen }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th width="15%" class="text-right">Debit (Rp)</th>
                <th width="15%" class="text-right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $record->jh_keterangan }}</td>
                <td class="text-right">{{ number_format($record->jh_dr, 2) }}</td>
                <td class="text-right">{{ number_format($record->jh_cr, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
