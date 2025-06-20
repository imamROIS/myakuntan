<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Jurnalharian;
use App\Models\ChartOfAccount;
use Carbon\Carbon; // <-- Tambahkan ini
use Illuminate\Support\Collection;

class CashFlowWidget extends Widget
{
    protected static string $view = 'filament.widgets.cash-flow-widget';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    // Filter properties
    // Properti untuk menentukan periode laporan
    public $period = 'monthly'; // Default: bulanan
    public $month; // Bulan yang dipilih
    public $year; // Tahun yang dipilih
    public $startDate; // Tanggal mulai (untuk periode custom)
    public $endDate; // Tanggal akhir (untuk periode custom)

    public function mount(): void
{
     // Set default bulan dan tahun ke bulan dan tahun sekarang
    $this->month = (int)Carbon::now()->format('m'); // Konversi ke integer
    $this->year = (int)Carbon::now()->format('Y'); // Konversi ke integer
    $this->endDate = Carbon::now()->format('Y-m-d');
}

    public function updatedPeriod($value): void
    {
         // Reset filter tanggal jika beralih ke mode bulanan
        if ($value === 'monthly') {
            $this->startDate = null;
            $this->endDate = null;
        } 
        // Reset filter bulan/tahun jika beralih ke mode custom
        elseif ($value === 'custom') {
            $this->month = null;
            $this->year = null;
        }
    }

    /**
     * Get cash flow from operating activities
     */
    public function getOperatingActivities(): Collection
{
    // Pastikan kode akun kas/bank benar
    // Mencari akun kas/bank yang akan digunakan
    $cashAccounts = ChartOfAccount::where('coa_type', 'KAS/BANK')
        ->orWhere('coa_name', 'like', '%kas%')
        ->orWhere('coa_name', 'like', '%bank%')
        ->pluck('coa_code');

    if ($cashAccounts->isEmpty()) {
        logger()->error('Tidak ada akun KAS/BANK yang terdaftar');
        return collect();
    }

    // Query transaksi jurnal harian yang terkait dengan:
    // 1. Akun kas/bank
    // 2. Termasuk kategori PENDAPATAN, BEBAN, atau mengandung kata 'operasional'
    // 3. Tambahkan ->orWhere untuk kategori lainya yang ingin ditambahkan
        $query = Jurnalharian::whereIn('jh_code_account', $cashAccounts)
        ->whereHas('coa', function($q) {
            $q->where('coa_category', 'AKTIVA')
              ->orWhere('coa_category', 'PASIVA')
              ->orWhere('coa_name', 'like', '%operasional%');
        });

    $this->applyDateFilters($query);

    logger()->debug('Operating Activities Query:', [
        'sql' => $query->toSql(),
        'bindings' => $query->getBindings(),
        'count' => $query->count()
    ]);

    return $query->get()
        ->groupBy('jh_code_account')
        ->map(function ($transactions) {
            return [
                'account_name' => $transactions->first()->coa->coa_name,
                'cash_in' => $transactions->sum('jh_dr'),
                'cash_out' => $transactions->sum('jh_cr'),
                'net_cash' => $transactions->sum('jh_dr') - $transactions->sum('jh_cr')
            ];
        });
}

    public function getAvailableYears(): array
{
    return Jurnalharian::query()
        ->selectRaw('YEAR(jh_tanggal) as year')
        ->groupBy('year')
        ->pluck('year', 'year')
        ->toArray();
}

    /**
     * Get cash flow from investing activities
     */
    public function getInvestingActivities(): Collection
    {
        $cashAccounts = ChartOfAccount::where('coa_type', 'KAS/BANK')->pluck('coa_code');
        
        $query = Jurnalharian::whereIn('jh_code_account', $cashAccounts)
            ->whereHas('coa', function($q) {
                $q->where('coa_category', 'ASET')
                ->orWhere('coa_category', 'INVESTASI');
            });

        $this->applyDateFilters($query);

        return $query->get()
            ->groupBy('jh_code_account')
            ->map(function ($transactions) {
                return [
                    'account_name' => $transactions->first()->coa->coa_name,
                    'cash_in' => $transactions->sum('jh_dr'),  // Penjualan aset
                    'cash_out' => $transactions->sum('jh_cr'), // Pembelian aset
                    'net_cash' => $transactions->sum('jh_dr') - $transactions->sum('jh_cr')
                ];
            });
    }

    /**
     * Get cash flow from financing activities
     */
    public function getFinancingActivities(): Collection
    {
        $cashAccounts = ChartOfAccount::where('coa_category', 'PASIVA')->pluck('coa_code');
        
        $query = Jurnalharian::whereIn('jh_code_account', $cashAccounts)
            ->whereHas('coa', function($q) {
                $q->where('coa_type', 'MODAL')
                  ->orWhere('coa_type', 'HUTANG')
                  ->orWhere('coa_type', 'PIUTANG');
            });

        $this->applyDateFilters($query);

        return $query->get()
            ->groupBy('jh_code_account')
            ->map(function ($transactions) {
                return [
                    'account_name' => $transactions->first()->coa->coa_name,
                    'cash_in' => $transactions->sum('jh_dr'),  // Pinjaman/modal masuk
                    'cash_out' => $transactions->sum('jh_cr'), // Pembayaran hutang/prive
                    'net_cash' => $transactions->sum('jh_dr') - $transactions->sum('jh_cr')
                ];
            });
    }

    protected function applyDateFilters($query): void
    {
        match ($this->period) {
            'monthly' => $query
                ->whereMonth('jh_tanggal', $this->month)
                ->whereYear('jh_tanggal', $this->year),
            'yearly' => $query->whereYear('jh_tanggal', $this->year),
            'custom' => $query
                ->whereDate('jh_tanggal', '>=', $this->startDate)
                ->whereDate('jh_tanggal', '<=', $this->endDate),
        };
    }

   public function getPeriodLabel(): string
{
    return match ($this->period) {
        'monthly' => Carbon::create()
            ->year((int)$this->year)  // Pastikan integer
            ->month((int)$this->month ?? 1) // Pastikan integer
            ->translatedFormat('F Y'),
        'yearly' => "Tahun {$this->year}",
        'custom' => Carbon::parse($this->startDate)->format('d M Y') . 
            ' - ' . 
            Carbon::parse($this->endDate)->format('d M Y'),
    };
}

    public function getNetCashFlow(): float
    {
        $operating = $this->getOperatingActivities()->sum('net_cash');
        $investing = $this->getInvestingActivities()->sum('net_cash');
        $financing = $this->getFinancingActivities()->sum('net_cash');
        
        return $operating + $investing + $financing;
    }

    public function getBeginningBalance(): float
{
    return ChartOfAccount::where('coa_type', 'KAS/BANK')
        ->orWhere('coa_name', 'like', '%kas%')
        ->orWhere('coa_name', 'like', '%bank%')
        ->sum('current_balance') ?? 0;
}

    public function getEndingBalance(): float
    {
        return $this->getBeginningBalance() + $this->getNetCashFlow();
    }
}
