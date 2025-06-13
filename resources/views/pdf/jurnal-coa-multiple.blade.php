<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        @page {
            margin: 1cm;
            size: A4 landscape;
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
            font-size: 11px;
        }

        th {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            text-align: left;
        }

        td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 20px;
            font-size: 11px;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .total-row {
            font-weight: bold;
            background-color: #e9ecef;
        }

    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">NAMA PERUSAHAAN ANDA</div>
            <div>Alamat Perusahaan, Kota, Kode Pos</div>
        </div>
        <h1>TRANSAKSI AKUN: {{ $coa->coa_code }} - {{ $coa->coa_name }}</h1>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th width="15%">No. Jurnal</th>
                <th width="15%">No. Dokumen</th>
                <th width="20%">Departemen</th>
                <th width="25%">Keterangan</th>
                <th width="10%" class="text-right">Debit (Rp)</th>
                <th width="10%" class="text-right">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($record->jh_tanggal)->format('d/m/Y') }}</td>
                <td>{{ $record->jh_nomor_jurnal }}</td>
                <td>{{ $record->jh_nomor_dokumen ?? '-' }}</td>
                <td>{{ $record->jh_departemen }}</td>
                <td>{{ Str::limit($record->jh_keterangan, 50) }}</td>
                <td class="text-right">{{ number_format($record->jh_dr, 2) }}</td>
                <td class="text-right">{{ number_format($record->jh_cr, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="6" class="text-right">TOTAL</td>
                <td class="text-right">{{ number_format($records->sum('jh_dr'), 2) }}</td>
                <td class="text-right">{{ number_format($records->sum('jh_cr'), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Total Data: {{ count($records) }} | Saldo Akhir: Rp {{ number_format($coa->current_balance, 2) }}</p>
    </div>
</body>
</html>
