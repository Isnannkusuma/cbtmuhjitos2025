<?php

namespace App\Filament\Widgets;

use App\Models\Mapel;
use Filament\Widgets\ChartWidget;

class RerataNilaiPerMapelChart extends ChartWidget
{
    protected static ?string $heading = 'Rata-rata Nilai per Mapel';
    protected int | string | array $columnSpan = 1;
    protected static ?int $sort = 2;
    

    protected function getData(): array
    {
        $mapels = Mapel::with('ujian.hasilUjian')->get();

        $labels = [];
        $data = [];

        foreach ($mapels as $mapel) {
            $nilai = 0;
            $jumlah = 0;
            foreach ($mapel->ujian as $ujian) {
                $nilai += $ujian->hasilUjian->sum('nilai');
                $jumlah += $ujian->hasilUjian->count();
            }

            $labels[] = $mapel->nama_mapel;
            $data[] = $jumlah > 0 ? round($nilai / $jumlah, 2) : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Rata-rata Nilai',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
