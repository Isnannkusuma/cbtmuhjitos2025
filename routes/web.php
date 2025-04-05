<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\HasilUjianList;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportPDFController;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

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


require __DIR__.'/auth.php';
