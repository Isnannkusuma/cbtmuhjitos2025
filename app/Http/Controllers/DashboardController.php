<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Akses_ujian;
use App\Models\Hasil_ujian;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $siswaId = Auth::user()->id_siswa; 

        // Ambil data akses ujian
        $aksesUjian = Akses_ujian::with(['ujian', 'siswa']) 
            ->where('id_siswa', $siswaId)
            ->get()
            ->map(function ($akses) use ($siswaId) {
                // Cek apakah ujian sudah selesai
                $hasil = Hasil_ujian::where('id_ujian', $akses->id_ujian)
                    ->where('id_siswa', $siswaId)
                    ->first();

                if ($hasil || $akses->status === 'completed') {
                    $akses->status = 'completed'; // Ujian selesai
                } elseif ($akses->status === 'in_progress') {
                    $akses->status = 'in_progress'; // Ujian sedang berlangsung
                } elseif ($akses->status === 'can_start') {
                    $akses->status = 'can_start'; // Ujian bisa dimulai
                } else {
                    $akses->status = 'not_started'; // Belum diberikan akses
                }

                // Tambahkan nama ujian dan nama siswa
                return [
                    'id_ujian' => $akses->id_ujian,
                    'nama_ujian' => $akses->ujian->nama_ujian ?? 'Tidak Tersedia', 
                    'nama_siswa' => $akses->siswa->nama_siswa ?? 'Tidak Tersedia', 
                    'status' => $akses->status,
                ];
            });

        // Ambil data hasil ujian
        $hasilUjian = Hasil_ujian::with('ujian')
            ->where('id_siswa', $siswaId)
            ->get();

        return Inertia::render('Dashboard', [
            'aksesUjian' => $aksesUjian,
            'hasilUjian' => $hasilUjian,
        ]);
    }
}