import Vue from 'vue';
import VueRouter from 'vue-router';




/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

Vue.use(VueRouter);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);
//Vue.component('input-alat', require('./components/admin-mtc/InputAlat.vue').default);
Vue.component('lapor-kendala', require('./components/admin-mtc/LaporKendala.vue').default);
Vue.component('alat-rusak', require('./components/admin-mtc/DataDetailAlat/DataAlatRusak.vue').default);
Vue.component('alat-musnah', require('./components/admin-mtc/DataDetailAlat/DataAlatMusnah.vue').default);
Vue.component('alat-error', require('./components/admin-mtc/DataDetailAlat/DataAlatError.vue').default);
Vue.component('alat-hilang', require('./components/admin-mtc/DataDetailAlat/DataAlatHilang.vue').default);
Vue.component('rincian-alat', require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DataRincianAlat.vue').default);
Vue.component('rincian-alat-sudah-digunakan', require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DataRincianAlatSudahDigunakan.vue').default);
Vue.component('rincian-alat-peminjaman', require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DataRincianAlatPeminjaman.vue').default);
Vue.component('input-rincian-alat', require('./components/admin-mtc/InputRincianAlat.vue').default);
Vue.component('mesin-hilang', require('./components/admin-mtc/DataMesin/DetailMesin/Kondisi/DataHilang.vue').default);
//Vue.component('data-alat', require('./components/admin-mtc/DataAlat.vue').default);
//Vue.component('detail-alat', require('./components/admin-mtc/DetailAlat.vue').default);

// const TabNavigationRiwayat = require('./components/admin-mtc/Riwayat/TabNavigation.vue').default;

// Manajer MTC
const MasterDataM = require('./components/manajer-mtc/MasterData/DataMaster.vue').default;
const InputDataM = require('./components/manajer-mtc/MasterData/InputMaster.vue').default;
const DetailMasterDataM = require('./components/manajer-mtc/MasterData/DetailMaster.vue').default;
const EditMasterDataM = require('./components/manajer-mtc/MasterData/EditMaster.vue').default;
Vue.component('master-data-musnah', require('./components/manajer-mtc/MasterData/KondisiDataMaster/DataMasterMusnah.vue').default);
const DataLayoutM = require('./components/manajer-mtc/Layout/DataLayout.vue').default;
const RiwayatM = require('./components/manajer-mtc/Riwayat/DataRiwayat.vue').default;
const RiwayatPeminjamanM = require('./components/manajer-mtc/Riwayat/DataRiwayatPeminjaman.vue').default;
const RiwayatPermintaanM = require('./components/manajer-mtc/Riwayat/DataRiwayatPermintaan.vue').default;
const RiwayatPenggantianM = require('./components/manajer-mtc/Riwayat/DataRiwayatPenggantian.vue').default;
const PeminjamanM  = require('./components/manajer-mtc/Peminjaman&Permintaan/Peminjaman/DataPeminjamanAlat.vue').default;
Vue.component('data-permintaanM', require('./components/manajer-mtc/Peminjaman&Permintaan/Permintaan/DataPermintaanAlat.vue').default);
const DetailPeminjamanM = require('./components/manajer-mtc/Peminjaman&Permintaan/Peminjaman/DetailPeminjamanAlat.vue').default;
const DetailPermintaanM = require('./components/manajer-mtc/Peminjaman&Permintaan/Permintaan/DetailPermintaanAlat.vue').default;
const PerawatanM = require('./components/manajer-mtc/Perawatan/DataPerawatanAlat.vue').default;
const DetailPerawatanM = require('./components/manajer-mtc/Perawatan/DetailPerawatan.vue').default;

// LOGIN
const Login = require('./components/auth/login.vue').default;

// Dashboard
const Dashboard = require('./components/admin-mtc/Dashboard/dashboard.vue').default;
const DashboardM = require('./components/manajer-mtc/Dashboard/dashboard.vue').default;
const DashboardU = require('./components/user/Dashboard/dashboard.vue').default;

/**Komponen Data Alat */
const DataAlat = require('./components/admin-mtc/DataAlat.vue').default;
const InputAlat = require('./components/admin-mtc/InputAlat.vue').default;
const DetailAlat = require('./components/admin-mtc/DetailAlat.vue').default;
const EditAlat = require('./components/admin-mtc/UpdateAlat.vue').default;
//Komponen Alat Belum Digunakan
const InputAlatBelumDigunakan = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatBelumDigunakan/InputDataAlatBelumDigunakan.vue').default;
const EditAlatBelumDigunakan = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatBelumDigunakan/EditDataAlatBelumDigunakan.vue').default;
const DetailAlatBelumDigunakan = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatBelumDigunakan/DetailDataAlatBelumDigunakan.vue').default;
const DetailRincianAlatBelumDigunakan = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatBelumDigunakan/DetailRincianDataAlatBelumDigunakan.vue').default;
const DetailRiwayatRusak = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatBelumDigunakan/Kondisi/RiwayatRusak.vue').default;
const DetailRiwayatMusnah = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatBelumDigunakan/Kondisi/RiwayatMusnah.vue').default;
const DetailRiwayatHilang = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatBelumDigunakan/Kondisi/RiwayatHilang.vue').default;
//Komponen Alat Sudah Digunakan
const EditAlatSudahDigunakan = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatSudahDigunakan/EditAlatSudahDigunakan.vue').default;
//Komponen Alat Dipinjam
const EditAlatDipinjam = require('./components/admin-mtc/DataDetailAlat/DataRincianAlat/DetailRincianAlatDipinjam/EditAlatDipinjam.vue').default;

/**Komponen Data Alat Error */
const DataAlatError = require('./components/admin-mtc/Alat/Error/DataAlatError.vue').default;
const InputAlatError = require('./components/admin-mtc/Alat/Error/InputDataAlatError.vue').default;
const EditAlatError = require('./components/admin-mtc/Alat/Error/EditAlatError.vue').default;
/**Komponen Data Alat Rusak */
const InputAlatRusak = require('./components/admin-mtc/Alat/Rusak/InputDataAlatRusak.vue').default;
const EditAlatRusak = require('./components/admin-mtc/Alat/Rusak/EditDataAlatRusak.vue').default;
/**komponen Data Alat Musnah */
const InputAlatMusnah = require('./components/admin-mtc/Alat/Musnah/InputDataAlatMusnah.vue').default;
/**komponen Data Alat Hilang */
const InputAlatHilang = require('./components/admin-mtc/Alat/Hilang/InputDataAlatHilang.vue').default;
const EditAlatHilang = require('./components/admin-mtc/Alat/Hilang/EditDataAlatHilang.vue').default;

/**Komponen Data Peminjaman Alat */
const DataPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/DataPeminjamanAlat.vue').default;
const EditPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/EditPeminjamanAlat.vue').default;
const DetailPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/DetailPeminjamanAlat.vue').default;
const DataRincianPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/DataRincianPeminjamanAlat.vue').default;
const DetailPengeluaranPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/DetailPengeluaranPeminjamanAlat.vue').default;
const DetailPengajuanPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/DetailPengajuanPeminjamanAlat.vue').default;
const DetailPerubahanPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/DetailPerubahanPeminjaman.vue').default;
const DetailPengembalianPeminjamanAlat = require('./components/admin-mtc/PeminjamanAlat/DetailPengembalianPeminjamanAlat.vue').default;

// Permintaan Alat
const DataPermintaanAlat = require('./components/admin-mtc/PeminjamanAlat/PermintaanAlat/DataPermintaanAlat.vue').default;
const DetailPermintaanAlat = require('./components/admin-mtc/PeminjamanAlat/PermintaanAlat/DetailPermintaanAlat.vue').default;
const DataRincianPermintaanAlat = require('./components/admin-mtc/PeminjamanAlat/PermintaanAlat/DataRincianPermintaanAlat.vue').default;
const DataPengajuanPermintaanalat = require('./components/admin-mtc/PeminjamanAlat/PermintaanAlat/DetailPengajuanPermintaanAlat.vue').default;

//Komponen Perawatan Alat
const DataPerawatanAlat = require('./components/admin-mtc/PerawatanAlat/DataPerawatanAlat.vue').default;
const InputPerawatanAlat = require('./components/admin-mtc/PerawatanAlat/InputPerawatanAlat.vue').default;
const EditPerawatanAlat = require('./components/admin-mtc/PerawatanAlat/EditPerawatanAlat.vue').default;
const DetailPerawatanAlat = require('./components/admin-mtc/PerawatanAlat/DetailPerawatan.vue').default;
const DetailRincianPerawatanAlat = require('./components/admin-mtc/PerawatanAlat/RincianPerawatan.vue').default;
const EditRincianPerawatanAlat = require('./components/admin-mtc/PerawatanAlat/EditRincianPerawatanAlat.vue').default;
// BARU
const BaruPerawatan = require('./components/admin-mtc/PerawatanAlat/Kondisi/Baru/index.vue').default;
const DetailBaruPerawatan = require('./components/admin-mtc/PerawatanAlat/Kondisi/Baru/detail.vue').default;
const ProsesPerawatan = require('./components/admin-mtc/PerawatanAlat/Kondisi/Proses/index.vue').default;
const DetailProsesPerawatan = require('./components/admin-mtc/PerawatanAlat/Kondisi/Proses/detail.vue').default;
const SelesaiPerawatan = require('./components/admin-mtc/PerawatanAlat/Kondisi/Selesai/index.vue').default;
const DetailSelesaiPerawatan = require('./components/admin-mtc/PerawatanAlat/Kondisi/Selesai/detail.vue').default;
// Jadwal Perawatan
const TabelPerawatan = require('./components/admin-mtc/PerawatanAlat/JadwalPerawatan/Tabel.vue').default;
const TabelPerencanaanPerawatan = require('./components/admin-mtc/PerawatanAlat/JadwalPerawatan/Perencanaan/Tabel.vue').default;
const KalenderPerawatan = require('./components/admin-mtc/PerawatanAlat/JadwalPerawatan/Kalender.vue').default;
const KalenderPerawatanBulan = require('./components/admin-mtc/PerawatanAlat/JadwalPerawatan/KalenderBulan.vue').default;
const KalenderPerencanaanPerawatan = require('./components/admin-mtc/PerawatanAlat/JadwalPerawatan/Perencanaan/Kalender.vue').default;
const RiwayatPerawatan = require('./components/admin-mtc/PerawatanAlat/JadwalPerawatan/RiwayatPerawatan.vue').default;

/**Komponen Data Mesin */
const DataMesin = require('./components/admin-mtc/DataMesin/MasterData/DataMesin.vue').default;
const InputMesin = require('./components/admin-mtc/DataMesin/MasterData/InputDataMesin.vue').default;
const EditMesin = require('./components/admin-mtc/DataMesin/MasterData/EditDataMesin.vue').default;
const DetailMesin = require('./components/admin-mtc/DataMesin/DetailMesin/DetailMesin.vue').default;
const DataRincianMesinBelumDigunakan = require('./components/admin-mtc/DataMesin/DetailMesin/DataRincianMesinBelumDigunakan.vue').default;
const DataRincianMesinSudahDigunakan = require('./components/admin-mtc/DataMesin/DetailMesin/DataRincianMesinSudahDigunakan.vue').default;
const DataRincianMesinPeminjaman = require('./components/admin-mtc/DataMesin/DetailMesin/DataRincianPeminjamanMesin.vue').default;
const DataDetailMesinError = require('./components/admin-mtc/DataMesin/DetailMesin/Kondisi/DataError.vue').default;
const DataDetailMesinRusak = require('./components/admin-mtc/DataMesin/DetailMesin/Kondisi/DataRusak.vue').default;
const DataDetailMesinMusnah = require('./components/admin-mtc/DataMesin/DetailMesin/Kondisi/DataMusnah.vue').default;
const PeminjamanMesin = require('./components/admin-mtc/DataMesin/Peminjaman/PeminjamanMesin.vue').default;
const InputDataMesinBelumDigunakan = require('./components/admin-mtc/DataMesin/DetailMesin/DataMesinBelumDigunakan/InputDataMesinBelumDigunakan.vue').default;
const EditDataMesinBelumDigunakan = require('./components/admin-mtc/DataMesin/DetailMesin/DataMesinBelumDigunakan/EditDataMesinBelumDigunakan.vue').default;
const DetailDataMesinBelumDigunakan = require('./components/admin-mtc/DataMesin/DetailMesin/DataMesinBelumDigunakan/DetailDataMesinBelumDigunakan.vue').default;
const DetailRincianDataMesinBelumDigunakan = require('./components/admin-mtc/DataMesin/DetailMesin/DataMesinBelumDigunakan/DetailRincianRiwayatMesinBelumDigunakan.vue').default;
const DetailRiwayatMesinRusak = require('./components/admin-mtc/DataMesin/DetailMesin/DataMesinBelumDigunakan/Kondisi/RiwayatMesinRusak.vue').default;
const DetailRiwayatMesinMusnah = require('./components/admin-mtc/DataMesin/DetailMesin/DataMesinBelumDigunakan/Kondisi/RiwayatMesinMusnah.vue').default;
const DetailRiwayatMesinHilang = require('./components/admin-mtc/DataMesin/DetailMesin/DataMesinBelumDigunakan/Kondisi/RiwayatMesinHilang.vue').default;
const DataPerawatanMesin = require('./components/admin-mtc/DataMesin/DetailMesin/Perawatan/DataPerawatanMesin.vue').default;

//Komponen Data Riwayat
const DataRiwayat = require('./components/admin-mtc/Riwayat/DataRiwayat.vue').default;
const DataRiwayatPeminjaman = require('./components/admin-mtc/Riwayat/DataRiwayatPeminjaman.vue').default;
const DataRiwayatPermintaan = require('./components/admin-mtc/Riwayat/DataRiwayatPermintaan.vue').default;
const DataRiwayatPenggantian = require('./components/admin-mtc/Riwayat/DataRiwayatPenggantian.vue').default;

//LAYOUT
const DataLayout = require('./components/admin-mtc/Layout/DataLayout.vue').default;

//Inventory
const KategoriM = require('./components/manajer-mtc/AlatdanMesin/KategoriAlatdanMesin.vue').default;
const Kategori = require('./components/admin-mtc/AlatdanMesin/KategoriAlatdanMesin.vue').default;
const Merek = require('./components/admin-mtc/AlatdanMesin/MerekAlatdanMesin.vue').default;
const MerekM = require('./components/manajer-mtc/AlatdanMesin/MerekAlatdanMesin.vue').default;
const Tipe = require('./components/admin-mtc/AlatdanMesin/TipeAlatdanMesin.vue').default;
const TipeM = require('./components/manajer-mtc/AlatdanMesin/TipeAlatdanMesin.vue').default;

//USER DATA MASTER
const DataMasterUser = require('./components/user/MasterData/Data.vue').default;

// USER PEMINJAMAN & PERMINTAAN
const UserDataPeminjaman = require('./components/user/PeminjamanAlat/DataPeminjamanAlat.vue').default;
const UserInputPeminjaman = require('./components/user/PeminjamanAlat/InputPeminjamanAlat.vue').default;
const UserDetailPeminjaman = require('./components/user/PeminjamanAlat/DetailPeminjamanAlat.vue').default;
const UserDetailPengeluaran = require('./components/user/PeminjamanAlat/DetailPengeluaranPeminjamanAlat.vue').default;
const UserDetailPengajuan = require('./components/user/PeminjamanAlat/DetailPengajuanPeminjamanAlat.vue').default;
const UserDetailPerubahan = require('./components/user/PeminjamanAlat/DetailPerubahanPeminjaman.vue').default;
const UserDetailPengembalian = require('./components/user/PeminjamanAlat/DetailPengembalianPeminjamanAlat.vue').default;
const UserDetaiPeminjaman = require('./components/user/PeminjamanAlat/DataRincianPeminjamanAlat.vue').default;
const UserDataPermintaan = require('./components/user/PeminjamanAlat/PermintaanAlat/DataPermintaanAlat.vue').default;
const UserInputPermintaan = require('./components/user/PeminjamanAlat/PermintaanAlat/InputPermintaan.vue').default;
const UserDetailPermintaan  = require('./components/user/PeminjamanAlat/PermintaanAlat/DetailPermintaanAlat.vue').default;
const UserPermintaanPengeluaran = require('./components/user/PeminjamanAlat/PermintaanAlat/DataRincianPermintaanAlat.vue').default;
const UserInputPerubahanPeminjaman = require('./components/user/PeminjamanAlat/InputPerubahanPeminjaman.vue').default;

// KONDISI ERROR
const BaruError = require('./components/kondisi/Error/Baru-Error.vue').default;
const ProsesError = require('./components/kondisi/Error/Proses/Proses-Error.vue').default;
const DetailProsesError = require('./components/kondisi/Error/Proses/Detail-Proses-Error.vue').default;
const SelesaiError = require('./components/kondisi/Error/Selesai/Selesai-Error.vue').default;
const DetailSelesaiError = require('./components/kondisi/Error/Selesai/Detail-Selesai-Error.vue').default;

//KONDISI RUSAK
const BaruRusak = require('./components/kondisi/Rusak/Baru-Rusak.vue').default;
const DetailBaruRusak = require('./components/kondisi/Rusak/Detail-Baru-Rusak.vue').default;
const SelesaiRusak = require('./components/kondisi/Rusak/Selesai/Selesai-Rusak.vue').default;
const DetailSelesaiRusak = require('./components/kondisi/Rusak/Selesai/Detail-Selesai-Rusak.vue').default;
const MBaruRusak = require('./components/kondisi/Manager/Rusak/Baru-Rusak.vue').default;
const MDetailBaruRusak = require('./components/kondisi/Manager/Rusak/Detail-Baru-Rusak.vue').default;

//KONDISI MUSNAH
const BaruMusnah = require('./components/kondisi/Musnah/Baru-Musnah.vue').default;
const ProsesMusnah = require('./components/kondisi/Musnah/Proses/Proses-Musnah.vue').default;
const DetailProsesMusnah = require('./components/kondisi/Musnah/Proses/Detail-Proses-Musnah.vue').default;
const SelesaiMusnah = require('./components/kondisi/Musnah/Selesai/Selesai.Musnah.vue').default;
const DetailSelesaiMusnah = require('./components/kondisi/Musnah/Selesai/Detail-Selesai-Musnah.vue').default;
const MProsesMusnah = require('./components/kondisi/Manager/Rusak/Proses/Proses-Musnah.vue').default;
const MDetailProsesMusnah = require('./components/kondisi/Manager/Rusak/Proses/Detail-Proses-Musnah.vue').default;
const MSelesaiMusnah = require('./components/kondisi/Manager/Rusak/Selesai/Selesai.Musnah.vue').default;
const MDetailSelesaiMusnah = require('./components/kondisi/Manager/Rusak/Selesai/Detail-Selesai-Musnah.vue').default;

// Data Hilang
const BaruHilang = require('./components/kondisi/Hilang/Baru-Hilang.vue').default;
const DetailBaruHilang = require('./components/kondisi/Hilang/Detail-Baru-Hilang.vue').default;
const ProsesHilang = require('./components/kondisi/Hilang/Proses/Proses-Hilang.vue').default;
const DetailProsesHilang = require('./components/kondisi/Hilang/Proses/Detail-Proses_Hilang.vue').default;
const SelesaiHilang = require('./components/kondisi/Hilang/Selesai/Selesai-Hilang.vue').default;
const DetailSelesaiHilang = require('./components/kondisi/Hilang/Selesai/Detail-Selesai-Hilang.vue').default;
const UBaruHilang = require('./components/kondisi/User/Hilang/Baru-Hilang.vue').default;
const UDetailBaruHilang = require('./components/kondisi/User/Hilang/Detail-Baru-Hilang.vue').default;
const UProsesHilang = require('./components/kondisi/User/Hilang/Proses/Proses-Hilang.vue').default;
const UDetailProsesHilang = require('./components/kondisi/User/Hilang/Proses/Detail-Proses_Hilang.vue').default;
const USelesaiHilang = require('./components/kondisi/User/Hilang/Selesai/Selesai-Hilang.vue').default;
const UDetailSelesaiHilang = require('./components/kondisi/User/Hilang/Selesai/Detail-Selesai-Hilang.vue').default;

//Daftarkan Komponen DataAlatError
Vue.component('input-alat-error', InputAlatError);
Vue.component('edit-alat-error', EditAlatError);
//Daftarkan Komponen DataAlatRusak
Vue.component('input-alat-rusak', InputAlatRusak);
Vue.component('edit-alat-rusak', EditAlatRusak);
//Daftarkan Komponen DataAlatMusnah
Vue.component('input-alat-musnah', InputAlatMusnah);
//Daftarkan Komponen DataAlathilang
Vue.component('input-alat-hilang', InputAlatHilang);
Vue.component('edit-alat-hilang', EditAlatHilang);

//Daftarkan Komponen Alat Belum Digunakan
Vue.component('input-alat-belum-digunakan', InputAlatBelumDigunakan);
Vue.component('edit-alat-belum-digunakan', EditAlatBelumDigunakan);
Vue.component('detail-rincian-alat-belum-digunakan', DetailRincianAlatBelumDigunakan);
Vue.component('detail-riwayat-rusak', DetailRiwayatRusak);
Vue.component('detail-riwayat-musnah', DetailRiwayatMusnah);
Vue.component('detail-riwayat-hilang', DetailRiwayatHilang);

//Daftarkan Komponen Alat Sudah Digunakan
Vue.component('edit-alat-sudah-digunakan', EditAlatSudahDigunakan);

//Daftarkan Komponen Perawatan Alat
Vue.component('input-perawatan-alats', InputPerawatanAlat);
Vue.component('edit-perawatan-alat', EditPerawatanAlat);
Vue.component('detail-rincian-perawatan-alat', DetailRincianPerawatanAlat);
Vue.component('edit-rincian-perawatan-alat', EditRincianPerawatanAlat);

//Daftarkan Komponen Alat Dipinjam
Vue.component('edit-alat-dipinjam', EditAlatDipinjam);
Vue.component('edit-peminjaman-alat', EditPeminjamanAlat);
Vue.component('data-rincian-peminjaman-alat', DataRincianPeminjamanAlat);
Vue.component('pengeluaran-peminjaman', DetailPengeluaranPeminjamanAlat);
Vue.component('pengajuan-peminjaman', DetailPengajuanPeminjamanAlat);
Vue.component('perubahan-peminjaman', DetailPerubahanPeminjamanAlat);
Vue.component('pengembalian-peminjaman', DetailPengembalianPeminjamanAlat);

//Permintaan Alat
Vue.component('data-permintaan-alat', DataPermintaanAlat);
Vue.component('data-rincian-permintaan-alat', DataRincianPermintaanAlat);
Vue.component('data-pengajuan', DataPengajuanPermintaanalat);

//Daftarkan komponen Data Mesin
Vue.component('data-mesin', DataMesin);
Vue.component('input-data-mesin', InputMesin);
Vue.component('edit-data-mesin', EditMesin);
Vue.component('detail-mesin', DetailMesin);
Vue.component('data-rincian-mesin-belum-digunakan', DataRincianMesinBelumDigunakan);
Vue.component('data-rincian-mesin-sudah-digunakan', DataRincianMesinSudahDigunakan);
Vue.component('data-rincian-peminjaman-mesin', DataRincianMesinPeminjaman);
Vue.component('data-detail-mesin-error', DataDetailMesinError);
Vue.component('data-detail-mesin-rusak', DataDetailMesinRusak);
Vue.component('data-detail-mesin-musnah', DataDetailMesinMusnah);
Vue.component('peminjaman-mesin', PeminjamanMesin);
Vue.component('input-data-mesin-belum-digunakan', InputDataMesinBelumDigunakan);
Vue.component('edit-data-mesin-belum-digunakan', EditDataMesinBelumDigunakan);
Vue.component('detail-rincian-data-mesin-belum-digunakan', DetailRincianDataMesinBelumDigunakan);
Vue.component('riwayat-mesin-rusak', DetailRiwayatMesinRusak);
Vue.component('riwayat-mesin-musnah', DetailRiwayatMesinMusnah);
Vue.component('riwayat-mesin-hilang', DetailRiwayatMesinHilang);
Vue.component('data-perawatan-mesin', DataPerawatanMesin);

//Daftarkan Komponen Data Riwayat
Vue.component('data-riwayat', DataRiwayat);

//Komponen User Peminjaman & Permintaan
Vue.component('user-input-peminjaman', UserInputPeminjaman);
Vue.component('user-detail-pengajuan', UserDetailPengajuan);
Vue.component('user-detail-pengeluaran', UserDetailPengeluaran);
Vue.component('user-detail-pengembalian', UserDetailPengembalian);
Vue.component('user-detail-perubahan', UserDetailPerubahan);
Vue.component('user-detail-peminjaman', UserDetaiPeminjaman);
Vue.component('user-data-permintaan', UserDataPermintaan);
Vue.component('user-input-permintaan', UserInputPermintaan);
Vue.component('user-permintaan-pengeluaran', UserPermintaanPengeluaran);
Vue.component('user-input-perubahan-peminjaman', UserInputPerubahanPeminjaman);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const routes = [
    {
        path: '/',
        component: Login,
    },
    // Dashboard
    {
        path: '/admin-mtc/dashboard',
        component: Dashboard,
        name: 'dashboard-adminmtc',
    },
    {
        path: '/manajer-mtc/dashboard',
        component: DashboardM,
        name: 'dashboard-manajermtc',
    },
    {
        path: '/user/dashboard',
        component: DashboardU,
        name: 'dashboard-user',
    },
    // MANAJER
    {
        path: '/manajer-mtc/master-data',
        component: MasterDataM,
    },
    {
        path: '/manajer-mtc/master-data/input',
        component: InputDataM,
    },
    {
        path: '/manajer-mtc/master-data/detail/:id',
        component: DetailMasterDataM,
    },
    {
        path: '/manajer-mtc/master-data/edit/:id',
        component: EditMasterDataM,
    },
    {
      path: '/manajer-mtc/layout',
      component: DataLayoutM,
    },
    {
      path: '/manajer-mtc/peminjaman',
      component: PeminjamanM,
    },
    {
      path: '/manajer-mtc/peminjaman/detail/:id',
      component: DetailPeminjamanM,
    },
    {
      path: '/manajer-mtc/permintaan/detail/:id',
      component: DetailPermintaanM,
    },
    {
      path: '/manajer-mtc/perawatan',
      component: PerawatanM,
    },
    {
      path: '/manajer-mtc/perawatan/detail/:id',
      component: DetailPerawatanM,
    },
    // ADMIN MTC
    {
        path: '/admin-mtc/layout',
        component: DataLayout,
    },
    //Inventory
    {
        path: '/manajer-mtc/inventory',
        component: KategoriM,
        name: 'kategori-mgn',
    },
    {
        path: '/admin-mtc/inventory',
        component: Kategori,
        name: 'kategori',
    },
    {
        path: '/manajer-mtc/inventory',
        component: MerekM,
        name: 'merek-mgn',
    },
    {
        path: '/admin-mtc/inventory',
        component: Merek,
        name: 'merek',
    },
    {
        path: '/manajer-mtc/inventory',
        component: TipeM,
        name: 'tipe-mgn',
    },
    {
        path: '/admin-mtc/inventory',
        component: Tipe,
        name: 'tipe',
    },
    {
        path: '/admin-mtc/data-alat/detail/:id',
        //name: 'DetailAlat',
        component: DetailAlat,
        //props: true
    },
    {
        path: '/admin-mtc/data-alat',
        component: DataAlat,
    },
    {
        path: '/admin-mtc/data-alat/edit/:id',
        component: EditAlat,
    },
    {
        path: '/admin-mtc/data-alat/input',
        component: InputAlat,
    },
    {
        path: '/admin-mtc/data-alat/belum-digunakan/input',
        component: InputAlatBelumDigunakan,
    },
    {
        path: '/admin-mtc/data-alat/belum-digunakan/edit/:id',
        component: EditAlatBelumDigunakan,
    },
    {
        path: '/admin-mtc/data-alat/belum-digunakan/detail/:id',
        component: DetailAlatBelumDigunakan,
    },
    {
        path: '/admin-mtc/alat/data-error',
        component: DataAlatError,
    },
    {
        path: '/admin-mtc/alat/data-error/input',
        component: InputAlatError,
    },
    {
        path: '/admin-mtc/data-alat/detail-peminjaman/:id',
        component: DetailPeminjamanAlat,
    },
    {
        path: '/admin-mtc/data-alat/permintaan/detail/:id',
        component: DetailPermintaanAlat,
    },
    {
        path: '/admin-mtc/peminjaman',
        component: DataPeminjamanAlat,
    },
    {
        path: '/admin-mtc/data-mesin/edit/:id',
        component: EditMesin,
    },
    {
        path: '/admin-mtc/data-mesin/input',
        component: InputMesin,
    },
    {
        path: '/admin-mtc/data-mesin/detail/:id',
        component: DetailMesin,
    },
    {
        path: '/admin-mtc/data-mesin/belum-digunakan/detail/:id',
        component: DetailDataMesinBelumDigunakan,
    },
    // {
    //     path: '/admin-mtc/perawatan',
    //     component: DataPerawatanAlat,
    // },
    // KONDISI BARU PERAWATAN
    {
        path: '/admin-mtc/perawatan',
        component: BaruPerawatan,
        name: 'baru-perawatan',
    },
    {
        path: '/admin-mtc/perawatan/:id',
        component: DetailBaruPerawatan,
        name: 'detail-baru-perawatan',
    },
    {
        path: '/admin-mtc/perawatan/proses',
        component: ProsesPerawatan,
        name: 'proses-perawatan',
    },
    {
        path: '/admin-mtc/perawatan/proses/:id',
        component: DetailProsesPerawatan,
        name: 'detail-proses-perawatan',
    },
    {
        path: '/admin-mtc/perawatan/selesai',
        component: SelesaiPerawatan,
        name: 'selesai-perawatan',
    },
    {
        path: '/admin-mtc/perawatan/selesai/:id',
        component: DetailSelesaiPerawatan,
        name: 'detail-selesai-perawatan',
    },
    // JADWAL Perawatan
    {
        path: '/admin-mtc/jadwal-perawatan',
        component: TabelPerawatan,
        name: 'tabel-jadwal-perawatan',
    },
    {
        path: '/admin-mtc/perencanaan/jadwal-perawatan',
        component: TabelPerencanaanPerawatan,
        name: 'tabel-perencanaan-jadwal-perawatan',
    },
    {
        path: '/admin-mtc/jadwal-perawatan',
        component: KalenderPerawatan,
        name: 'kalender-perawatan',
    },
    {
        path: '/admin-mtc/jadwal-perawatan',
        component: KalenderPerawatanBulan,
        name: 'kalender-perawatan-bulan',
    },
    {
        path: '/admin-mtc/perencanaan/jadwal-perawatan',
        component: KalenderPerencanaanPerawatan,
        name: 'kalender-perencanaan-perawatan',
    },
    {
        path: '/admin-mtc/riwayat-perawatan',
        component: RiwayatPerawatan,
        name: 'riwayat-perawatan',
    },
    {
        path: '/admin-mtc/perawatan/{id}/edit',
        component: EditPerawatanAlat,
    },
    {
        path: '/admin-mtc/data-perawatan/detail/:id',
        component: DetailPerawatanAlat,
    },
    // RIWAYAT
    {
        path: '/manajer-mtc/riwayat',
        component: RiwayatM,
        name: 'data-riwayat-perkondisi-mgn',
    },
    {
        path: '/admin-mtc/riwayat',
        component: DataRiwayat,
        name: 'data-riwayat-perkondisi',
    },
    {
        path: '/manajer-mtc/riwayat',
        component: RiwayatPeminjamanM,
        name: 'data-riwayat-peminjaman-mgn',
    },
    {
        path: '/admin-mtc/riwayat',
        component: DataRiwayatPeminjaman,
        name: 'data-riwayat-peminjaman',
    },
    {
        path: '/manajer-mtc/riwayat',
        component: RiwayatPermintaanM,
        name: 'data-riwayat-permintaan-mgn'
    },
    {
        path: '/admin-mtc/riwayat',
        component: DataRiwayatPermintaan,
        name: 'data-riwayat-permintaan',
    },
    {
        path: '/manajer-mtc/riwayat',
        component: RiwayatPenggantianM,
        name: 'data-riwayat-penggantian-mgn',
    },
    {
        path: '/admin-mtc/riwayat',
        component: DataRiwayatPenggantian,
        name: 'data-riwayat-penggantian',
    },
    // USER
    {
        path: '/user/data',
        component: DataMasterUser,
    },
    {
        path: '/user-mtc/data',
        component: DataMasterUser,
    },
    {
        path: '/user/peminjaman',
        component: UserDataPeminjaman,
    },
    {
        path: '/user-mtc/peminjaman',
        component: UserDataPeminjaman,
    },
    {
        path: '/user/peminjaman/detail/:id',
        component: UserDetailPeminjaman,
    },
    {
        path: '/user/permintaan/detail/:id',
        component: UserDetailPermintaan,
    },
    //KONDISI ERROR
    {
      path: '/kondisi-error',
      component: BaruError,
      name: 'kondisi-baru-error',
    },
    {
      path: '/admin-mtc/kondisi-error/proses',
      component: ProsesError,
      name: 'kondisi-proses-error',
    },
    {
      path: '/admin-mtc/kondisi-error/proses/:id',
      component: DetailProsesError,
      name: 'kondisi-detail-proses-error',
    },
    {
      path: '/admin-mtc/kondisi-error/selesai',
      component: SelesaiError,
      name: 'kondisi-selesai-error',
    },
    {
        path: '/admin-mtc/kondisi-error/selesai/:id',
        component: DetailSelesaiError,
        name: 'kondisi-detail-selesai-error',
    },
    //KONDISI RUSAK
    {
        path: '/manajer-mtc/kondisi-rusak',
        component: MBaruRusak,
        name: 'm-kondisi-baru-rusak',
    },
    {
        path: '/manajer-mtc/kondisi-rusak/:id',
        component: MDetailBaruRusak,
        name: 'm-kondisi-detail-rusak',
    },
    {
        path: '/kondisi-rusak',
        component: BaruRusak,
        name: 'kondisi-baru-rusak',
    },
    {
        path: '/admin-mtc/kondisi-rusak/:id',
        component: DetailBaruRusak,
        name: 'kondisi-detail-baru-rusak',
    },
    {
        path: '/admin-mtc/kondisi-rusak/selesai',
        component: SelesaiRusak,
        name: 'kondisi-selesai-rusak',
    },
    {
        path: '/admin-mtc/kondisi-rusak/selesai/:id',
        component: DetailSelesaiRusak,
        name: 'kondisi-detail-selesai-rusak',
    },
    //KONDISI MUSNAH
    {
        path: '/kondisi-musnah',
        component: BaruMusnah,
        name: 'kondisi-baru-musnah',
    },
    {
        path: '/admin-mtc/kondisi-musnah/proses',
        component: ProsesMusnah,
        name: 'kondisi-proses-musnah',
    },
    {
        path: '/admin-mtc/kondisi-musnah/proses/:id',
        component: DetailProsesMusnah,
        name: 'kondisi-detail-proses-musnah',
    },
    {
        path: '/admin-mtc/kondisi-musnah/selesai',
        component: SelesaiMusnah,
        name: 'kondisi-selesai-musnah',
    },
    {
        path: '/admin-mtc/kondisi-musnah/selesai/:id',
        component: DetailSelesaiMusnah,
        name: 'kondisi-detail-selesai-musnah',
    },
    {
        path: '/manajer-mtc/kondisi-musnah/proses',
        component: MProsesMusnah,
        name: 'm-kondisi-proses-musnah',
    },
    {
        path: '/manajer-mtc/kondisi-musnah/:id',
        component: MDetailProsesMusnah,
        name: 'm-kondisi-detail-proses-musnah',
    },
    {
        path: '/manajer-mtc/kondisi-musnah/selesai',
        component: MSelesaiMusnah,
        name: 'm-kondisi-selesai-musnah',
    },
    {
        path: '/manajer-mtc/kondisi-musnah/selesai/:id',
        component: MDetailSelesaiMusnah,
        name: 'm-kondisi-detail-selesai-musnah',
    },
    // DATA HILANG
    {
        path: '/admin-mtc/data-hilang',
        component: BaruHilang,
        name: 'data-baru-hilang',
    },
    {
        path: '/admin-mtc/data-hilang/:id',
        component: DetailBaruHilang,
        name: 'data-detail-baru-hilang',
    },
    {
        path: '/admin-mtc/data-hilang/proses',
        component: ProsesHilang,
        name: 'data-proses-hilang',
    },
    {
        path: '/admin-mtc/data-hilang/proses/:id',
        component: DetailProsesHilang,
        name: 'data-detail-proses-hilang',
    },
    {
        path: '/admin-mtc/data-hilang/selesai',
        component: SelesaiHilang,
        name: 'data-selesai-hilang',
    },
    {
        path: '/admin-mtc/data-hilang/selesai/:id',
        component: DetailSelesaiHilang,
        name: 'data-detail-selesai-hilang',
    },
    {
        path: '/user/data-hilang/',
        component: UBaruHilang,
        name: 'user-data-baru-hilang',
    },
    {
        path: '/user/data-hilang/:id',
        component: UDetailBaruHilang,
        name: 'user-data-detail-baru-hilang',
    },
    {
        path: '/user/data-hilang/proses',
        component: UProsesHilang,
        name: 'user-data-proses-hilang',
    },
    {
        path: '/user/data-hilang/proses/:id',
        component: UDetailProsesHilang,
        name: 'user-data-detail-proses-hilang',
    },
    {
        path: '/user/data-hilang/selesai',
        component: USelesaiHilang,
        name: 'user-data-selesai-hilang',
    },
    {
        path: '/user/data-hilang/selesai/:id',
        component: UDetailSelesaiHilang,
        name: 'user-data-detail-selesai-hilang',
    },
];

const router = new VueRouter({
    mode: 'history',
    routes,
})


const app = new Vue({
    el: '#app',
    router,
});

