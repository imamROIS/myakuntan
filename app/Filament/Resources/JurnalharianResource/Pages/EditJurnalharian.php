<?php

namespace App\Filament\Resources\JurnalharianResource\Pages;

use App\Filament\Resources\JurnalharianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJurnalharian extends EditRecord
{
    protected static string $resource = JurnalharianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
