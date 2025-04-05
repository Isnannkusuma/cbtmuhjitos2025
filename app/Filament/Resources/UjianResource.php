<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UjianResource\Pages;
use App\Filament\Resources\UjianResource\RelationManagers;
use App\Models\Ujian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UjianResource extends Resource
{
    protected static ?string $model = Ujian::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationLabel = 'Daftar Ujian';

    protected static ?string $modelLabel = 'Daftar Ujian';

    protected static ?string $pluralModelLabel = 'Daftar Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_ujian')
                    ->label('Nama Ujian')
                    ->required(),
                Forms\Components\Select::make('id_mapel')
                    ->label('Nama Mata Pelajaran')
                    ->relationship('mapel', 'nama_mapel')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('id_guru')
                    ->label('Nama Guru')
                    ->relationship('guru', 'nama_guru')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('waktu')
                    ->label('Waktu (Menit)')
                    ->integer()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->label('No')
                    ->getStateUsing(fn($rowLoop) => $rowLoop->iteration)
                    ->searchable()
                    ->sortable(false),
                Tables\Columns\TextColumn::make('nama_ujian')
                    ->label('Nama Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mapel.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nama_guru')
                    ->label('Pengampu')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waktu')
                    ->label('Waktu (Menit)')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListUjians::route('/'),
            'create' => Pages\CreateUjian::route('/create'),
            // 'edit' => Pages\EditUjian::route('/{record}/edit'),
        ];
    }
}
