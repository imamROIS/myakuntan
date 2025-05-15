<?php

namespace App\Filament\Resources\JurnalharianResource\Pages;

use App\Filament\Resources\JurnalharianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJurnalharians extends ListRecords
{
    protected static string $resource = JurnalharianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
