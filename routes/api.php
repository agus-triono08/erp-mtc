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
use App\Http\Controllers\Inventory\NoSeriLogController;
use App\Http\Controllers\Inventory\PerawatanController;
use App\Http\Controllers\Inventory\ErrorController;
use App\Http\Controllers\Inventory\ErrorActivityController;
use App\Http\Controllers\Inventory\RusakController;
use App\Http\Controllers\Inventory\MusnahController;
use App\Http\Controllers\Inventory\HilangController;
use App\Http\Controllers\Inventory\PermintaanController;
use App\Http\Controllers\Inventory\PermintaanLogController;
use App\Http\Controllers\Inventory\PeminjamanController;
use App\Http\Controllers\Inventory\PeminjamanLogController;
use App\Http\Controllers\Inventory\PerubahanPeminjamanController;
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
Route::prefix('/v1/tools')->group(function () {
    Route::get('/low-stock/count', [ToolsController::class, 'apiLowStockTools']);
    Route::get('/low-stock/list', [ToolsController::class, 'listLowStockTools']); // Add this line        
    });
//NOSeri
Route::apiResource('v1/noseri', NoseriController::class);
Route::put('/v1/noseri/editlogs/{id}', [NoSeriController::class, 'editLog']);
Route::post('/v1/noseri/store/{kodeAlat}', [NoSeriController::class, 'store']);
Route::get('/v1/noseri/getNoSeri/{kodeAlat}', [NoseriController::class, 'getNoSeri']);
Route::prefix('v1/noseri')->group(function () {
    Route::post('/reject', [NoseriController::class, 'reject']);
    Route::post('/update-status', [NoseriController::class, 'updateStatus']);
    Route::post('/bulk-update-status', [NoseriController::class, 'bulkUpdateStatus']);
    Route::post('/reject-permintaan', [NoseriController::class, 'rejectPermintaan']);
    Route::post('/update-status-permintaan', [NoseriController::class, 'updateStatusPermintaan']);
    Route::post('/bulk-update-status-permintaan', [NoseriController::class, 'bulkUpdateStatusPermintaan']);
    Route::post('/update-status-permintaan-user', [NoseriController::class, 'updateStatusPermintaanUser']);
    Route::post('/update-status-peminjaman-user', [NoseriController::class, 'updateStatusPeminjamanUser']);    
    Route::post('/bulk-update-status-permintaan-user', [NoseriController::class, 'bulkUpdateStatusPermintaanUser']);
    Route::post('/bulk-update-status-peminjaman-user', [NoseriController::class, 'bulkUpdateStatusPeminjamanUser']);
    Route::post('/update-status-perubahan', [NoseriController::class, 'updateStatusPerubahanPeminjaman']);
    Route::post('/bulk-update-status-perubahan', [NoseriController::class, 'bulkUpdateStatusPerubahanPeminjaman']);
    Route::post('/reject-perubahan', [NoseriController::class, 'rejectPerubahanPeminjaman']);
    Route::post('/cek-kondisi', [NoseriController::class, 'cekKondisi']);
});
Route::get('/v1/logs/noseri', [NoSeriLogController::class, 'index']);
Route::get('/v1/noseri/{noSeriId}/logs', [NoSeriLogController::class, 'getLogs']);
Route::get('/v1/tool-conditions', [NoSeriController::class, 'getToolConditionData']);
//Perawatan
Route::apiResource('v1/perawatan', PerawatanController::class);
Route::prefix('v1/perawatan')->group(function() {
    Route::post('/status-pelaksanaan', [PerawatanController::class, 'statusPelaksanaan']);
    Route::post('/status-selesai', [PerawatanController::class, 'statusSelesai']);    
});
Route::prefix('/v1/perawatan')->group(function () {
    Route::get('/belum/count', [PerawatanController::class, 'countBelum']);
    Route::get('/belum/list', [PerawatanController::class, 'listBelum']); // Add this line        
    });
Route::group(['prefix' => 'inventory', 'as' => 'inventory.'], function() {
        Route::get('/perawatan/progress', [PerawatanController::class, 'getProgressData'])
        ->name('perawatan.progress');});
//Perbaikan
Route::apiResource('v1/perbaikan', ErrorController::class);
Route::get('/v1/perbaikan/getError/{noSeri}', [ErrorController::class, 'getError']);
Route::prefix('v1/perbaikan')->group(function() {
    Route::post('/update-status', [ErrorController::class, 'updateStatus']);
    Route::post('/add-activity', [ErrorController::class, 'addActivity']);
});
Route::apiResource('/v1/activity-perbaikan', ErrorActivityController::class);
Route::prefix('/v1/perbaikan')->group(function () {
        Route::get('/belum/count', [ErrorController::class, 'countBelum']);
        Route::get('/belum-diproses/list', [ErrorController::class, 'listBelumDiproses']); // Add this line        
        });
// Kerusakan
Route::apiResource('v1/kerusakan', RusakController::class);
Route::get('/v1/kerusakan/getRusak/{noSeri}', [RusakController::class, 'getRusak']);
Route::prefix('v1/kerusakan')->group(function() {
    Route::post('/add-activity', [RusakController::class, 'addActivity']);
    Route::post('/pemusnahan-diterima', [RusakController::class, 'pemusnahanDiterima']);
    Route::post('/pemusnahan-ditolak', [RusakController::class, 'pemusnahanDitolak']);
});
Route::prefix('/v1/kerusakan')->group(function () {
        Route::get('/belum/count', [RusakController::class, 'countBelum']);
        Route::get('/belum/list', [RusakController::class, 'listBelum']); // Add this line        
        });
// Pemusnahan
Route::apiResource('v1/pemusnahan', MusnahController::class);
Route::get('/v1/pemusnahan/getMusnah/{noSeri}', [MusnahController::class, 'getMusnah']);
Route::prefix('v1/pemusnahan')->group(function() {
    Route::post('/add-activity', [MusnahController::class, 'addActivity']);    
});
Route::prefix('/v1/pemusnahan')->group(function () {
        Route::get('/selesai/count', [MusnahController::class, 'countSelesai']);
        Route::get('/selesai/list', [MusnahController::class, 'listSelesai']); // Add this line        
        });
// Kehilangan
Route::apiResource('v1/kehilangan', HilangController::class);
Route::get('/v1/kehilangan/getHilang/{noSeri}', [HilangController::class, 'getHilang']);
Route::prefix('v1/kehilangan')->group(function() {
    Route::post('/add-activity', [HilangController::class, 'addActivity']);    
    Route::post('/add-activity-proses', [HilangController::class, 'addActivityProses']);
    Route::post('/pengantian-diterima', [HilangController::class, 'pengantianDiterima']);
    Route::post('/pengantian-ditolak', [HilangController::class, 'pengantianDitolak']);
    Route::post('/alat-diserahkan', [HilangController::class, 'alatDiserahkan']);
    Route::post('/alat-diterima', [HilangController::class, 'alatDiterima']);
});
Route::prefix('/v1/kehilangan')->group(function () {
        Route::get('/belum/count', [HilangController::class, 'countBelum']);
        Route::get('/belum/list', [HilangController::class, 'listBelum']); // Add this line        
        });
// Permintaan
Route::apiResource('v1/permintaan', PermintaanController::class);
Route::get('/v1/permintaan/getPermintaan/{kodeAlat}', [PermintaanController::class, 'getPermintaan']);
Route::get('/v1/permintaan/getNoPermintaan/{noPermintaan}', [PermintaanController::class, 'getNoPermintaan']);
Route::get('/v1/permintaan/getPengajuanNoPermintaan/{noPermintaan}', [PermintaanController::class, 'getPengajuanNoPermintaan']);
Route::get('/v1/logs-permintaan', [PermintaanLogController::class, 'index']);
Route::prefix('/v1/permintaan')->group(function () {
        Route::get('/belum-diproses/count', [PermintaanController::class, 'countBelumDiproses']);
        Route::get('/belum-diproses/list', [PermintaanController::class, 'listBelumDiproses']); // Add this line        
    });
Route::get('/v1/permintaan/chart/monthly-completed', [PermintaanController::class, 'monthlyCompletedLoansAlternative']);
Route::get('/v1/permintaan/chart/available-years', [PermintaanController::class, 'availableYears']);
Route::get('/v1/permintaan/chart/monthly-all-status', [PermintaanController::class, 'monthlyAllStatus']);
// Peminjaman
Route::apiResource('v1/peminjaman', PeminjamanController::class);
Route::get('/v1/peminjaman/getPeminjaman/{kodeAlat}', [PeminjamanController::class, 'getPeminjaman']);
Route::get('/v1/peminjaman/getNoPeminjaman/{noPinjam}', [PeminjamanController::class, 'getNoPeminjaman']);
Route::get('/v1/peminjaman/getPengajuanNoPeminjaman/{noPinjam}', [PeminjamanController::class, 'getPengajuanNoPeminjaman']);
Route::get('/v1/logs-peminjaman', [PeminjamanLogController::class, 'index']);
Route::prefix('/v1/peminjaman')->group(function () {
        Route::get('/belum-diproses/count', [PeminjamanController::class, 'countBelumDiproses']);
        Route::get('/belum-diproses/list', [PeminjamanController::class, 'listBelumDiproses']); // Add this line        
    });
Route::get('/v1/peminjaman/chart/monthly-completed', [PeminjamanController::class, 'monthlyCompletedLoans']);
Route::get('/v1/peminjaman/chart/available-years', [PeminjamanController::class, 'availableYears']);
Route::get('v1/peminjaman/chart/monthly-all-status', [PeminjamanController::class, 'monthlyAllStatus']);
// Perubahan Peminjaman
Route::apiResource('v1/perubahan-perminjaman', PerubahanPeminjamanController::class);
Route::post('/v1/perubahan-peminjaman/store/{noPinjam}', [PerubahanPeminjamanController::class, 'store']);
Route::get('/v1/getPerubahanNoPeminjaman/{noPinjam}', [PerubahanPeminjamanController::class, 'getPerubahanNoPeminjaman']);
Route::prefix('v1/perubahan-peminjaman')->group(function () {
    Route::post('/reject-perubahan', [PerubahanPeminjamanController::class, 'rejectPerubahan']);
});