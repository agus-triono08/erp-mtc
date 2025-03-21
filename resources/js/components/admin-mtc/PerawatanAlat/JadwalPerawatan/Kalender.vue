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
        <router-link id="pills-home-tab" data-toggle="pill" data-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true" class="nav-link" :class="{ active: $route.name === 'tabel-jadwal-perawatan' }" :to="{ name: 'tabel-jadwal-perawatan' }">Tabel</router-link>
      </li>
      <li role="presentation" class="nav-item">
        <router-link id="pills-profile-tab" data-toggle="pill" data-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false" class="nav-link" :class="{ active: $route.name === 'kalender-perawatan' }" :to="{ name: 'kalender-perawatan' }">Kalender</router-link>
      </li>
    </ul>
    <div class="row align-items-center justify-content-end mr-5 mb-3">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <router-link class="nav-link" id="year-tab" data-toggle="tab" role="tab" aria-controls="year" aria-selected="true" :class="{active: $route.name === 'kalender-perawatan'}" :to="{name: 'kalender-perawatan'}">Year</router-link>
        </li>
        <li class="nav-item" role="presentation">
          <router-link class="nav-link" id="mount-tab" data-toggle="tab" role="tab" aria-controls="mount" aria-selected="false" :class="{active: $route.name === 'kalender-perawatan-bulan'}" :to="{name: 'kalender-perawatan-bulan'}">Mount</router-link>
        </li>
      </ul>
    </div>
    <div class="row mb-2 mt-2 align-items-center justify-content-center" id="head">      
        <h5 style="color: #000;"><b>{{ currentYear }}</b></h5>      
    </div>
    <!-- Export -->
    <div class="row align-items-center justify-content-end mr-5 mb-3">         
      <button class="btn btn-sm btn-outline-primary mr-2" @click="printCalendar"><i class="bi bi-printer"></i> Print</button>
      <!-- <button class="btn btn-sm btn-outline-primary"><i class="bi bi-filetype-exe"></i>Export</button> -->
    </div>    
    <!-- Main -->
    <div class="table table-responsive">      
      <v-calendar
        id="calendar-to-print"
        :columns="$screens({ default: 1, lg: 3 })"
        :rows="$screens({ default: 1, lg: 4 })"
        :is-expanded="$screens({ default: true, lg: false })"
        :from-page="{ year: 2025, month: 1 }"
        :attributes="attrs"
        class="m-5 align-items-center justify-content-center"
      />
      <v-calendar        
        :columns="$screens({ default: 1, lg: 6 })"
        :rows="$screens({ default: 1, lg: 2 })"
        :is-expanded="$screens({ default: true, lg: false })"
        :from-page="{ year: 2025, month: 1 }"
        :attributes="attrs"
        class="m-5 align-items-center justify-content-center"        
      />
    </div>
  </div>
</template>

<script>
import { Calendar } from 'v-calendar';

export default {  
  name: 'MultiPaneCalendar',
  components: {
    VCalendar: Calendar,
  },
  data() {
    return {
      attrs: [
        {
          highlight: {
            start: { fillMode: 'outline', color: 'red', contentClass: 'italic' },
            base: { fillMode: 'light', color: 'red', contentClass: 'italic' },
            end: { fillMode: 'outline', color: 'red', contentClass: 'italic' },
          },
          dates: { start: new Date(2025, 2, 17), end: new Date(2025, 2, 21) },
          popover: {
            label: 'Nama Alat: Mesin A, No. Seri: 123456',
            visibility: 'focus',
          },
        },
        {
          highlight: {
            start: { fillMode: 'outline', color: 'orange', contentClass: 'italic' },
            base: { fillMode: 'light', color: 'orange', contentClass: 'italic' },
            end: { fillMode: 'outline', color: 'orange', contentClass: 'italic' },
          },
          dates: { start: new Date(2025, 2, 24), end: new Date(2025, 2, 26) },
          popover: {
            label: 'Nama Alat: Mesin B, No. Seri: 231342',
            visibility: 'focus',
          },
        },
      ],
    }
  },
  methods: {
    printCalendar() {
      // Menyembunyikan elemen-elemen lain
      const elements = document.body.children;
      for (let i = 0; i < elements.length; i++) {
        if (elements[i].id !== 'calendar-to-print') {
          elements[i].style.display = true;
        }
      }

      // Mencetak halaman yang hanya berisi kalender
      window.print();

      // Setelah print, tampilkan kembali elemen-elemen yang disembunyikan
      for (let i = 0; i < elements.length; i++) {
        elements[i].style.display = '';
      }
    }
  },
  computed: {
    currentYear() {
      const date = new Date();
      return date.getFullYear();
    },
  }
};
</script>
<style>
@media screen {
  #calendar-to-print {
    display: none;
  }
}

@media print {
  body * {
    visibility: hidden;
  }

  #calendar-to-print,
  #calendar-to-print * {
    visibility: visible;
  }

  #head, #head * {
    visibility: visible;
  }

  #head {
    position: absolute;
    left: 50%;
    top: 0;
    transform: translate(-50%);
  }

  #calendar-to-print {
    position: absolute;
    left: 0;
    top: 0;
  }
}
</style>
