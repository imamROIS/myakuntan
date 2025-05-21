<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\ChartOfAccount;

class BalanceSheetReport extends Widget
{
    protected static string $view = 'filament.widgets.balance-sheet-report';
    protected int | string | array $columnSpan = 'full';

    public function getAktiva()
    {
        return ChartOfAccount::where('coa_category', 'like', 'Aktiva%')
            ->where('is_active', true)
            ->orderBy('coa_category')
            ->get();
    }

    public function getPasiva()
    {
        return ChartOfAccount::where(function ($query) {
                $query->where('coa_category', 'like', 'Kewajiban%')
                      ->orWhere('coa_category', 'Modal');
            })
            ->where('is_active', true)
            ->orderBy('coa_category')
            ->get();
    }

    public function getTotalAktiva()
    {
        return $this->getAktiva()->sum('current_balance');
    }

    public function getTotalPasiva()
    {
        return $this->getPasiva()->sum('current_balance');
    }
}
