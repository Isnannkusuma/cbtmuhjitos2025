<?php

namespace App\Filament\Widgets;

use App\Models\Akses_ujian;
use Filament\Widgets\ChartWidget;

class AksesUjianPieChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Akses Ujian';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Status',
                    'data' => [
                        Akses_ujian::where('status', 'completed')->count(),
                        Akses_ujian::where('status', 'in_progress')->count(),
                        Akses_ujian::where('status', 'can_start')->count(),
                        Akses_ujian::where('status', 'not_started')->count(),
                    ],
                ],
            ],
            'labels' => ['Selesai', 'Sedang Mengerjakan', 'Bisa Mulai', 'Belum Dimulai'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
