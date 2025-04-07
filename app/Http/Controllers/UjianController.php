<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Hasil_ujian;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UjianController extends Controller
{
    public function show($id)
    {
        // Ambil data ujian berdasarkan ID
        $ujian = Ujian::findOrFail($id);

        // Ambil data soal berdasarkan ID ujian
        $questions = Soal::where('id_ujian', $id)->get();

        // Kirim data ke frontend
        return Inertia::render('Ujian', [
            'ujian' => $ujian,
            'questions' => $questions,
            'durasi' => $ujian->waktu, // Kirim durasi ke frontend
        ]);
    }
    public function submit(Request $request, $id)
    {
        $userId = Auth::user()->id;

        // Ambil semua soal untuk ujian ini
        $questions = Soal::where('id_ujian', $id)->get();
        $jumlahSoal = $questions->count();
        $jumlahBenar = 0;

        // Hitung jumlah jawaban benar
        foreach ($questions as $question) {
            $jawabanBenar = $question->jawaban;
            $jawabanSiswa = $request->answers[$question->id] ?? null;

            if (is_array($jawabanSiswa)) {
                // Jika soal multi-opsi
                sort($jawabanSiswa);
                $jawabanBenarArray = explode(',', $jawabanBenar);
                sort($jawabanBenarArray);

                if ($jawabanSiswa === $jawabanBenarArray) {
                    $jumlahBenar++;
                }
            } else {
                // Jika soal pilihan ganda atau benar/salah
                if ($jawabanSiswa === $jawabanBenar) {
                    $jumlahBenar++;
                }
            }
        }

        $jumlahSalah = $jumlahSoal - $jumlahBenar;
        $nilai = ($jumlahBenar / $jumlahSoal) * 100;

        // Simpan hasil ujian ke database
        Hasil_ujian::updateOrCreate(
            [
                'id_ujian' => $id,
                'id_siswa' => $userId,
            ],
            [
                'jumlah_soal' => $jumlahSoal,
                'jumlah_benar' => $jumlahBenar,
                'jumlah_salah' => $jumlahSalah,
                'nilai' => $nilai,
            ]
        );

        return redirect()->route('dashboard')->with('success', 'Ujian selesai! Hasil Anda telah disimpan.');
    }
}