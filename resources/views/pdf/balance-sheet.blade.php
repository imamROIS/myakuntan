<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
        }

        .report-title {
            font-size: 12pt;
            margin: 8px 0;
            font-weight: bold;
        }

        .period {
            font-size: 10pt;
        }

        .balance-sheet-container {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .column {
            width: 48%;
        }

        .section-title {
            font-size: 11pt;
            font-weight: bold;
            background-color: #f5f5f5;
            padding: 5px 8px;
            margin-bottom: 8px;
            border-bottom: 1px solid #ddd;
        }

        .category-title {
            font-weight: bold;
            margin: 10px 0 5px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #eee;
            font-size: 10pt;
        }

        .account-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 9.5pt;
        }

        .account-name {
            flex: 2;
            padding-left: 5px;
        }

        .account-balance {
            flex: 1;
            text-align: right;
            padding-right: 5px;
        }

        .sub-detail {
            font-size: 9pt;
            color: #555;
            margin-left: 10px;
            margin-bottom: 2px;
        }

        .category-total {
            font-weight: bold;
            margin-top: 5px;
            padding-top: 3px;
            border-top: 1px dashed #ddd;
            font-size: 10pt;
        }

        .summary {
            margin-top: 20px;
            border-top: 1px solid #333;
            padding-top: 10px;
        }

        .summary-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 11pt;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 10pt;
        }

        .summary-label {
            font-weight: bold;
        }

        .balanced {
            color: #27ae60;
        }

        .unbalanced {
            color: #e74c3c;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
            font-size: 8pt;
            color: #666;
        }

        .date-printed {
            text-align: right;
            font-size: 8pt;
            margin-bottom: 10px;
            color: #666;
        }

        .total-section {
            font-weight: bold;
            margin-top: 10px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }

    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">{{ config('app.name') }}</div>
        <div class="report-title">LAPORAN NERACA</div>
        <div class="period">
            Periode: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d M Y') : 'Awal' }}
            - {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d M Y') : 'Sekarang' }}
        </div>
    </div>

    <div class="date-printed">
        Dicetak pada: {{ now()->format('d M Y H:i') }}
    </div>

    <div class="balance-sheet-container">
        <!-- Aktiva Column -->
        <div class="column">
            <div class="section-title">AKTIVA</div>

            @foreach($aktiva as $category => $group)
            <div class="category">
                <div class="category-title">{{ $category }}</div>

                @foreach($group as $akun)
                <div class="account-item">
                    <div class="account-name">{{ $akun->coa_name }}</div>
                    <div class="account-balance">Rp {{ number_format($akun->current_balance, 0, ',', '.') }}</div>
                </div>

                @if($showDetails)
                <div class="sub-detail">
                    <div class="account-item">
                        <div class="account-name">↳ Total Debit</div>
                        <div class="account-balance">Rp {{ number_format($akun->total_debit, 0, ',', '.') }}</div>
                    </div>
                    <div class="account-item">
                        <div class="account-name">↳ Total Kredit</div>
                        <div class="account-balance">Rp {{ number_format($akun->total_credit, 0, ',', '.') }}</div>
                    </div>
                </div>
                @endif
                @endforeach

                <div class="account-item category-total">
                    <div class="account-name">Total {{ $category }}</div>
                    <div class="account-balance">Rp {{ number_format($getTotalByCategory($group), 0, ',', '.') }}</div>
                </div>
            </div>
            @endforeach

            <div class="account-item total-section">
                <div class="account-name">TOTAL AKTIVA</div>
                <div class="account-balance">Rp {{ number_format($totalAktiva, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Pasiva Column -->
        <div class="column">
            <div class="section-title">PASIVA</div>

            @foreach($pasiva as $category => $group)
            <div class="category">
                <div class="category-title">{{ $category }}</div>

                @foreach($group as $akun)
                <div class="account-item">
                    <div class="account-name">{{ $akun->coa_name }}</div>
                    <div class="account-balance">Rp {{ number_format($akun->current_balance, 0, ',', '.') }}</div>
                </div>

                @if($showDetails)
                <div class="sub-detail">
                    <div class="account-item">
                        <div class="account-name">↳ Total Debit</div>
                        <div class="account-balance">Rp {{ number_format($akun->total_debit, 0, ',', '.') }}</div>
                    </div>
                    <div class="account-item">
                        <div class="account-name">↳ Total Kredit</div>
                        <div class="account-balance">Rp {{ number_format($akun->total_credit, 0, ',', '.') }}</div>
                    </div>
                </div>
                @endif
                @endforeach

                <div class="account-item category-total">
                    <div class="account-name">Total {{ $category }}</div>
                    <div class="account-balance">Rp {{ number_format($getTotalByCategory($group), 0, ',', '.') }}</div>
                </div>
            </div>
            @endforeach

            <div class="account-item total-section">
                <div class="account-name">TOTAL PASIVA</div>
                <div class="account-balance">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="summary">
        <div class="summary-title">RINGKASAN NERACA</div>

        <div class="summary-item">
            <div class="summary-label">Total Aktiva:</div>
            <div>Rp {{ number_format($totalAktiva, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Total Pasiva:</div>
            <div>Rp {{ number_format($totalPasiva, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Status Neraca:</div>
            <div class="{{ $isBalanced ? 'balanced' : 'unbalanced' }}">
                {{ $isBalanced ? 'SEIMBANG' : 'TIDAK SEIMBANG' }}
            </div>
        </div>
        @unless($isBalanced)
        <div class="summary-item">
            <div class="summary-label">Selisih:</div>
            <div>Rp {{ number_format($balanceDifference, 0, ',', '.') }}</div>
        </div>
        @endunless

        <div style="margin-top: 10px;">
            <div class="summary-item">
                <div class="summary-label">Total Debit:</div>
                <div>Rp {{ number_format($totalDebit, 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Kredit:</div>
                <div>Rp {{ number_format($totalCredit, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        Laporan ini dihasilkan secara otomatis oleh Sistem Akuntansi {{ config('app.name') }}
    </div>
</body>
</html>
