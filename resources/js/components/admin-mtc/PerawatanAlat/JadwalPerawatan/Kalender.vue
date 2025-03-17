<template>
  <div class="container-fluid">
    <!-- Head -->
    <div class="row mb-2 align-items-center">
      <div class="col-sm-6">
        <h3 class="text-black-10 mt-5" style="font-family: Raleway; color: #000;"><b>Jadwal Perawatan Alat/Mesin</b></h3>        
      </div>
    </div>
    <ul id="pills-tab" role="tablist" class="nav nav-pills mb-3" style="margin-top: 1rem !important;">
      <li role="presentation" class="nav-item">
        <router-link id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="false" class="nav-link" :class="{ active: $route.name === 'tabel-jadwal-perawatan' }" :to="{ name: 'tabel-jadwal-perawatan' }">Tabel</router-link>
      </li>
      <li role="presentation" class="nav-item">
        <router-link id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true" class="nav-link" :class="{ active: $route.name === 'kalender-perawatan' }" :to="{ name: 'kalender-perawatan' }">Kalender</router-link>
      </li>
    </ul>
    <div class="mb-5 mt-2 mr-3" id="calendar"></div>
  </div>
</template>
<script>
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

// Tambahkan kode berikut untuk mendefinisikan nama bulan Indonesia
const locale = {
  id: 'id',
  firstDay: 0, // hari pertama dalam seminggu (0 = Minggu, 1 = Senin)
  dayNames: ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
  dayNamesShort: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
  monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
  monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
  titleFormat: { year: 'numeric', month: 'long', day: 'numeric' },
};

export default {
  data() {
    return {
      jadwalPerawatan: [
        {
          id: 1,
          no_perawatan: 'R-01',
          nama_alat: 'Bor',
          no_seri: 'B-01',
          tanggal_start: new Date(new Date().getFullYear(), new Date().getMonth(), 6),
          tanggal_end: new Date(new Date().getFullYear(), new Date().getMonth(), 10),
          waktu_mulai: '',
          waktu_selesai: '',
          pic: '',
          detail: '',
          kondisi: '',
          status: 'Selesai'
        },
        {
          id: 2,
          no_perawatan: 'R-02',
          nama_alat: 'Bor',
          no_seri: 'B-02',
          tanggal_start: new Date(new Date().getFullYear(), new Date().getMonth(), 13),
          tanggal_end: new Date(new Date().getFullYear(), new Date().getMonth(), 15),
          waktu_mulai: '',
          waktu_selesai: '',
          pic: '',
          detail: '',
          kondisi: '',
          status: 'Pelaksanaan'
        },
        {
          id: 3,
          no_perawatan: 'R-03',
          nama_alat: 'Bor',
          no_seri: 'B-03',
          tanggal_start: new Date(new Date().getFullYear(), new Date().getMonth(), 19),
          tanggal_end: new Date(new Date().getFullYear(), new Date().getMonth(), 25),
          waktu_mulai: '',
          waktu_selesai: '',
          pic: '',
          detail: '',
          kondisi: '',
          status: 'Belum Selesai'
        },
        // tambahkan data lainnya
      ]
    }
  },
  mounted() {
    const calendarEl = document.getElementById('calendar');
    const calendar = new Calendar(calendarEl, {
      plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      initialDate: new Date(),
      navLinks: true, // can click day/week names to navigate views
      editable: true,
      dayMaxEvents: true, // allow "more" link when too many events
      locale: 'id', // tambahkan opsi locale
      hiddenDays: [0, 6],
      events: this.jadwalPerawatan.map((item) => {
        return {
          title: `${item.nama_alat} - ${item.no_seri}`,
          start: item.tanggal_start,
          end: item.tanggal_end,
          allDay: true,
          backgroundColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
          borderColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
          display: 'block',
        }
      }),
      select: (arg) => {
        const tanggalPerawatan = this.jadwalPerawatan.find((item) => {
          return (arg.start >= item.tanggal_start && arg.start <= item.tanggal_end) || (arg.end >= item.tanggal_start && arg.end <= item.tanggal_end);
        });
        if (tanggalPerawatan) {
          alert('Tanggal ini sudah terblokir');
          return false;
        }
      }
    });
    calendar.render();
  }
}
</script>
<style>
  #calendar {
    width: 100%;
    height: 600px;
  }
  #pills-tab .nav-link {
    color: #000;
  }

  #pills-tab .nav-link.active {
    background-color: #169ea8;
    color: #fff;
  }
</style>