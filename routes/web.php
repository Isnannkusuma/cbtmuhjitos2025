<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\HasilUjianList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportPDFController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UjianController;
use Inertia\Inertia;


Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard'); // Redirect ke halaman dashboard jika sudah login
    }

    // Jika pengguna belum login, tampilkan halaman login
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/ujian', function () {
    return Inertia::render('Ujian');
})->middleware(['auth', 'verified'])->name('ujian');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/admin/hasil-ujians/{id}', HasilUjianList::class);

Route::get('/admin/avatar/{filename}', function ($filename) {
    $path = storage_path("app/private/avatars/{$filename}"); 

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('admin.avatar');

Route::get('/export/hasil-ujian/{id}', [ExportPDFController::class, 'export'])
    ->name('export.hasil.ujian');

Route::middleware(['auth'])
    ->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
});

Route::middleware(['auth'])->group(function () {

    Route::get('/ujian/{id}/submit/{answers}', [UjianController::class, 'submit'])->name('ujian.submit');
    Route::post('/ujian/{id}/submit', [UjianController::class, 'submit'])->name('ujian.submit');
    Route::get('/ujian/{id}', [UjianController::class, 'show'])->name('ujian.show');
});

require __DIR__.'/auth.php';
