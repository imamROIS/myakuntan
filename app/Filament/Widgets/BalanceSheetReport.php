<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\ChartOfAccount;
use App\Models\Jurnalharian;
use Illuminate\Support\Collection;
use Filament\Forms;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Components\Actions\Action;

class BalanceSheetReport extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.widgets.balance-sheet-report';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 2;

    public ?string $selectedYear = null;
    public ?string $startDate = null;
    public ?string $endDate = null;
    public bool $showDetails = false;

    public function mount(): void
    {
        $this->form->fill([
            'selectedYear' => now()->year,
            'startDate' => null,
            'endDate' => now()->toDateString(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('selectedYear')
                    ->label('Tahun')
                    ->options(Jurnalharian::getAvailableYears())
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidget')),
                
                Forms\Components\DatePicker::make('startDate')
                    ->label('Dari Tanggal')
                    ->maxDate(now())
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidget')),
                
                Forms\Components\DatePicker::make('endDate')
                    ->label('Sampai Tanggal')
                    ->maxDate(now())
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidget')),
            ]),
            Forms\Components\Toggle::make('showDetails')
                ->label('Tampilkan Detail Debit/Kredit')
                ->reactive()
                ->afterStateUpdated(fn () => $this->dispatch('refreshWidget')),
            Forms\Components\Actions::make([
                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->color('success')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action('exportToPdf')
            ])
        ];
    }

    public function getFilteredChartOfAccounts(): Collection
    {
        $startDate = $this->startDate ? Carbon::parse($this->startDate) : null;
        $endDate = $this->endDate ? Carbon::parse($this->endDate) : null;

        $coa = ChartOfAccount::where('is_active', true)->get();

        foreach ($coa as $akun) {
            $query = $akun->jurnals();
            
            if ($startDate) {
                $query->whereDate('jh_tanggal', '>=', $startDate);
            }
            
            if ($endDate) {
                $query->whereDate('jh_tanggal', '<=', $endDate);
            }

            $total = $query->selectRaw('SUM(jh_dr) as dr, SUM(jh_cr) as cr')
                          ->first();

            $akun->total_debit = $total->dr ?? 0;
            $akun->total_credit = $total->cr ?? 0;
            
            $akun->current_balance = $akun->increase_on_debit
                ? $akun->opening_balance + $akun->total_debit - $akun->total_credit
                : $akun->opening_balance + $akun->total_credit - $akun->total_debit;
        }

        return $coa->groupBy('coa_category');
    }

    public function getTotalByCategory($group): float
    {
        return $group->sum('current_balance');
    }

    public function getTotalAktiva(): float
    {
        return $this->getFilteredChartOfAccounts()
            ->filter(fn ($_, $key) => str_contains(strtolower($key), 'aktiva'))
            ->flatten()
            ->sum('current_balance');
    }

    public function getTotalPasiva(): float
    {
        return $this->getFilteredChartOfAccounts()
            ->filter(fn ($_, $key) => str_contains(strtolower($key), 'pasiva') || 
                     str_contains(strtolower($key), 'kewajiban') || 
                     str_contains(strtolower($key), 'modal'))
            ->flatten()
            ->sum('current_balance');
    }

    public function isBalanced(): bool
    {
        return round($this->getTotalAktiva(), 2) === round($this->getTotalPasiva(), 2);
    }

    public function getBalanceDifference(): float
    {
        return round(abs($this->getTotalAktiva() - $this->getTotalPasiva()), 2);
    }

    public function getTotalDebit(): float
    {
        $query = Jurnalharian::query();
        
        if ($this->startDate) {
            $query->whereDate('jh_tanggal', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->whereDate('jh_tanggal', '<=', $this->endDate);
        }
        
        return $query->sum('jh_dr');
    }

    public function getTotalCredit(): float
    {
        $query = Jurnalharian::query();
        
        if ($this->startDate) {
            $query->whereDate('jh_tanggal', '>=', $this->startDate);
        }
        
        if ($this->endDate) {
            $query->whereDate('jh_tanggal', '<=', $this->endDate);
        }
        
        return $query->sum('jh_cr');
    }

     public function exportToPdf()
    {
        $data = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'showDetails' => $this->showDetails,
            'aktiva' => $this->getFilteredChartOfAccounts()->filter(fn ($_, $key) => str_contains(strtolower($key), 'aktiva')),
            'pasiva' => $this->getFilteredChartOfAccounts()->filter(fn ($_, $key) => in_array(strtolower($key), ['pasiva', 'kewajiban', 'modal'])),
            'totalAktiva' => $this->getTotalAktiva(),
            'totalPasiva' => $this->getTotalPasiva(),
            'isBalanced' => $this->isBalanced(),
            'balanceDifference' => $this->getBalanceDifference(),
            'totalDebit' => $this->getTotalDebit(),
            'totalCredit' => $this->getTotalCredit(),
            'getTotalByCategory' => fn($group) => $this->getTotalByCategory($group),
        ];

        $pdf = Pdf::loadHTML(
            Blade::render('pdf.balance-sheet', $data)
        )->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            "balance-sheet-{$this->startDate}-to-{$this->endDate}.pdf"
        );
    }
}
