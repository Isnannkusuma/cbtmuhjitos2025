<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HasilUjianResource\Pages;
use App\Models\Ujian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Livewire;


class HasilUjianResource extends Resource
{
    protected static ?string $model = Ujian::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $navigationLabel = 'Hasil Ujian';

    protected static ?string $modelLabel = 'Hasil Ujian';

    protected static ?string $pluralModelLabel = 'Hasil Ujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('id_ujian')
                    ->label('Nama Ujian')
                    ->relationship('ujian', 'nama_ujian')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('id_siswa')
                    ->label('Nama Siswa')
                    ->relationship('siswa', 'nama_siswa')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('jumlah_soal')
                    ->label('Jumlah Soal')
                    ->integer()
                    ->required(),
                Forms\Components\TextInput::make('jumlah_benar')
                    ->label('Jumlah Benar')
                    ->integer()
                    ->required(),
                Forms\Components\TextInput::make('jumlah_salah')
                    ->label('Jumlah Salah')
                    ->integer()
                    ->required(),
                Forms\Components\TextInput::make('nilai')
                    ->label('nilai')
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
                    ->getStateUsing(fn($rowLoop) => $rowLoop->iteration),
                Tables\Columns\TextColumn::make('nama_ujian')
                    ->label('Nama Ujian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nama_guru')
                    ->label('Nama Guru')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mapel.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_soal')
                    ->label('Jumlah Soal')
                    ->getStateUsing(fn($record) => $record->soal()->count()),
                Tables\Columns\TextColumn::make('waktu')
                    ->label('Waktu')
                    ->getStateUsing(fn($record) => $record->waktu . ' menit')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Daftar Hasil Ujian')
                    ->schema([
                        TextEntry::make('mapel.nama_mapel'),
                        TextEntry::make('nama_ujian'),
                        TextEntry::make('guru.nama_guru'),
                        TextEntry::make('jumlah_soal')
                            ->label('Jumlah Soal')
                            ->getStateUsing(fn($record) => $record->soal()->count()),
                        TextEntry::make('nilai_tertinggi')
                            ->label('Nilai Tertinggi')
                            ->getStateUsing(
                                fn($record) =>
                                $record->hasilUjian()->where('id_ujian', $record->id)->max('nilai') ?: 0
                            ),
                        TextEntry::make('nilai_terendah')
                            ->label('Nilai Terendah')
                            ->getStateUsing(
                                fn($record) =>
                                $record->hasilUjian()->where('id_ujian', $record->id)->min('nilai') ?: 0
                            ),
                        TextEntry::make('rata-rata')
                            ->label('Rata-rata Nilai')
                            ->getStateUsing(
                                fn($record) =>
                                $record->hasilUjian()->where('id_ujian', $record->id)->avg('nilai') ?: 0
                            ),
                        TextEntry::make('waktu')
                            ->label('Waktu')
                            ->getStateUsing(fn($record) => $record->waktu . ' menit'),
                    ])->columns(2),
                Section::make('Data Hasil Ujian')
                    ->schema([
                        Livewire::make('hasil-ujian-list')
                            ->extraAttributes(['id' => fn($record) => $record->id])
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
            'index' => Pages\ListHasilUjians::route('/'),
            'view' => Pages\ViewHasilUjians::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
