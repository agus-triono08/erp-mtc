<template>
  <div class="container-fluid mt-3 mr-3 mb-3">
    <h1 class="h3 mb-4 text-gray-900"><b>Dashboard</b></h1>
    <!-- Card 1 -->
    <div class="row">
			<!-- Jumlah Stok Tools <= 1 -->
      <div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2 mr-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Jumlah Stok Di Bawah Batas Minimum
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ lowStockCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-graph-down-arrow fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
			<!-- Peminjaman -->
			<div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Peminjamanan Alat/Mesin yang Menunggu Persetujuan
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ peminjamanStokCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-hourglass-split fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
			<!-- Permintaan -->
			<div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Permintaan Alat/Mesin yang Menunggu Persetujuan
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ permintaanStokCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-hourglass-split fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
			<!-- Perbaikan -->
			<div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Perbaikan Alat/Mesin yang Belum Diperbaiki
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ perbaikanStokCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-tools fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Card 2 -->
		<div class="row">
			<!-- Kerusakan -->
			<div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Alat/Mesin yang Rusak
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ kerusakanStockCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-exclamation-triangle fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
			<!-- Pemusnahan -->
			<div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Alat/Mesin yang Sudah Dimusnahkan
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ pemusnahanStokCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-fire fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
			<!-- Kehilangan -->
			<div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Alat/Mesin yang Hilang dan Belum Diganti
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ kehilanganStokCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-repeat fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
			<!-- Perawatan -->
			<div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                  Alat/Mesin yang Belum Dilakukan Perawatan Bulan ini
                </div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ perawatanStokCount }}</div>                                                  
              </div>
							<div class="col-auto">
								<i class="bi bi-calendar2-week fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
		</div>
    <!-- Bar & Chart Pie -->
    <div class="row">
      <!-- Bar Perawatan -->
      <div class="col-lg-8 mb-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Perawatan Alat/Mesin Tahun ini</h6>
          </div>
          <div class="card-body" v-if="!loading">
            <h4 class="small font-weight-bold m-4">
              Belum Dilakukan Perawatan
              <span class="float-right">{{ progressData.belum_dilakukan }}% ({{ progressData.counts.belum_dilakukan }} Alat/Mesin)</span>
            </h4>
            <div class="progress m-4">
              <div
                class="progress-bar bg-danger"
                role="progressbar"
                :style="'width: ' + progressData.belum_dilakukan + '%'"
                :aria-valuenow="progressData.belum_dilakukan"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            
            <h4 class="small font-weight-bold m-4">
              Dalam Proses Perawatan
              <span class="float-right">{{ progressData.dalam_proses }}% ({{ progressData.counts.dalam_proses }} Alat/Mesin)</span>
            </h4>
            <div class="progress m-4">
              <div
                class="progress-bar bg-warning"
                role="progressbar"
                :style="'width: ' + progressData.dalam_proses + '%'"
                :aria-valuenow="progressData.dalam_proses"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            
            <h4 class="small font-weight-bold m-4">
              Selesai Perawatan
              <span class="float-right">{{ progressData.selesai }}% ({{ progressData.counts.selesai }} Alat/Mesin)</span>
            </h4>
            <div class="progress m-4">
              <div
                class="progress-bar bg-success"
                role="progressbar"
                :style="'width: ' + progressData.selesai + '%'"
                :aria-valuenow="progressData.selesai"
                aria-valuemin="0"
                aria-valuemax="100"
              ></div>
            </div>
            
            <div class="text-center mt-4 mb-5">
              <small class="text-muted">Total Perawatan: {{ progressData.total }} Alat/Mesin</small>
            </div>
          </div>
          <div class="card-body text-center" v-else>
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Loading...</span>
            </div>
            <p>Memuat data progress perawatan...</p>
          </div>
        </div>
      </div>
      <!-- Chart Pie -->
      <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Kondisi Alat/Mesin</h6>
          </div>
          <div class="card-body">
            <div class="chart-pie pt-4 pb-2">
              <canvas id="myPieChart"></canvas>              
            </div>
            <div  class="mb-5 text-center small">
                <span id="chartLegend"></span>
              </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Chart -->
		<div class="row">
			<!-- Area Chart Peminjaman -->
			<div class="col-xl-6 col-lg-7">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Statistik Peminjaman Selesai Bulanan</h6>
            <div class="dropdown no-arrow">
              <select v-model="selectedYear" @change="fetchMonthlyCompletedLoans" class="form-control form-control-sm">
                <option v-for="year in availableYears" :value="year" :key="year">
                  {{ year }}
                </option>
              </select>
            </div>
          </div>
          <!-- Card Body -->
          <div class="card-body">
            <div class="chart-area">
              <canvas class="m-3" ref="monthlyLoansChart"></canvas>
            </div>
            <div v-if="isloading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
            <div v-if="errorMessage" class="alert alert-danger mt-3">
              {{ errorMessage }}
            </div>
          </div>
        </div>
      </div>
      <!-- Area Chart Permintaan -->
			<div class="col-xl-6 col-lg-7">
        <div class="card shadow mb-4">
          <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Statistik Permintaan Digunakan Bulanan</h6>
            <div class="dropdown no-arrow">
              <select v-model="selectedYearPermintaan" @change="fetchMonthlyCompletedLoansPermintaan" class="form-control form-control-sm">
                <option v-for="year in availableYearsPermintaan" :value="year" :key="year">
                  {{ year }}
                </option>
              </select>
            </div>
          </div>
          <!-- Card Body -->
          <div class="card-body">
            <div class="chart-area">
              <canvas class="m-3" ref="monthlyLoansChartPermintaan"></canvas>
            </div>
            <div v-if="isloading" class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
            <div v-if="errorMessagePermintaan" class="alert alert-danger mt-3">
              {{ errorMessagePermintaan }}
            </div>
          </div>
        </div>
      </div>
		</div>    
  </div>
</template>

<script>
import Chart from 'chart.js/auto';
import axios from 'axios'
export default {
  data() {
    return {
			chart: null,
      selectedYear: new Date().getFullYear(),
      availableYears: [],
      isloading: false,
      errorMessage: '',
      chartPermintaan: null,
      selectedYearPermintaan: new Date().getFullYear(),
      availableYearsPermintaan: [],
      errorMessagePermintaan: '',
      lowStockCount: 0,
			peminjamanStokCount: 0,
			permintaanStokCount: 0,
			perbaikanStokCount: 0,
			kerusakanStockCount: 0,
			pemusnahanStokCount: 0,
			kehilanganStokCount: 0,
			perawatanStokCount: 0,
      progressData: {
        belum_dilakukan: 0,
        dalam_proses: 0,
        selesai: 0,
        total: 0,
        counts: {
          belum_dilakukan: 0,
          dalam_proses: 0,
          selesai: 0
        }
      },
      loading: true,
      chartData: {
        labels: [],
        data: [],
        colors: []
      },
      pieChart: null
    }
  },
  mounted() {
    this.fetchLowStockCount();
		this.fetchPeminjamanStokCount();
		this.fetchPermintaanStokCount();
		this.fetchPerbaikanStokCount();
		this.fetchKerusakanStockCount();
		this.fetchPemusnahanStokCount();
		this.fetchKehilanganStokCount();
		this.fetchPerawatanStokCount();
		this.fetchMonthlyCompletedLoans();
    this.fetchAvailableYears();
    this.fetchMonthlyCompletedLoansPermintaan();
    this.fetchAvailableYearsPermintaan();
    this.fetchProgressData();
    this.fetchChartData();
  },
  methods: {
    async fetchLowStockCount() {
      try {
        const response = await axios.get('/api/v1/tools/low-stock/count');
        if (response.data.success) {
          this.lowStockCount = response.data.total_low_stock;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },		
		async fetchPeminjamanStokCount() {
      try {
        const response = await axios.get('/api/v1/peminjaman/belum-diproses/count');
        if (response.data.success) {
          this.peminjamanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
		async fetchPermintaanStokCount() {
      try {
        const response = await axios.get('/api/v1/permintaan/belum-diproses/count');
        if (response.data.success) {
          this.permintaanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
		async fetchPerbaikanStokCount() {
      try {
        const response = await axios.get('/api/v1/perbaikan/belum/count');
        if (response.data.success) {
          this.perbaikanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
		async fetchKerusakanStockCount() {
      try {
        const response = await axios.get('/api/v1/kerusakan/belum/count');
        if (response.data.success) {
          this.kerusakanStockCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
		async fetchPemusnahanStokCount() {
      try {
        const response = await axios.get('/api/v1/pemusnahan/selesai/count');
        if (response.data.success) {
          this.pemusnahanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
		async fetchKehilanganStokCount() {
      try {
        const response = await axios.get('/api/v1/kehilangan/belum/count');
        if (response.data.success) {
          this.kehilanganStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
		async fetchPerawatanStokCount() {
      try {
        const response = await axios.get('/api/v1/perawatan/belum/count');
        if (response.data.success) {
          this.perawatanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
    async fetchAvailableYears() {
      try {
        const response = await axios.get('/api/v1/peminjaman/chart/available-years');
        this.availableYears = response.data.years;
        // Set tahun terakhir sebagai tahun yang dipilih secara default
        if (this.availableYears.length > 0) {
          this.selectedYear = Math.max(...this.availableYears);
        }
      } catch (error) {
        console.error('Error fetching available years:', error);
        this.errorMessage = 'Gagal memuat daftar tahun';
      }
    },
    async fetchMonthlyCompletedLoans() {
      this.isloading = true;
      this.errorMessage = '';
      
      try {
        const response = await axios.get(`/api/v1/peminjaman/chart/monthly-completed?year=${this.selectedYear}`);
        this.renderChart(response.data);
      } catch (error) {
        console.error('Error fetching monthly completed loans:', error);
        this.errorMessage = 'Gagal memuat data peminjaman';
        // Hancurkan chart jika ada error
        if (this.chart) {
          this.chart.destroy();
          this.chart = null;
        }
      } finally {
        this.isloading = false;
      }
    },
    async fetchAvailableYearsPermintaan() {
      try {
        const response = await axios.get('/api/v1/permintaan/chart/available-years');
        this.availableYearsPermintaan = response.data.years;
        // Set tahun terakhir sebagai tahun yang dipilih secara default
        if (this.availableYearsPermintaan.length > 0) {
          this.selectedYearPermintaan = Math.max(...this.availableYearsPermintaan);
        }
      } catch (error) {
        console.error('Error fetching available years:', error);
        this.errorMessagePermintaan = 'Gagal memuat daftar tahun';
      }
    },
    async fetchMonthlyCompletedLoansPermintaan() {
      this.isloading = true;
      this.errorMessagePermintaan = '';
      try {
        const response = await axios.get(`/api/v1/permintaan/chart/monthly-completed?year=${this.selectedYearPermintaan}`);
        this.renderChartPermintaan(response.data);
      } catch (error) {
        console.error('Error fetching monthly completed loans:', error);
        this.errorMessagePermintaan = 'Gagal memuat data permintaan';
        // Hancurkan chart jika ada error
        if (this.chartPermintaan) {
          this.chartPermintaan.destroy();
          this.chartPermintaan = null;
        }
      } finally {
        this.isloading = false;
      }
    },
    renderChart(chartData) {
      // Hancurkan chart sebelumnya jika ada
      if (this.chart) {
        this.chart.destroy();
      }

      const ctx = this.$refs.monthlyLoansChart.getContext('2d');
      this.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
          datasets: [{
            label: `Peminjaman Selesai (${this.selectedYear})`,
            data: chartData.data,
            backgroundColor: 'rgba(22, 158, 168, 0.5)',
            borderColor: 'rgba(22, 158, 168, 1)',
            borderWidth: 1,
            pointBackgroundColor: 'rgba(22, 158, 168, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(22, 158, 168, 1)'
          }]
        },
        options: {
          maintainAspectRatio: false,
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  if (Number.isInteger(value)) {
                    return value;
                  }
                },
                stepSize: 1
              }
            }
          },
          plugins: {
            legend: {
              display: true,
              position: 'top'
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.7)',
              titleFont: {
                size: 14
              },
              bodyFont: {
                size: 12
              },
              callbacks: {
                label: function(context) {
                  return `Peminjaman: ${context.raw}`;
                },
                title: function(context) {
                  return `${context[0].label} ${chartData.year}`;
                }
              }
            }
          }
        }
      });
    },
    renderChartPermintaan(chartDataPermintaan) {
      // Hancurkan chart sebelumnya jika ada
      if (this.chartPermintaan) {
        this.chart.destroy();
      }

      const ctx = this.$refs.monthlyLoansChartPermintaan.getContext('2d');
      this.chartPermintaan = new Chart(ctx, {
        type: 'bar', // Anda bisa ganti dengan 'line' untuk chart garis
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
          datasets: [{
            label: `Permintaan Selesai (${this.selectedYearPermintaan})`,
            data: chartDataPermintaan.data,
            backgroundColor: 'rgba(22, 158, 168, 0.5)',
            borderColor: 'rgba(22, 158, 168, 1)',
            borderWidth: 1,
            pointBackgroundColor: 'rgba(22, 158, 168, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(22, 158, 168, 1)'
          }]
        },
        options: {
          maintainAspectRatio: false,
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  if (Number.isInteger(value)) {
                    return value;
                  }
                },
                stepSize: 1
              }
            }
          },
          plugins: {
            legend: {
              display: true,
              position: 'top'
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.7)',
              titleFont: {
                size: 14
              },
              bodyFont: {
                size: 12
              },
              callbacks: {
                label: function(context) {
                  return `Permintaan: ${context.raw}`;
                },
                title: function(context) {
                  return `${context[0].label} ${chartDataPermintaan.year}`;
                }
              }
            }
          }
        }
      });
    },
    async fetchProgressData() {
      try {
        this.loading = true;
        const response = await axios.get('/api/inventory/perawatan/progress');
        
        if (response.data.success) {
          this.progressData = response.data.data;
        }
      } catch (error) {
        console.error('Error fetching progress data:', error);
        this.$toast.error('Gagal memuat data progress perawatan');
      } finally {
        this.loading = false;
      }
    },
    fetchChartData() {
      axios.get('/api/v1/tool-conditions')
        .then(response => {
          this.chartData = response.data;
          this.renderChartNoSeri();
        })
        .catch(error => {
          console.error('Error fetching chart data:', error);
        });
    },
    renderChartNoSeri() {
      if (this.pieChart) {
        this.pieChart.destroy();
      }

      const ctx = document.getElementById('myPieChart').getContext('2d');
      this.pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: this.chartData.labels,
          datasets: [{
            data: this.chartData.data,
            backgroundColor: this.chartData.colors,
            hoverBackgroundColor: this.chartData.colors,
            hoverBorderColor: "rgba(234, 236, 244, 1)",
          }],
        },
        options: {
          maintainAspectRatio: false,
          plugins: {
            tooltip: {
              backgroundColor: "rgb(255,255,255)",
              bodyColor: "#858796",
              borderColor: '#dddfeb',
              borderWidth: 1,
              padding: 10,
              displayColors: false,
              caretPadding: 10,
            },
            legend: {
              display: false, // Hide default legend
            },
          },
          cutout: '80%',
        },
      });

      // === Custom Legend ===
      const legendContainer = document.getElementById('chartLegend');
      legendContainer.innerHTML = ''; // Clear previous

      // Atur styling kontainer untuk flex horizontal dan center
      legendContainer.style.display = 'flex';
      legendContainer.style.justifyContent = 'center';
      legendContainer.style.flexWrap = 'wrap'; // Agar tetap responsif jika panjang

      this.chartData.labels.forEach((label, index) => {
        const color = this.chartData.colors[index];
        const item = document.createElement('div');
        
        item.style.display = 'flex';
        item.style.alignItems = 'center';
        item.style.marginRight = '16px';
        // item.style.marginBottom = '8px';
        item.innerHTML = `
          <span style="width: 12px; height: 12px; background-color: ${color}; display: inline-block; margin-right: 6px; border-radius: 2px;"></span>
          <span style="font-size: 14px;">${label}</span>
        `;
        
        legendContainer.appendChild(item);
      });
    },
  },
  beforeDestroy() {
    // Hancurkan chart saat komponen di-destroy
    if (this.chart) {
      this.chart.destroy();
    } 
    if (this.chartPermintaan) {
      this.chartPermintaan.destroy();
    }
  }
}
</script>
<style scoped>
.chart-area {
  position: relative;
  height: 20rem;
}
@media (min-width: 768px) {
  .chart-area {
    height: 25rem;
  }
}
.dropdown {
  display: inline-block;
}
.form-control-sm {
  width: 100px;
  display: inline-block;
}
.progress {
  height: 20px;
}
</style>