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
        <router-link id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="true" class="nav-link" :class="{ active: $route.name === 'kalender-perawatan-bulan' }" :to="{ name: 'kalender-perawatan-bulan' }">Kalender</router-link>
      </li>
    </ul>
    <!-- <div class="row align-items-center justify-content-end mr-5 mb-3">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <router-link class="nav-link" id="year-tab" data-toggle="tab" role="tab" aria-controls="year" aria-selected="false" :class="{active: $route.name === 'kalender-perawatan'}" :to="{name: 'kalender-perawatan'}">Year</router-link>
        </li>
        <li class="nav-item" role="presentation">
          <router-link class="nav-link" id="mount-tab" data-toggle="tab" role="tab" aria-controls="mount" aria-selected="true" :class="{active: $route.name === 'kalender-perawatan-bulan'}" :to="{name: 'kalender-perawatan-bulan'}">Mount</router-link>
        </li>
      </ul>
    </div> -->
    <div class="row align-items-center justify-content-end mr-3">
      <button class="btn btn-sm btn-outline-primary mr-2" @click="exportToExcel"><i class="bi bi-filetype-exe"></i>Export</button>
      <!-- <button class="btn btn-sm btn-outline-primary mr-2" @click="printCalendar"><i class="bi bi-printer"></i> Print</button> -->
    </div>    
    <div class="mb-5 mt-2 mr-3" id="calendar"></div>
  </div>
</template>
<script>
import axios from 'axios';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';
import * as XLSX from 'xlsx';
import multiMonthPlugin from '@fullcalendar/multimonth';
import resourceTimeGridPlugin from '@fullcalendar/resource-timegrid';
// import resourceDayGridPlugin from '@fullcalendar/resource-daygrid';

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
  // mixins: [mixins],
  data() {
    return {
      jadwalPerawatan: [
        // {
        //   id: 1,
        //   no_perawatan: 'R-01',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-01',
        //   tanggal_start: '2025-03-06',
        //   tanggal_end: '2025-03-10',
        //   waktu_mulai: '08:00',
        //   waktu_selesai: '08:30',
        //   pic: 'Jhon',
        //   detail: '',
        //   kondisi: '',
        //   status: 'Selesai'
        // },
        // {
        //   id: 2,
        //   no_perawatan: 'R-02',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-02',
        //   tanggal_start: '2025-03-13',
        //   tanggal_end: '2025-03-13',
        //   waktu_mulai: '08:00',
        //   waktu_selesai: '09:30',
        //   pic: 'Adam',
        //   detail: '',
        //   kondisi: '',
        //   status: 'Pelaksanaan'
        // },
        // {
        //   id: 3,
        //   no_perawatan: 'R-03',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-03',
        //   tanggal_start: '2025-03-19',
        //   tanggal_end: '2025-03-25',
        //   waktu_mulai: '08:00',
        //   waktu_selesai: '09:00',
        //   pic: 'Thomas',
        //   detail: '',
        //   kondisi: '',
        //   status: 'Belum Selesai'
        // },
        // {
        //   id: 4,
        //   no_perawatan: 'R-04',
        //   nama_alat: 'Bor',
        //   no_seri: 'B-04',
        //   tanggal_start: '2025-11-03',
        //   tanggal_end: '2025-11-05',
        //   waktu_mulai: '10:20',
        //   waktu_selesai: '11:00',
        //   pic: 'Atom',
        //   detail: '',
        //   kondisi: '',
        //   status: 'Belum Selesai'
        // },
        // tambahkan data lainnya
      ]
    }
  },
  mounted() {
    this.fetchData();
    // const calendarEl = document.getElementById('calendar');
    // const calendar = new Calendar(calendarEl, {
    //   plugins: [multiMonthPlugin, dayGridPlugin, timeGridPlugin, listPlugin, resourceTimeGridPlugin],
    //   headerToolbar: {
    //     left: 'prev,next today',
    //     center: 'title',
    //     right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    //   },
    //   initialDate: new Date(),
    //   initialView: 'resourceTimeGridDay',
    //   // initialView: 'resourceDayGridDay',
    //   schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
    //   initialView: 'multiMonthYear',
    //   navLinks: true, // can click day/week names to navigate views
    //   editable: true,
    //   dayMaxEvents: true, // allow "more" link when too many events
    //   locale: 'id', // tambahkan opsi locale
    //   hiddenDays: [0, 6],
    //   slotMinTime: '08:00',
    //   slotMaxTime: '17:00',
    //   // events: this.jadwalPerawatan.map((item) => {
    //   //   if (item.waktu_mulai && item.waktu_selesai) {
    //   //     return {
    //   //       title: `${item.nama_alat} - ${item.no_seri}`,
    //   //       start: item.tanggal_start,
    //   //       end: item.tanggal_end,
    //   //       allDay: false,
    //   //       startTime: item.waktu_mulai,
    //   //       endTime: item.waktu_selesai,
    //   //       backgroundColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
    //   //       borderColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
    //   //       display: 'block',
    //   //     }
    //   //   } else {
    //   //     return {
    //   //       title: `${item.nama_alat} - ${item.no_seri}`,
    //   //       start: item.tanggal_start,
    //   //       end: item.tanggal_end,
    //   //       allDay: true,
    //   //       backgroundColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
    //   //       borderColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
    //   //       display: 'block',
    //   //     }
    //   //   }
    //   // }),
    //   // resources: [{
    //   //   id: 'Adi',
    //   //   title: 'Adi'
    //   // }],
    //   resourceAreaWidth: '200px',
    //   resourceAreaHeaderContent: 'PIC',
    //   resources: this.jadwalPerawatan.map((item) => {
    //     const resource = {
    //       id: item.id,
    //       title: item.pic,  // pastikan pic terisi dengan benar
    //     };
    //     // console.log(resource); // Cek apakah resources terisi dengan benar
    //     return resource;
    //   }),
    //   events: this.jadwalPerawatan.map((item) => {
    //     const startDate = new Date(item.tanggal_start);
    //     const endDate = new Date(item.tanggal_end);
    //     startDate.setHours(item.waktu_mulai.split(":")[0]);
    //     startDate.setMinutes(item.waktu_mulai.split(":")[1]);
    //     endDate.setHours(item.waktu_selesai.split(":")[0]);
    //     endDate.setMinutes(item.waktu_selesai.split(":")[1]);
    //     return {
    //       title: `${item.nama_alat} - ${item.no_seri}`,
    //       start: startDate,
    //       end: endDate,
    //       allDay: false,
    //       resourceId: item.id,
    //       backgroundColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
    //       borderColor: item.status === 'Belum Selesai' ? '#dc3545' : item.status === 'Pelaksanaan' ? '#169ea8' : '#28a745',
    //       display: 'block',
    //     }
    //   }),
    //   select: (arg) => {
    //     const tanggalPerawatan = this.jadwalPerawatan.find((item) => {
    //       return (arg.start >= item.tanggal_start && arg.start <= item.tanggal_end) || (arg.end >= item.tanggal_start && arg.end <= item.tanggal_end);
    //     });
    //     if (tanggalPerawatan) {
    //       alert('Tanggal ini sudah terblokir');
    //       return false;
    //     }
    //   }
    // });
    // calendar.render();
  },
  methods: {
    async fetchData() {
      try {
        const params = {
          all: ''
        };
        const response = await axios.get('/api/v1/perawatan', { params });
        this.jadwalPerawatan = response.data;
        // console.log(this.jadwalPerawatan);
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
        resources: this.jadwalPerawatan.map((item) => ({
          id: item.id,
          title: item.no_seri?.tools?.nama || 'Tidak Diketahui',
        })),
        events: this.jadwalPerawatan.map((item) => {
          const alat = item.no_seri?.tools;
          const noSeri = item.no_seri?.no_seri;

          // Gunakan tgl_perawatan sebagai tanggal event jika tidak ada waktu mulai/selesai
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
            title: `${item.no_perawatan} - ${alat?.nama || 'Alat'} - ${noSeri || ''}`,
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
    },
    // exportToExcel() {
    //   const tahun = new Date().getFullYear();
    //   const data = this.jadwalPerawatan.filter((item) => {
    //     const tanggalStart = new Date(item.tanggal_start);
    //     return tanggalStart.getFullYear() === tahun;
    //   });

    //   const worksheet = XLSX.utils.json_to_sheet(data);
    //   const workbook = XLSX.utils.book_new();
    //   XLSX.utils.book_append_sheet(workbook, worksheet, 'Jadwal Perawatan');

    //   XLSX.writeFile(workbook, `Jadwal Perawatan ${tahun}.xlsx`);
    // },
    printCalendar() {
      // // Menyembunyikan elemen-elemen lain
      // const elements = document.body.children;
      // for (let i = 0; i < elements.length; i++) {
      //   if (elements[i].id !== 'calendar') {
      //     elements[i].style.display = true;
      //   }
      // }

      // // Mencetak halaman yang hanya berisi kalender
      // window.print();

      // // Setelah print, tampilkan kembali elemen-elemen yang disembunyikan
      // for (let i = 0; i < elements.length; i++) {
      //   elements[i].style.display = '';
      // }
      const calendarEl = document.getElementById('calendar');
      const calendarHtml = calendarEl.outerHTML;
      const printWindow = window.open('', '', 'height=600,width=800');
      printWindow.document.write('<html><head><title>Kalender Perawatan</title>');
      printWindow.document.write('<link rel="stylesheet" href="' + document.location.protocol + '//' + document.location.host + '/css/style.css">');
      printWindow.document.write('</head><body>');
      printWindow.document.write(calendarHtml);
      printWindow.document.write('</body></html>');
      printWindow.print();
      printWindow.close();
    }
  },
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
  @media print {
    /* body * {
      visibility: hidden;
    }

    #calendar,
    #calendar * {
      visibility: visible;
    }

    #calendar {
      position: absolute;
      left: 0;
      top: 0;
    } */
    #calendar {
      width: 100%;
      height: 100%;
      margin: 0;
      padding: 0;
    }
  }
</style>