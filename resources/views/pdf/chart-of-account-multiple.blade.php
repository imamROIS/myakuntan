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

    </style>
</head>
<body>
    <div class="header">
        <div class="company-info">
            <div class="company-name">NAMA PERUSAHAAN ANDA</div>
            <div>Alamat Perusahaan, Kota, Kode Pos</div>
        </div>
        <h1>DAFTAR CHART OF ACCOUNTS</h1>
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="15%">Kode Akun</th>
                <th width="25%">Nama Akun</th>
                <th width="15%">Jenis</th>
                <th width="15%">Kategori</th>
                <th width="10%">Saldo Normal</th>
                <th width="10%" class="text-right">Saldo</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $record)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $record->coa_code }}</td>
                <td>{{ $record->coa_name }}</td>
                <td>{{ $record->coa_type }}</td>
                <td>{{ $record->coa_category }}</td>
                <td>{{ $record->increase_on_debit ? 'Debit' : 'Kredit' }}</td>
                <td class="text-right">Rp {{ number_format($record->current_balance, 2) }}</td>
                <td class="text-center">{{ $record->is_active ? 'Aktif' : 'Non-Aktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Total Data: {{ count($records) }}</p>
    </div>
</body>
</html>
