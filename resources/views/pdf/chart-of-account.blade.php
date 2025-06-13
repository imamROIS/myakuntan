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
        <h1>CHART OF ACCOUNT</h1>
    </div>

    <div class="detail-section">
        <div class="detail-item">
            <span class="detail-label">Kode Akun:</span>
            <span>{{ $record->coa_code }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Nama Akun:</span>
            <span>{{ $record->coa_name }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Jenis Akun:</span>
            <span>{{ $record->coa_type }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Kategori:</span>
            <span>{{ $record->coa_category }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Saldo Normal:</span>
            <span>{{ $record->increase_on_debit ? 'Debit' : 'Kredit' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Saldo Saat Ini:</span>
            <span class="text-right">Rp {{ number_format($record->current_balance, 2) }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Status:</span>
            <span>{{ $record->is_active ? 'Aktif' : 'Non-Aktif' }}</span>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <div class="detail-label">Deskripsi:</div>
        <div>{{ $record->description ?? '-' }}</div>
    </div>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</body>
</html>
