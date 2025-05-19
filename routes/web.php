<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('authen.login');
})->name('login');

// Route untuk guest (belum login)
Route::middleware(['guest'])->group(function () {
    // Anda bisa menambahkan route untuk login/register di sini jika diperlukan
});

// Route untuk Admin MTC (harus login)
Route::middleware(['guest'])->prefix('admin-mtc')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin-mtc.Main.dashboard');
    })->name('dashboard.adminmtc');

    Route::get('/data-alat', function () {
        return view('admin-mtc.Main.data');
    })->name('data.adminmtc');

    Route::get('/data-alat/input', function () {
        return view('admin-mtc.Main.component.inputalat');
    })->name('data.input.adminmtc');

    Route::get('/data-rincian-alat/input', function () {
        return view('admin-mtc.Main.component.inputrincianalat');
    })->name('data.input.rincian.adminmtc');

    Route::get('/data-alat/detail', function () {
        return view('admin-mtc.Main.component.detailalat');
    })->name('data.detail.adminmtc');

    Route::get('/riwayat', function(){
        return view('admin-mtc.Main.riwayat');
    })->name('adminmtc-riwayat');

    Route::get('/peminjaman', function () {
        return view('admin-mtc.Main.peminjaman');
    })->name('peminjaman.adminmtc');

    Route::get('/permintaan', function () {
        return view('admin-mtc.Main.permintaan');
    })->name('permintaan.adminmtc');

    // PERAWATAN
    Route::get('/perawatan', function() {
        return view('admin-mtc.Main.perawatan');
    })->name('perawatan.adminmtc');

    Route::get('/jadwal-perawatan', function() {
        return view('admin-mtc.Main.jadwal-perawatan');
    })->name('jadwalperawatan.adminmtc');

    Route::get('/perencanaan/jadwal-perawatan', function() {
        return view('admin-mtc.Main.perencanaan-jadwal-perawatan');
    })->name('perencanaanjadwalperawatan.adminmtc');

    Route::get('/riwayat-perawatan', function() {
        return view('admin-mtc.Main.riwayat-perawatan');
    })->name('riwayatperawatan.adminmtc');

    Route::get('/lapor-kendala', function () {
        return view('admin-mtc.Main.laporkendala');
    })->name('laporkendala.adminmtc');

    Route::get('/alat/data-error', function () {
        return view('admin-mtc.Main.component.alat.error.data');
    })->name('dataalaterror.adminmtc');

    Route::get('/alat/data-error/input', function () {
        return view('admin-mtc.Main.component.alat.error.input');
    })->name('input.dataalaterror.adminmtc');

    Route::get('/layout', function() {
        return view('admin-mtc.Main.layout');
    })->name('layout.adminmtc');

    Route::get('/inventory', function() {
        return view('admin-mtc.Main.kategori');
    })->name('kategori.adminmtc');
});

// Route untuk User MTC (harus login)
Route::middleware(['guest'])->prefix('user-mtc')->group(function () {
    Route::get('/dashboard', function () {
        return view('user-mtc.Main.dashboard');
    })->name('dashboard.user-mtc');

    Route::get('/data', function () {
        return view('user-mtc.Main.data');
    })->name('data.user-mtc');

    Route::get('/peminjaman', function () {
        return view('user-mtc.Main.peminjaman');
    })->name('peminjaman.user-mtc');
});

// Route untuk User biasa (harus login)
Route::middleware(['guest'])->prefix('user')->group(function () {
    Route::get('/dashboard', function () {
        return view('user.Main.dashboard');
    })->name('dashboard.user');

    Route::get('/data', function () {
        return view('user.Main.data');
    })->name('data.user');

    Route::get('/peminjaman', function () {
        return view('user.Main.peminjaman');
    })->name('peminjaman.user');

    Route::get('/data-hilang', function(){
        return view('user.Main.Kondisi.data-hilang');
    })->name('datahilang.user');
});

// Route untuk Manajer MTC (harus login)
Route::middleware(['guest'])->prefix('manajer-mtc')->group(function () {
    Route::get('/dashboard', function () {
        return view('manajer-mtc.Main.dashboard');
    })->name('dashboard.manajermtc');

    Route::get('/master-data', function () {
        return view('manajer-mtc.Main.data');
    })->name('data.manajermtc');

    Route::get('/master-data/input', function () {
        return view('manajer-mtc.Main.component.inputalat');
    })->name('data.input.manajermtc');

    Route::get('/data-rincian-alat/input', function () {
        return view('manajer-mtc.Main.component.inputrincianalat');
    })->name('data.input.rincian.manajermtc');

    Route::get('/data-alat/detail', function () {
        return view('manajer-mtc.Main.component.detailalat');
    })->name('data.detail.manajermtc');

    Route::get('/riwayat', function(){
        return view('manajer-mtc.Main.riwayat');
    })->name('manajermtc-riwayat');

    Route::get('/peminjaman', function () {
        return view('manajer-mtc.Main.peminjaman');
    })->name('peminjaman.manajermtc');

    Route::get('/permintaan', function () {
        return view('manajer-mtc.Main.permintaan');
    })->name('permintaan.manajermtc');

    Route::get('/perawatan', function() {
        return view('manajer-mtc.Main.perawatan');
    })->name('perawatan.manajermtc');

    Route::get('/lapor-kendala', function () {
        return view('manajer-mtc.Main.laporkendala');
    })->name('laporkendala.manajermtc');

    Route::get('/alat/data-error', function () {
        return view('manajer-mtc.Main.component.alat.error.data');
    })->name('dataalaterror.manajermtc');

    Route::get('/alat/data-error/input', function () {
        return view('manajer-mtc.Main.component.alat.error.input');
    })->name('input.dataalaterror.manajermtc');

    Route::get('/layout', function() {
        return view('manajer-mtc.Main.layout');
    })->name('layout.manajermtc');

    Route::get('/inventory', function() {
        return view('manajer-mtc.Main.kategori');
    })->name('kategori.managermtc');

    Route::get('/kondisi-rusak', function(){
        return view('manajer-mtc.Main.Kondisi.kondisi-rusak');
    })->name('kondisirusak.managermtc');
    
});

// Route untuk kondisi (bisa disesuaikan apakah perlu guest atau tidak)
Route::middleware(['guest'])->group(function () {
    Route::get('/kondisi-error', function(){
        return view('admin-mtc.Main.Kondisi.kondisi-error');
    })->name('kondisi-error');

    Route::get('/kondisi-rusak', function(){
        return view('admin-mtc.Main.Kondisi.kondisi-rusak');
    })->name('kondisi-rusak');

    Route::get('/kondisi-musnah', function(){
        return view('admin-mtc.Main.Kondisi.kondisi-musnah');
    })->name('kondisi-musnah');

    Route::get('/admin-mtc/data-hilang', function(){
        return view('admin-mtc.Main.Kondisi.data-hilang');
    })->name('datahilang.adminmtc');
});

// Auth routes (jika menggunakan Laravel Auth bawaan)
Auth::routes();
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');