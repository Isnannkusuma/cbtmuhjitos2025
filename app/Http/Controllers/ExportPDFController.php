<?php

namespace App\Http\Controllers;

use App\Models\Hasil_ujian;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportPDFController extends Controller
{
    public function export($id)
    {
        $hasilUjian = Hasil_ujian::with(['ujian', 'siswa'])
            ->where('id_ujian', $id)
            ->get();

        $pdf = Pdf::loadView('pdf.hasil-ujian', compact('hasilUjian'))->setPaper('A4', 'portrait');
        return $pdf->stream('hasil-ujian.pdf');
    }
}
