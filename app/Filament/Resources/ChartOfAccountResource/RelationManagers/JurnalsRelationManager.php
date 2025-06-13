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
            
use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;


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
                 // Tambahkan action export PDF
            // Export single transaction
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function ($record) {
                    $data = [
                        'record' => $record,
                        'coa' => $this->getOwnerRecord(),
                        'title' => 'Transaksi Jurnal - ' . $record->jh_nomor_jurnal,
                    ];
                    
                    $pdf = Pdf::loadHTML(
                        Blade::render('pdf.jurnal-coa', $data)
                    );
                    
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "jurnal-{$record->jh_nomor_jurnal}.pdf"
                    );
                }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->url(fn ($livewire) => \App\Filament\Resources\JurnalharianResource::getUrl('create', [
                        'jh_code_account' => $livewire->ownerRecord->coa_code
                    ])),

                   // Export all transactions
            Action::make('exportAllPdf')
                ->label('Export Semua')
                ->icon('heroicon-o-document-arrow-down')
                ->color('primary')
                ->action(function () {
                    $coa = $this->getOwnerRecord();
                    $records = $coa->jurnals()->get(); // Menggunakan relasi langsung
                    
                    $data = [
                        'records' => $records,
                        'coa' => $coa,
                        'title' => 'Semua Transaksi Akun - ' . $coa->coa_code,
                    ];
                    
                    $pdf = Pdf::loadHTML(
                        Blade::render('pdf.jurnal-coa-multiple', $data)
                    );
                    
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "transaksi-akun-{$coa->coa_code}.pdf"
                    );
                }),
            ])
        //     ->bulkActions([
        //     // Export selected transactions
        //     Action::make('exportSelectedPdf')
        //         ->label('Export Selected')
        //         ->icon('heroicon-o-document-arrow-down')
        //         ->color('primary')
        //         ->action(function ($records) {
        //             $coa = $this->getOwnerRecord();
                    
        //             $data = [
        //                 'records' => $records,
        //                 'coa' => $coa,
        //                 'title' => 'Transaksi Terpilih Akun - ' . $coa->coa_code,
        //             ];
                    
        //             $pdf = Pdf::loadHTML(
        //                 Blade::render('pdf.jurnal-coa-multiple', $data)
        //             );
                    
        //             return response()->streamDownload(
        //                 fn () => print($pdf->output()),
        //                 "transaksi-terpilih-akun-{$coa->coa_code}.pdf"
        //             );
        //         }),
        // ])
            ->defaultSort('jh_tanggal', 'desc');
    }
}