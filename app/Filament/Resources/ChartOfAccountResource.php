<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChartOfAccountResource\Pages;
use App\Filament\Resources\ChartOfAccountResource\RelationManagers;

use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


use Filament\Tables\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini untuk PDF generation
use Illuminate\Support\Facades\Blade; // Tambahkan ini untuk rendering blade


class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $modelLabel = 'Chart of Account';

    protected static ?string $navigationLabel = 'Chart of Accounts';

    protected static ?string $navigationGroup = 'Akuntansi';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Informasi Akun')
                ->schema([
                    Forms\Components\TextInput::make('coa_code')
                        ->label('KODE AKUN')
                        ->datalist(ChartOfAccount::pluck('coa_code')->toArray())
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(20)
                        ->columnSpan(1),
                        
                    Forms\Components\TextInput::make('coa_name')
                        ->label('NAMA AKUN')
                        ->datalist(ChartOfAccount::pluck('coa_name')->toArray())
                        ->required()
                        ->maxLength(100)
                        ->columnSpan(2),
                        
                    Forms\Components\TextInput::make('coa_type')
                        ->label('JENIS AKUN')
                        ->datalist(ChartOfAccount::pluck('coa_type')->toArray())
                        ->required()
                        ->maxLength(50)
                        ->columnSpan(1),
                        
                        Forms\Components\Select::make('coa_category')
                        ->label('KATEGORI AKUN')
                        ->options([
                            'AKTIVA' => 'AKTIVA',
                            'PASIVA' => 'PASIVA',  
                        ])
                        ->required()
                        ->columnSpan(1),
                        
                    Forms\Components\Toggle::make('increase_on_debit')
                        ->label('SALDO NORMAL DEBIT?')
                        ->default(true)
                        ->helperText('Centang jika akun ini bertambah nilainya di debit (seperti aset/beban)')
                        ->columnSpan(1),
                    
                    Forms\Components\TextInput::make('coa_debit')
                        ->label('DEBIT')
                        ->numeric()
                        ->inputMode('decimal')
                        ->prefix('Rp ')
                        ->default(0)
                        ->disabled()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('coa_credit')
                        ->label('CREDIT')
                        ->numeric()
                        ->inputMode('decimal')
                        ->prefix('Rp ')
                        ->default(0)
                        ->disabled()
                        ->columnSpan(1),
                    
                        
                    Forms\Components\TextInput::make('opening_balance')
                        ->label('SALDO AWAL')
                        ->numeric()
                        ->inputMode('decimal')
                        ->prefix('Rp ')
                        ->default(0)
                        ->columnSpan(1),
                        
                    Forms\Components\TextInput::make('current_balance')
                        ->label('SALDO SAAT INI')
                        ->numeric()
                        ->inputMode('decimal')
                        ->prefix('Rp ')
                        ->default(0)
                        ->columnSpan(1)
                        ->disabled(),
                        
                    Forms\Components\Toggle::make('is_active')
                        ->label('AKTIF')
                        ->default(true)
                        ->columnSpan(1),
                        
                    Forms\Components\Textarea::make('description')
                        ->label('DESKRIPSI')
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])->columns(3),
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('coa_code')
                ->label('KODE')
                
                ->searchable()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('coa_name')
                ->label('NAMA AKUN')
                ->searchable()
                ->sortable(),
                
            Tables\Columns\TextColumn::make('coa_type')
                ->label('JENIS')
                
                ->sortable(),
                
            Tables\Columns\TextColumn::make('coa_category')
                ->label('KATEGORI')
                
                ->sortable(),
                
            Tables\Columns\TextColumn::make('current_balance')
                ->label('SALDO')
                ->numeric(decimalPlaces: 2)
                ->money('IDR')
                ->color(fn (ChartOfAccount $record) => $record->current_balance < 0 ? 'danger' : 'success')
                ->alignRight(),
                
            Tables\Columns\IconColumn::make('is_active')
                ->label('AKTIF')
                ->boolean(),
        ])
            ->filters([
                //
                Tables\Filters\SelectFilter::make('coa_type')
                    ->label('Jenis Akun')
                    ->options(fn () => ChartOfAccount::distinct('coa_type')->pluck('coa_type', 'coa_type')),
                    
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                
            // Tambahkan action export PDF
            Action::make('exportPdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function ($record) {
                    $data = [
                        'record' => $record,
                        'title' => 'Chart of Account - ' . $record->coa_code,
                    ];
                    
                    $pdf = Pdf::loadHTML(
                        Blade::render('pdf.chart-of-account', $data)
                    );
                    
                    return response()->streamDownload(
                        fn () => print($pdf->output()),
                        "chart-of-account-{$record->coa_code}.pdf"
                    );
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                     // Bulk export PDF
                Action::make('exportPdfBulk')
                    ->label('Export Selected to PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($records) {
                        $data = [
                            'records' => $records,
                            'title' => 'Multiple Chart of Accounts',
                        ];
                        
                        $pdf = Pdf::loadHTML(
                            Blade::render('pdf.chart-of-account-multiple', $data)
                        );
                        
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "chart-of-accounts-".now()->format('YmdHis').".pdf"
                        );
                    }),
                ]),
            ])
            ->defaultSort('coa_code')
            ->striped();
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\JurnalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChartOfAccounts::route('/'),
            'create' => Pages\CreateChartOfAccount::route('/create'),
            'edit' => Pages\EditChartOfAccount::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
