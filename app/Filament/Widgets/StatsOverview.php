<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Ujian;

class StatsOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 1;
    
    protected function getCards(): array
{
    return [
        Stat::make('Total Siswa', Siswa::count())
            ->description('Siswa aktif terdaftar')
            ->descriptionIcon('heroicon-m-users')
            ->color('success'),

        Stat::make('Total Guru', Guru::count())
            ->description('Guru pengampu mapel')
            ->descriptionIcon('heroicon-m-user-group')
            ->color('info'),

        Stat::make('Total Ujian', Ujian::count())
            ->description('Jumlah ujian yang telah dibuat')
            ->descriptionIcon('heroicon-m-clipboard-document-check')
            ->color('warning'),
    ];
}
}
