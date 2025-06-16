<?php

namespace App\Filament\Widgets;

use App\Models\Jurnalharian;
use Filament\Widgets\Widget;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Actions\Action;
use Filament\Actions\StaticAction;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\IconPosition;

class CreateJurnalButton extends Widget
{
    protected static string $view = 'filament.widgets.create-jurnal-button';
protected static ?int $sort = 100;

    // Method yang akan dipanggil dari view
    public function createJurnal()
    {
        return Action::make('createJurnal')
            ->label('Buat Jurnal Baru')
            ->icon('heroicon-o-plus-circle')
            ->button()
            ->size('lg')
            ->color('primary')
            ->modalHeading('Buat Jurnal Harian Baru')
            ->modalSubmitActionLabel('Simpan Jurnal')
            ->modalCancelActionLabel('Batal')
            
            ->action(function (array $data) {
                // Validasi balance debit-kredit
                if (abs($data['jh_dr'] - $data['jh_cr']) > 0.01) {
                    Notification::make()
                        ->title('Error: Debit dan Kredit tidak balance!')
                        ->danger()
                        ->send();
                    return;
                }
                
                // Simpan data
                Jurnalharian::create($data);
                
                // Notifikasi sukses
                Notification::make()
                    ->title('Jurnal berhasil dibuat!')
                    ->success()
                    ->send();
            });
    }
}
