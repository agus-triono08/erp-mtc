<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\TechIssueController;
use App\Http\Controllers\RincianAlatController;
use App\Http\Controllers\MesinController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Inventory\MerekController;
use App\Http\Controllers\Inventory\JenisController;
use App\Http\Controllers\Inventory\KategoriController;
use App\Http\Controllers\Inventory\KategoriMerekController;
use App\Http\Controllers\Inventory\TipeController;
use App\Http\Controllers\Inventory\ToolsController;
use App\Http\Controllers\Inventory\NoSeriController;
use App\Http\Controllers\Inventory\PerawatanController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//Inventory
Route::post('/alats', [AlatController::class, 'store']);
Route::get('/alats', [AlatController::class, 'index']);
Route::get('/alats/{id}', [AlatController::class, 'show']);
Route::get('/alats/{id}/edit', [AlatController::class, 'edit']);
Route::post('/alats/{id}/update', [AlatController::class, 'update']);
Route::delete('/alats/{id}/edit', [AlatController::class, 'destroy']);

//Alat Error
Route::post('/alats/error', [AlatController::class, 'storeError']);
Route::get('/alats/error', [AlatController::class, 'indexError']);
Route::get('alats/error/{id}', [AlatController::class, 'showError']);
Route::get('alats/errors/{kodeAlat}', [AlatController::class, 'getErrorsByKodeAlat']);
Route::get('alats/error/{id}/edit', [AlatController::class, 'editError']);

//Alat Rusak
Route::get('alats/datarusak/{kodeAlat}', [AlatController::class, 'getRusakByKodeAlat']);
Route::get('/alats/datarusak/{id}/edit', [AlatController::class, 'editRusak']);

//Alat Musnah
Route::get('alats/datamusnah/{kodeAlat}', [AlatController::class, 'getMusnahByKodeAlat']);

//Alat Hilang
Route::get('alats/datahilang/{kodeAlat}', [AlatController::class, 'getHilangByKodeAlat']);
Route::get('alats/datahilang/{id}/edit', [AlatController::class, 'editHilang']);

//Lokasi Penyimpanan
Route::get('locations', [AlatController::class, 'getLokasiPenyimpanan']);

//Peminjaman Alat
Route::get('/peminjaman', [AlatController::class, 'indexPeminjaman']);
Route::get('peminjaman/{id}', [AlatController::class, 'showPeminjaman']);
Route::get('/peminjaman/alats/{kodeAlat}', [AlatController::class, 'getPeminjamanAlatByKodeAlat']);
Route::get('/peminjaman/alats/nopin/{NoPinjam}', [AlatController::class, 'getPeminjamanAlatByNoPinjam']);
Route::get('/peminjaman/alats/dipinjam/{id}/edit', [AlatController::class, 'editRincianPeminjamanAlat']);

//Permintaan Alat
Route::get('alats/permintaan/{kodeAlat}', [AlatController::class, 'getPermintaanAlatBykodeAlat']);
Route::get('alats/permintaan/sudah-digunakan/{id}/edit', [AlatController::class, 'editRincianPermintaanAlat']);
Route::get('/permintaan', [AlatController::class, 'indexPermintaan']);
Route::get('/permintaan/{id}', [AlatController::class, 'showPermintaan']);
Route::get('/permintaan/rincian/noper/{noPermintaan}', [AlatController::class, 'byNoPermintaan']);

//Perawatan Alat
Route::get('/perawatan/alat', [AlatController::class, 'indexPerawatanAlat']);
Route::get('/perawatan/alat/{id}/edit', [AlatController::class, 'editPerawatanAlat']);
Route::get('/perawatan/rincian-alat/{id}/edit', [AlatController::class, 'editRincianPerawatanAlat']);
Route::get('/perawatan/alat/{id}', [AlatController::class, 'showPerawatanAlat']);
Route::get('/perawatan/alat/norawat/{noRawat}', [AlatController::class, 'getPerawatanAlatByNoPerawatan']);

//No Seri Data Alat
Route::get('/no-seri/belumdigunakan/{kodeAlat}', [AlatController::class, 'getNoSeriBelumDigunakanByKodeAlat']);
Route::get('/no-seri/belum-digunakan/{noSeri}/riwayat', [AlatController::class, 'getRiwayatAlatByNoSeri']);
Route::get('/no-seri/belumdigunakan/{id}/edit', [AlatController::class, 'editNoSeriBelumDigunakan']);
Route::get('/no-seri/belum-digunakan/detail-riwayat/{id}', [AlatController::class, 'showRiwayatAlat']);
Route::get('/no-seri', [AlatController::class, 'indexNoSeri']);
Route::get('/no-seri/{kode_alat}', [AlatController::class, 'getNoSeriByKodeAlat']);

//Riwayat
Route::get('/riwayat/alats', [AlatController::class, 'indexRiwayatAlat']);


//Data Master Mesin
Route::get('/mesins', [MesinController::class, 'index']);
Route::get('/mesins/{id}/edit', [MesinController::class, 'edit']);
Route::get('/mesins/{id}', [MesinController::class, 'show']);
Route::get('/mesins/no-seri/belumdigunakan/{kodMesin}', [MesinController::class, 'byKodeMesinNoSeriBelumDigunakan']);
Route::get('/mesins/no-seri/belum-digunakan/{id}/edit', [MesinController::class, 'editNoSeriBelumDigunakan']);
Route::get('/mesins/no-seri/sudahdigunakan/{kodeMesin}', [MesinController::class, 'byKodeMesinPermintaan']);
Route::get('/mesins/no-seri/peminjaman/{kodeMesin}', [MesinController::class, 'byKodeMesinPeminjaman']);
Route::get('/mesins/no-seri/belum-digunakan/detail-riwayat/{id}', [MesinController::class, 'showRiwayatMesin']);
Route::get('/mesins/errors/{kodeMesin}', [MesinController::class, 'byKodeMesinError']);
Route::get('/mesins/rusak/{kodeMesin}', [MesinController::class, 'byKodeMesinRusak']);
Route::get('/mesins/musnah/{kodeMesin}', [MesinController::class, 'byKodeMesinMusnah']);
Route::get('/mesin/peminjaman', [MesinController::class, 'indexPeminjamanMesin']);
Route::get('/mesin/perawatan', [MesinController::class, 'indexPerawatanMesin']);

//Nama PIC MTC
Route::post('/pic/mtc', [AlatController::class, 'storeStaff']);
Route::get('/pic/mtc', [AlatController::class, 'indexStaff']);
Route::get('/pic/mtc/{id}', [AlatController::class, 'showStaff']);
Route::get('/pic/mtc/{id}/edit', [AlatController::class, 'editStaff']);

Route::get('/rincian-alat', [RincianAlatController::class, 'index']);
Route::post('/rincian-alat', [RincianAlatController::class, 'store']);
Route::delete('/rincian-alat/{id}', [RincianAlatController::class, 'destroy']);

Route::post('tech-issue', [TechIssueController::class, 'store']);

//USER
Route::apiResource('v1/users', UserController::class);
Route::post('v1/login', [UserController::class, 'login']);

// LAYOUT
Route::apiResource('v1/layouts', LayoutController::class);

// DIVISI
Route::apiResource('v1/divisis', DivisiController::class);

// JABATAN
Route::apiResource('v1/jabatans', JabatanController::class);

//Inventory
//MEREK
Route::apiResource('v1/merek', MerekController::class);
//JENIS
Route::apiResource('v1/jenis', JenisController::class);
//KATEGORI
Route::apiResource('v1/kategori', KategoriController::class);
//KategoriMerek
Route::apiResource('v1/kategori-merek', KategoriMerekController::class);
Route::get('/v1/kategori-merek/check', [KategoriMerekController::class, 'check']);
//TIPE
Route::apiResource('v1/tipe', TipeController::class);
//TOOLS
Route::apiResource('v1/tools', ToolsController::class);
Route::get('/v1/tools/{id}/no-seri', [ToolsController::class, 'getNoSeriByTool']);
//NOSeri
Route::apiResource('v1/noseri', NoseriController::class);
Route::get('/v1/noseri/getNoSeri/{kodeAlat}', [NoseriController::class, 'getNoSeri']);
//Perawatan
Route::apiResource('v1/perawatan', PerawatanController::class);