<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Soal;
use App\Models\Hasil_ujian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class UjianController extends Controller
{
    public function show($id)
    {
        // mengambil data ujian berdasarkan ID
        $ujian = Ujian::findOrFail($id);

        // mengambil data soal berdasarkan ID ujian
        $questions = Soal::where('id_ujian', $id)->get();

        // Kirim data ke frontend
        return Inertia::render('Ujian', [
            'ujian' => $ujian,
            'questions' => $questions,
            'durasi' => $ujian->waktu, // ngirim durasi ke frontend
        ]);
    }

    public function submit(Request $request, $id, $answers)
    {
        // Decode JSON string from URL parameter
        $answers = json_decode($answers, true);

        // Log data yang diterima
        Log::info('Data jawaban diterima:', $answers);

        if (is_null($answers) || !is_array($answers)) {
            Log::error('Jawaban siswa tidak diterima.');
            return response()->json(['error' => 'Jawaban tidak valid'], 400);
        }

        $userId = Auth::user()->siswa->id;

        // Mengambil semua soal untuk ujian ini
        $questions = Soal::where('id_ujian', $id)->get();
        $jumlahSoal = $questions->count();
        $jumlahBenar = 0;

        // Menghitung jumlah jawaban benar
        foreach ($questions as $question) {
            $jawabanBenar = $question->jawaban;
            $jawabanSiswa = $answers[$question->id] ?? null;

            Log::info("Soal ID: {$question->id}");
            Log::info("Jawaban Benar: {$jawabanBenar}");
            Log::info("Jawaban Siswa: " . json_encode($jawabanSiswa));

            if (is_array($jawabanSiswa)) {
                $jawabanBenarArray = explode(',', $jawabanBenar);
                sort($jawabanSiswa);
                sort($jawabanBenarArray);

                if ($jawabanSiswa === $jawabanBenarArray) {
                    $jumlahBenar++;
                }
            } else {
                if ($jawabanSiswa === $jawabanBenar) {
                    $jumlahBenar++;
                }
            }
        }

        $jumlahSalah = $jumlahSoal - $jumlahBenar;
        $nilai = $jumlahSoal > 0 ? ($jumlahBenar / $jumlahSoal) * 100 : 0;

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