<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\ChartOfAccount;
use App\Models\Jurnalharian;
use Illuminate\Support\Collection;
use Filament\Forms;
use Carbon\Carbon;


class BalanceSheetReport extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static string $view = 'filament.widgets.balance-sheet-report';
    protected int | string | array $columnSpan = 'full';

    public ?string $selectedYear = null;
    public ?string $selectedDate = null;

    public function mount(): void
    {
        $this->form->fill([
            'selectedYear' => now()->year,
            'selectedDate' => now()->toDateString(),
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('selectedYear')
                    ->label('Pilih Tahun')
                    ->options(Jurnalharian::getAvailableYears())
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidget')),

                Forms\Components\DatePicker::make('selectedDate')
                    ->label('Per Tanggal')
                    ->maxDate(now())
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->dispatch('refreshWidget')),
            ]),
        ];
    }

    public function getFilteredChartOfAccounts(): Collection
    {
        $tanggal = $this->selectedDate ?? now()->toDateString();

        $coa = ChartOfAccount::where('is_active', true)->get();

        foreach ($coa as $akun) {
            $total = $akun->jurnals()
                ->whereDate('jh_tanggal', '<=', $tanggal)
                ->selectRaw('SUM(jh_dr) as dr, SUM(jh_cr) as cr')
                ->first();

            $dr = $total->dr ?? 0;
            $cr = $total->cr ?? 0;

            $akun->current_balance = $akun->increase_on_debit
                ? $akun->opening_balance + $dr - $cr
                : $akun->opening_balance + $cr - $dr;
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
            ->filter(fn ($_, $key) => str_contains(strtolower($key), 'pasiva') || str_contains(strtolower($key), 'kewajiban') || str_contains(strtolower($key), 'modal'))
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
    return Jurnalharian::whereYear('jh_tanggal', $this->selectedYear)
        ->sum('jh_dr');
}

public function getTotalCredit(): float
{
    return Jurnalharian::whereYear('jh_tanggal', $this->selectedYear)
        ->sum('jh_cr');
}




}
