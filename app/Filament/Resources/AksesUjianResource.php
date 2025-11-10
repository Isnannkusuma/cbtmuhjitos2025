<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AksesUjianResource\Pages;
use App\Models\Akses_ujian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;

class AksesUjianResource extends Resource
{
    protected static ?string $model = Akses_ujian::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationLabel = 'Akses Ujian';

    protected static ?string $modelLabel = 'Akses Ujian';

    protected static ?string $pluralModelLabel = 'Akses Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_siswa')
                    ->label('Nama Siswa')
                    ->relationship('siswa', 'nama_siswa')
                    ->placeholder('Pilih Siswa')
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('id_ujian')
                    ->label('Nama Ujian')
                    ->relationship('ujian', 'nama_ujian')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'not_started' => 'Belum Dimulai',
                        'can_start' => 'Start',
                        'completed' => 'Selesai',
                    ])
                    ->required()
                    ->reactive(),
            ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->getStateUsing(fn($rowLoop) => $rowLoop->iteration)
                    ->sortable(false),
                Tables\Columns\TextColumn::make('siswa.nama_siswa')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ujian.nama_ujian')
                    ->label('Nama Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'not_started' => 'gray',
                        'can_start' => 'primary',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                    })
                    ->formatStateUsing(fn($state) => match ($state) {
                        'not_started' => 'Belum Dimulai',
                        'can_start' => 'Start',
                        'in_progress' => 'Sedang Dikerjakan',
                        'completed' => 'Selesai',
                    })
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('id_siswa')
                    ->label('Nama Siswa')
                    ->relationship('siswa', 'nama_siswa')
                    ->searchable()
                    ->placeholder('Pilih Siswa'),
                SelectFilter::make('id_ujian')
                    ->label('Nama Ujian')
                    ->relationship('ujian', 'nama_ujian')
                    ->searchable()
                    ->placeholder('Pilih Ujian'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->placeholder('Pilih Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->button()
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_edit')
                        ->label('Edit Data')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->action(function ($records, $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => $data['status'],
                                ]);
                            });
                        })
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'not_started' => 'Belum Dimulai',
                                    'can_start' => 'Start',
                                    'completed' => 'Selesai',
                                ])
                                ->required(),
                        ]),
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
            'index' => Pages\ListAksesUjians::route('/'),
            // 'create' => Pages\CreateAksesUjian::route('/create'),
            // 'edit' => Pages\EditAksesUjian::route('/{record}/edit'),
        ];
    }
}
