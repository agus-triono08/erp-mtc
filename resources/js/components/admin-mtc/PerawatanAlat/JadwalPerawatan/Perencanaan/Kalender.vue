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
        <router-link id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="false" class="nav-link" :class="{ active: $route.name === 'tabel-perencanaan-jadwal-perawatan' }" :to="{ name: 'tabel-perencanaan-jadwal-perawatan' }">Tabel</router-link>
      </li>
      <li role="presentation" class="nav-item">
        <router-link id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true" class="nav-link" :class="{ active: $route.name === 'kalender-perencanaan-perawatan' }" :to="{ name: 'kalender-perencanaan-perawatan' }">Kalender</router-link>
      </li>
    </ul>
    <div class="row align-items-center justify-content-end mr-3">
      <button class="btn btn-sm btn-outline-primary mr-2" @click="exportToExcel"><i class="bi bi-filetype-exe"></i>Export</button>
    </div>    
    <div class="mb-5 mt-2 mr-3" id="calendar"></div>
  </div>
</template>

<script>
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import * as XLSX from 'xlsx';
import multiMonthPlugin from '@fullcalendar/multimonth';
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';
import axios from 'axios';

export default {
  data() {
    return {
      jadwalPerawatan: []
    }
  },
  mounted() {
    this.fetchData();
  },
  methods: {
    async fetchData() {
      try {
        const params = {
          all: ''
        };
        const response = await axios.get('/api/v1/perawatan', { params });
        this.jadwalPerawatan = response.data;
        this.renderCalendar();
      } catch (error) {
        console.error(error);
      }
    },
    renderCalendar() {
      const calendarEl = document.getElementById('calendar');
      const calendar = new Calendar(calendarEl, {
        plugins: [multiMonthPlugin, dayGridPlugin, timeGridPlugin, listPlugin, resourceTimeGridPlugin],
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        initialDate: new Date(),
        initialView: 'multiMonthYear',
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        navLinks: true,
        editable: false,
        dayMaxEvents: true,
        locale: 'id',
        hiddenDays: [0, 6],
        slotMinTime: '08:00',
        slotMaxTime: '17:00',
        resourceAreaWidth: '200px',
        resourceAreaHeaderContent: 'PIC',
        
        // Konfigurasi untuk menghilangkan jam di list view
        displayEventTime: false, // Menyembunyikan waktu di semua view
        views: {
          listWeek: {
            displayEventTime: false, // Khusus untuk list view
            listDayFormat: { 
              month: 'long', 
              day: 'numeric', 
              year: 'numeric',
              omitCommas: false 
            },
            listDaySideFormat: false // Menghilangkan hari di samping
          }
        },
        
        resources: this.jadwalPerawatan.map((item) => ({
          id: item.id,
          title: item.no_seri?.tools?.nama || 'Tidak Diketahui',
        })),
        
        events: this.jadwalPerawatan.map((item) => {
          const alat = item.no_seri?.tools;
          const noSeri = item.no_seri?.no_seri;

          const startDate = item.tgl_mulai_perawatan ? new Date(item.tgl_mulai_perawatan) : new Date(item.tgl_perawatan);
          const endDate = item.tgl_selesai_perawatan ? new Date(item.tgl_selesai_perawatan) : new Date(item.tgl_perawatan);

          if (item.waktu_mulai) {
            const [startHour, startMin] = item.waktu_mulai.split(':');
            startDate.setHours(startHour, startMin);
          }
          if (item.waktu_selesai) {
            const [endHour, endMin] = item.waktu_selesai.split(':');
            endDate.setHours(endHour, endMin);
          }

          return {
            title: `${alat?.nama || 'Alat'} - ${noSeri || ''} - No. Perawatan: ${item.no_perawatan}`,
            start: startDate,
            end: endDate,
            allDay: false,
            resourceId: item.id,
            backgroundColor:
              item.status === 'Belum Dilakukan Perawatan' ? '#dc3545'
              : item.status === 'Pelaksanaan' ? '#169ea8'
              : '#28a745',
            borderColor:
              item.status === 'Belum Dilakukan Perawatan' ? '#dc3545'
              : item.status === 'Pelaksanaan' ? '#169ea8'
              : '#28a745',
            display: 'block',
          };
        }),
        
        // Custom event render untuk kontrol lebih detail
        eventContent: function(arg) {
          // Jika view adalah list, tampilkan tanpa waktu
          if (arg.view.type === 'listWeek') {
            return { html: `<div class="fc-list-event-title">${arg.event.title}</div>` };
          }
          // Untuk view lainnya, tampilkan normal
          return {
            html: `<div class="fc-event-title-container">
                     <div class="fc-event-time">${arg.timeText}</div>
                     <div class="fc-event-title">${arg.event.title}</div>
                   </div>`
          };
        }
      });

      calendar.render();
    },
    
    exportToExcel() {
      const tahun = new Date().getFullYear();

      const data = this.jadwalPerawatan
        .filter(item => new Date(item.tgl_perawatan).getFullYear() === tahun)
        .map(item => {
          const alat = item.no_seri?.tools || {};
          return {
            'No Perawatan' : item.no_perawatan || '-',
            'Nama Alat': alat.nama || 'Tidak Diketahui',
            'No Seri': item.no_seri?.no_seri || 'Tidak Diketahui',
            'Tanggal Perawatan': item.tgl_perawatan || '-',
            'Waktu Mulai': item.waktu_mulai || '-',
            'Waktu Selesai': item.waktu_selesai || '-',
            'Status': item.status || '-',
            'Fungsi': alat.fungsi || '-',
            'Deskripsi': alat.deskripsi || '-',
            'Vendor': alat.vendor || '-',
            'Sumber': alat.sumber || '-',
            'Pembelian': alat.pembelian || '-',
          };
        });

      const worksheet = XLSX.utils.json_to_sheet(data);
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, 'Jadwal Perawatan');

      XLSX.writeFile(workbook, `Jadwal_Perawatan_${tahun}.xlsx`);
    }
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
  
  /* Menghilangkan waktu di list view */
  .fc-list-event-time {
    display: none !important;
  }
  
  /* Style untuk event di list view */
  .fc-list-event-title {
    font-size: 14px;
    padding: 5px;
  }
  
  @media print {
    #calendar {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }
  }
</style>