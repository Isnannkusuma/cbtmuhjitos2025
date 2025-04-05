<?php

namespace App\Filament\Widgets;

use App\Models\Ujian;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentExams extends BaseWidget
{
    protected static ?string $heading = 'Ujian Terbaru';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ujian::query()
                    ->with(['mapel', 'guru'])
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->getStateUsing(fn($rowLoop) => $rowLoop->iteration)
                    ->sortable(false),
                    
                TextColumn::make('nama_ujian')
                    ->label('Nama Ujian')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mapel.nama_mapel')
                    ->label('Mata Pelajaran')
                    ->searchable(),

                TextColumn::make('guru.nama_guru')
                    ->label('Guru'),

                TextColumn::make('waktu')
                    ->label('Waktu (Menit)')
            ]);
    }
}
