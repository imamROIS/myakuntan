<?php

namespace App\Filament\Resources;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\JurnalharianResource\Pages;
use App\Filament\Resources\JurnalharianResource\RelationManagers;
use App\Models\Jurnalharian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Actions\ReplicateAction;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions; // Gunakan Actions untuk grup tombol
// use Filament\Forms\Components\Actions\Action; // Gunakan Action untuk tombol individual
use Filament\Support\Enums\Alignment; // Untuk alignment Actions



use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

// use Filament\Tables\Actions\Action;

use Barryvdh\DomPDF\Facade\Pdf; // Tambahkan ini untuk PDF generation
use Illuminate\Support\Facades\Blade; // Tambahkan ini untuk rendering blade

class JurnalharianResource extends Resource
{
    protected static ?string $model = JurnalHarian::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $modelLabel = 'Jurnal Harian';

    protected static ?string $navigationLabel = 'Jurnal Harian';

    protected static ?string $navigationGroup = 'Akuntansi';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
        ->live() // Tambahkan ini
            ->schema([
                Forms\Components\Section::make('Informasi Jurnal')
                ->description('The items you have selected for purchase')                
                ->extraAttributes(['class' => 'bg-white dark:bg-gray-800 shadow-md rounded-lg'])
                    ->schema([
                        Forms\Components\DatePicker::make('jh_tanggal')
                            ->label('TANGGAL')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->columnSpan(1),
                        Forms\Components\Select::make('jh_code_account')
                            ->label('CODE ACCOUNT')
                            ->required()
                            ->relationship(
                                name: 'coa', 
                                titleAttribute: 'coa_code',
                                modifyQueryUsing: fn (Builder $query) => $query->where('is_active', true)->orderBy('coa_code')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->coa_code} - {$record->coa_name}")
                            ->searchable(['coa_code', 'coa_name'])
                            ->preload()
                            ->columnSpan(1)
                            ->live(), // Tambahkan live untuk real-time update

                        Forms\Components\TextInput::make('jh_dr')
                            ->label('DEBIT (DR)')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefix('Rp ')
                            ->default(0)
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('jh_cr')
                            ->label('KREDIT (CR)')
                            ->numeric()
                            ->inputMode('decimal')
                            ->prefix('Rp ')
                            ->default(0)
                            ->columnSpan(1),

                        
                    ])->columns(5),

                Forms\Components\Section::make('Detail Transaksi')
                    
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('jh_nama_account')
                            ->label('NAMA TRANSAKSI')
                            
                            ->maxLength(100)
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('jh_nomor_jurnal')
                            ->label('NOMOR JURNAL')
                            
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('jh_nomor_dokumen')
                            ->label('NOMOR DOKUMEN')
                            ->maxLength(50)
                            ->columnSpan(1),
                        
                            
                        
                            
                            
                        Forms\Components\TextInput::make('jh_code_dept')
                            ->label('CODE DEPT')
                            
                            ->maxLength(20)
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('jh_departemen')
                            ->label('DEPARTEMEN')
                            
                            ->maxLength(100)
                            ->columnSpan(2),

                        
                            
                        Forms\Components\TextInput::make('jh_pemohon')
                            ->label('PEMOHON')
                            
                            ->maxLength(100)
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('jh_keterangan')
                            ->label('KETERANGAN')
                            ->maxLength(500)
                            ->columnSpanFull(),
                        
                    ])->columns(3),
                    
                Forms\Components\Section::make('Validasi Akuntansi')
                    ->schema([
                        Forms\Components\Placeholder::make('validation_info')
                            ->content(function (Forms\Get $get) {
                                $debit = (float) $get('jh_dr') ?? 0;
                                $credit = (float) $get('jh_cr') ?? 0;
                                
                                if (abs($debit - $credit) > 0.01) {
                                    return "PERINGATAN: Total debit dan kredit tidak balance! Selisih: " . ($debit - $credit);
                                }
                                
                                return "Valid: Debit dan Kredit balance";
                            })
                            ->columnSpanFull(),
                    ])
                    ->hidden(fn (Forms\Get $get) => abs(((float) $get('jh_dr') ?? 0) - ((float) $get('jh_cr') ?? 0)) <= 0.01),
            ]); 

    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('jh_tanggal')
                ->label('TANGGAL')
                ->date('d/m/Y')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('jh_nomor_jurnal')
                ->label('NOMOR JURNAL')
                ->searchable(),

            Tables\Columns\TextColumn::make('jh_nama_account')
                ->label('NAMA ACCOUNT')
                ->searchable()
                ->formatStateUsing(fn (string $state): string => Str::upper($state)),

            Tables\Columns\TextColumn::make('jh_departemen')
                ->label('DEPARTEMEN')
                ->searchable()
                ->formatStateUsing(fn (string $state): string => Str::upper($state)),

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
                ->label('PEMOHON')
                ->searchable()
                ->formatStateUsing(fn (string $state): string => Str::upper($state)),

            Tables\Columns\TextColumn::make('created_at')
                ->label('DIBUAT')
                ->dateTime('d/m/Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->filters([
            Tables\Filters\TrashedFilter::make(),
            Tables\Filters\SelectFilter::make('jh_departemen')
                ->label('DEPARTEMEN')
                ->options(fn () => JurnalHarian::query()
                    ->pluck('jh_departemen', 'jh_departemen')
                    ->mapWithKeys(fn ($item) => [Str::upper($item) => Str::upper($item)]))
                ->searchable(),
            Tables\Filters\Filter::make('jh_tanggal')
                ->form([
                    Forms\Components\DatePicker::make('dari_tanggal')
                        ->label('DARI TANGGAL'),
                    Forms\Components\DatePicker::make('sampai_tanggal')
                        ->label('SAMPAI TANGGAL'),
                ])
                ->query(function ($query, array $data) {
                    return $query
                        ->when($data['dari_tanggal'],
                            fn ($query) => $query->whereDate('jh_tanggal', '>=', $data['dari_tanggal']))
                        ->when($data['sampai_tanggal'],
                            fn ($query) => $query->whereDate('jh_tanggal', '<=', $data['sampai_tanggal']));
                }),
        ])
        ->actions([
            Tables\Actions\ViewAction::make(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
            Tables\Actions\RestoreAction::make(),
            Tables\Actions\ForceDeleteAction::make(),
            Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->action(function ($record) {
                        // Data yang akan dikirim ke view PDF
                        $data = [
                            'record' => $record,
                            'title' => 'Jurnal Harian - ' . $record->jh_nomor_jurnal,
                        ];
                        
                        // Generate PDF
                        $pdf = Pdf::loadHTML(
                            Blade::render('pdf.jurnalharian', $data)
                        );
                        
                        // Download PDF
                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "jurnal-harian-{$record->jh_nomor_jurnal}.pdf"
                        );
                    }),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
                Tables\Actions\BulkAction::make('exportPdf')
                        ->label('Export Selected to PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function ($records) {
                            $data = [
                                'records' => $records,
                                'title' => 'Multiple Jurnal Harian',
                            ];
                            
                            $pdf = Pdf::loadHTML(
                                Blade::render('pdf.jurnalharian-multiple', $data)
                            );
                            
                            return response()->streamDownload(
                                fn () => print($pdf->output()),
                                "jurnal-harian-multiple.pdf"
                            );
                        }),
            ]),
        ])
        ->defaultSort('jh_tanggal', 'desc')
        ->striped();
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
           'index' => Pages\ListJurnalharians::route('/'),
            'create' => Pages\CreateJurnalharian::route('/create'),
            'edit' => Pages\EditJurnalharian::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                \Illuminate\Database\Eloquent\SoftDeletingScope::class,
            ]);
    }
}
