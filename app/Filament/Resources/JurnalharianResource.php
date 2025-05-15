<?php

namespace App\Filament\Resources;

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
            ->schema([
                Forms\Components\Section::make('Informasi Jurnal')
                    ->schema([
                        Forms\Components\DatePicker::make('jh_tanggal')
                            ->label('TANGGAL')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(today()->format('Y-m-d')) // Format Y-m-d adalah format default untuk value datepicker
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('jh_nomor_jurnal')
                            ->label('NOMOR JURNAL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('jh_nomor_dokumen')
                            ->label('NOMOR DOKUMEN')
                            ->maxLength(50)
                            ->columnSpan(1),
                    ])->columns(3),

                Forms\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Forms\Components\TextInput::make('jh_code_account')
                            ->label('CODE ACCOUNT')
                            ->required()
                            ->maxLength(20)
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('jh_nama_account')
                            ->label('NAMA ACCOUNT')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('jh_code_dept')
                            ->label('CODE DEPT')
                            ->required()
                            ->maxLength(20)
                            ->columnSpan(1),
                            
                        Forms\Components\TextInput::make('jh_departemen')
                            ->label('DEPARTEMEN')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),

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
                            
                        Forms\Components\TextInput::make('jh_pemohon')
                            ->label('PEMOHON')
                            ->required()
                            ->maxLength(100)
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('jh_keterangan')
                            ->label('KETERANGAN')
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])->columns(3),
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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
}
