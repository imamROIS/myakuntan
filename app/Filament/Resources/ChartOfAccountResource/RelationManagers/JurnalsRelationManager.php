<?php

namespace App\Filament\Resources\ChartOfAccountResource\RelationManagers;
//app/Filament/Resources/ChartOfAccountResource/RelationManagers

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Jurnalharian;
use App\Models\ChartOfAccount;
use App\Filament\Resources\JurnalharianResource;
            //app/Filament/Resources/JurnalharianResource.php        


class JurnalsRelationManager extends RelationManager
{
    protected static string $relationship = 'jurnals';

    protected static ?string $title = 'Transaksi Jurnal';

    protected static ?string $modelLabel = 'Jurnal';

    protected static ?string $pluralModelLabel = 'Daftar Jurnal';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form fields bisa ditambahkan jika diperlukan
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('jh_nomor_jurnal')
            ->columns([
                Tables\Columns\TextColumn::make('jh_tanggal')
                    ->label('TANGGAL')
                    ->date('d/m/Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('jh_nomor_jurnal')
                    ->label('NO. JURNAL')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('jh_nomor_dokumen')
                    ->label('NO. DOKUMEN')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('jh_departemen')
                    ->label('DEPARTEMEN'),
                    
                Tables\Columns\TextColumn::make('jh_dr')
                    ->label('DEBIT')
                    ->numeric(decimalPlaces: 2)
                    ->money('IDR')
                    ->alignRight(),
                    
                Tables\Columns\TextColumn::make('jh_cr')
                    ->label('KREDIT')
                    ->numeric(decimalPlaces: 2)
                    ->money('IDR')
                    ->alignRight(),
                    
                Tables\Columns\TextColumn::make('jh_pemohon')
                    ->label('PEMOHON'),
                    
                Tables\Columns\TextColumn::make('jh_keterangan')
                    ->label('KETERANGAN')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bulan')
                    ->label('Filter Bulan')
                    ->options([
                        '01' => 'Januari',
                        '02' => 'Februari',
                        // ... bulan lainnya
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereMonth('jh_tanggal', $data['value']);
                        }
                    }),
                    
                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Filter Tahun')
                    ->options(function () {
                        return \App\Models\Jurnalharian::getAvailableYears()->toArray();
                    })
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereYear('jh_tanggal', $data['value']);
                        }
                    }),
            ])
            ->groups([
                Tables\Grouping\Group::make('jh_tanggal')
                    ->label('Per Tanggal')
                    ->collapsible(),
            ])
            ->defaultGroup('jh_tanggal')
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\ViewAction::make()
                //     ->url(fn ($record) => \App\Filament\Resources\JurnalharianResource::getUrl('view', ['record' => $record])),
                    
                // Tables\Actions\EditAction::make()
                //     ->url(fn ($record) => \App\Filament\Resources\JurnalharianResource::getUrl('edit', ['record' => $record])),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(fn ($livewire) => \App\Filament\Resources\JurnalharianResource::getUrl('create', [
                        'jh_code_account' => $livewire->ownerRecord->coa_code
                    ])),
            ])
            ->defaultSort('jh_tanggal', 'desc');
    }
}