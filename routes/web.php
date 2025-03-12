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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return view('authen.login');
})->name('login');


Route::group(['middleware' => 'guest'], function () {
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

// PERAWATAN
Route::get('/admin-mtc/perawatan', function() {
    return view('admin-mtc.Main.perawatan');
})->name('perawatan.adminmtc');

Route::get('/admin-mtc/jadwal-perawatan', function() {
    return view('admin-mtc.Main.jadwal-perawatan');
})->name('jadwalperawatan.adminmtc');

Route::get('/admin-mtc/riwayat-perawatan', function() {
    return view('admin-mtc.Main.riwayat-perawatan');
})->name('riwayatperawatan.adminmtc');

Route::get('/admin-mtc/lapor-kendala', function () {
    return view('admin-mtc.Main.laporkendala');
})->name('laporkendala.adminmtc');

Route::get('/admin-mtc/alat/data-error', function () {
    return view('admin-mtc.Main.component.alat.error.data');
})->name('dataalaterror.adminmtc');

Route::get('/admin-mtc/alat/data-error/input', function () {
    return view('admin-mtc.Main.component.alat.error.input');
})->name('input.dataalaterror.adminmtc');

Route::get('/admin-mtc/layout', function() {
    return view('admin-mtc.Main.layout');
})->name('layout.adminmtc');
});

Route::group(['middleware' => 'guest'], function () {
// USER
Route::get('/user/dashboard', function () {
    return view('user.Main.dashboard');
})->name('dashboard.user');

Route::get('/user/data', function () {
    return view('user.Main.data');
})->name('data.user');

Route::get('/user/peminjaman', function () {
    return view('user.Main.peminjaman');
})->name('peminjaman.user');
});

Route::group(['middleware' => 'guest'], function () {
// MANAJER MTC
Route::get('/manajer-mtc/dashboard', function () {
    return view('manajer-mtc.Main.dashboard');
})->name('dashboard.manajermtc');

Route::get('/manajer-mtc/master-data', function () {
    return view('manajer-mtc.Main.data');
})->name('data.manajermtc');

Route::get('/manajer-mtc/master-data/input', function () {
    return view('manajer-mtc.Main.component.inputalat');
})->name('data.input.manajermtc');

Route::get('/manajer-mtc/data-rincian-alat/input', function () {
    return view('manajer-mtc.Main.component.inputrincianalat');
})->name('data.input.rincian.manajermtc');

Route::get('/manajer-mtc/data-alat/detail', function () {
    return view('manajer-mtc.Main.component.detailalat');
})->name('data.detail.manajermtc');

Route::get('/manajer-mtc/riwayat', function(){
    return view('manajer-mtc.Main.riwayat');
})->name('manajermtc-riwayat');

Route::get('/manajer-mtc/peminjaman', function () {
    return view('manajer-mtc.Main.peminjaman');
})->name('peminjaman.manajermtc');

Route::get('/manajer-mtc/permintaan', function () {
    return view('manajer-mtc.Main.permintaan');
})->name('permintaan.manajermtc');

Route::get('/manajer-mtc/perawatan', function() {
    return view('manajer-mtc.Main.perawatan');
})->name('perawatan.manajermtc');

Route::get('/manajer-mtc/lapor-kendala', function () {
    return view('manajer-mtc.Main.laporkendala');
})->name('laporkendala.manajermtc');

Route::get('/manajer-mtc/alat/data-error', function () {
    return view('manajer-mtc.Main.component.alat.error.data');
})->name('dataalaterror.manajermtc');

Route::get('/manajer-mtc/alat/data-error/input', function () {
    return view('manajer-mtc.Main.component.alat.error.input');
})->name('input.dataalaterror.manajermtc');

Route::get('/manajer-mtc/layout', function() {
    return view('manajer-mtc.Main.layout');
})->name('layout.manajermtc');
});

// Kondisi
Route::get('/kondisi-error', function(){
    return view('admin-mtc.Main.Kondisi.kondisi-error');
})->name('kondisi-error');

Route::get('manager-mtc/kondisi-rusak', function(){
    return view('manajer-mtc.Main.Kondisi.kondisi-rusak');
})->name('kondisirusak.managermtc');

Route::get('/kondisi-rusak', function(){
    return view('admin-mtc.Main.Kondisi.kondisi-rusak');
})->name('kondisi-rusak');

Route::get('/kondisi-musnah', function(){
    return view('admin-mtc.Main.Kondisi.kondisi-musnah');
})->name('kondisi-musnah');

// DATA HILANG
Route::get('/admin-mtc/data-hilang', function(){
    return view('admin-mtc.Main.Kondisi.data-hilang');
})->name('datahilang.adminmtc');

Route::get('/user/data-hilang', function(){
    return view('user.Main.Kondisi.data-hilang');
})->name('datahilang.user');

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
