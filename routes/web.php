<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth; // Add this line

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
    return view('welcome');
});

Route::get('/admin-mtc/dashboard', function () {
    return view('admin-mtc.Main.dashboard');
})->name('dashboard.adminmtc');

Route::get('/admin-mtc/data-alat', function () {
    return view('admin-mtc.Main.data');
})->name('data.adminmtc');

Route::get('/admin-mtc/data-alat/input', function () {
    return view('admin-mtc.Main.component.inputalat');
})->name('data.input.adminmtc');

Route::get('/admin-mtc/data-rincian-alat/input', function () {
    return view('admin-mtc.Main.component.inputrincianalat');
})->name('data.input.rincian.adminmtc');

Route::get('/admin-mtc/data-alat/detail', function () {
    return view('admin-mtc.Main.component.detailalat');
})->name('data.detail.adminmtc');

Route::get('/admin-mtc/riwayat', function(){
    return view('admin-mtc.Main.riwayat');
})->name('adminmtc-riwayat');

Route::get('/admin-mtc/peminjaman', function () {
    return view('admin-mtc.Main.peminjaman');
})->name('peminjaman.adminmtc');

Route::get('/admin-mtc/permintaan', function () {
    return view('admin-mtc.Main.permintaan');
})->name('permintaan.adminmtc');

Route::get('/admin-mtc/perawatan', function() {
    return view('admin-mtc.Main.perawatan');
})->name('perawatan.adminmtc');

Route::get('/admin-mtc/lapor-kendala', function () {
    return view('admin-mtc.Main.laporkendala');
})->name('laporkendala.adminmtc');

Route::get('/admin-mtc/alat/data-error', function () {
    return view('admin-mtc.Main.component.alat.error.data');
})->name('dataalaterror.adminmtc');

Route::get('/admin-mtc/alat/data-error/input', function () {
    return view('admin-mtc.Main.component.alat.error.input');
})->name('input.dataalaterror.adminmtc');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
