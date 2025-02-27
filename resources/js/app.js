import Vue from 'vue';
import VueRouter from 'vue-router';
import { component } from 'vue/types/umd';



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

// LOGIN
const Login = require('./components/auth/login.vue').default;

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
    {
        path: '/admin-mtc/perawatan',
        component: DataPerawatanAlat,
    },
    {
        path: '/admin-mtc/perawatan/{id}/edit',
        component: EditPerawatanAlat,
    },
    {
        path: '/admin-mtc/data-perawatan/detail/:id',
        component: DetailPerawatanAlat,
    },
    {
        path: '/admin-mtc/riwayat',
        component: DataRiwayat,
    },
    // USER
    {
        path: '/user/peminjaman',
        component: UserDataPeminjaman,
    },
    {
        path: '/user/peminjaman/detail/:id',
        component: UserDetailPeminjaman,
    },
    {
        path: '/user/permintaan/detail/:id',
        component: UserDetailPermintaan,
    }
];

const router = new VueRouter({
    mode: 'history',
    routes,
})


const app = new Vue({
    el: '#app',
    router,
});

