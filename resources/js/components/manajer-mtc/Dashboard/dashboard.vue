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
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Jumlah Stok Di Bawah Batas Minimum
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="lowStockCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ lowStockCount }}
                  </a>
                </div>
                <div class="mt-2">
                  <a href="#" @click.prevent="showStokModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div>
              </div>
							<div class="col-auto">
								<i class="bi bi-graph-down-arrow fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Stok Tools <= 1 -->
      <div class="modal fade" id="stokModal" tabindex="-1" role="dialog" aria-labelledby="stokModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="max-width: max-content;">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="stokModalLabel">Daftar Stok Tools kurang sama dengan 1</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalStok">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingStok" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data stok tools...</p>
              </div>

              <div v-else>
                <div v-if="stokList.length === 0" class="alert alert-info">
                  Tidak ada Stok Tools yang di bawah batas minimum.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>Kode</th>
                        <th>Alat/Mesin</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="stok in stokList" :key="stok.id">
                        <td>{{ stok.kode || '-' }}</td>
                        <td>{{ stok.nama || '-' }}</td>
                        <td class="text-center">
                          <button @click="viewDetailStok(stok.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModalStok">Tutup</button>
            </div>
          </div>
        </div>
      </div>

			<!-- Peminjaman Card -->
      <div class="col-xl-3 col-md-4 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Peminjaman Alat/Mesin yang Menunggu Persetujuan
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="peminjamanStokCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ peminjamanStokCount }}
                  </a>
                </div>  
                <div class="mt-2">
                  <a href="#" @click.prevent="showPeminjamanModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div>                                                
              </div>
              <div class="col-auto">
                <i class="bi bi-hourglass-split fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Peminjaman Belum Diproses -->
      <div class="modal fade" id="peminjamanModal" tabindex="-1" role="dialog" aria-labelledby="peminjamanModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="max-width: max-content;">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="peminjamanModalLabel">Daftar Peminjaman Belum Diproses</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModal">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingPeminjaman" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data peminjaman...</p>
              </div>
              
              <div v-else>
                <div v-if="peminjamanList.length === 0" class="alert alert-info">
                  Tidak ada peminjaman yang belum diproses.
                </div>
                
                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Peminjaman</th>
                        <th>Peminjam</th>
                        <th>Alat/Mesin</th>
                        <th>No. Seri</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Detail</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="peminjaman in peminjamanList" :key="peminjaman.id">
                        <td>{{ peminjaman.no_peminjaman }}</td>
                        <td>{{ peminjaman.users ? peminjaman.users.nama : '-' }}</td>
                        <td>{{ peminjaman.tools ? peminjaman.tools.nama : '-' }}</td>
                        <td>
                          <span v-for="(noSeri, index) in peminjaman.no_seri" :key="noSeri.id">
                            {{ noSeri.no_seri }}<span v-if="index < peminjaman.no_seri.length - 1">, </span>
                          </span>
                        </td>
                        <td>{{ formatDate(peminjaman.tgl_pinjam) }}</td>
                        <td>{{ formatDate(peminjaman.tgl_kembali) }}</td>
                        <td>{{ peminjaman.detail_peminjaman || '-' }}</td>
                        <td class="text-center">
                          <button @click="viewDetailPeminjaman(peminjaman.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModal">Tutup</button>
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
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Permintaan Alat/Mesin yang Menunggu Persetujuan
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="permintaanStokCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ permintaanStokCount }}
                  </a>
                </div>  
                <div class="mt-2">
                  <a href="#" @click.prevent="showPermintaanModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div>                                              
              </div>
							<div class="col-auto">
								<i class="bi bi-hourglass-split fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Permintaan Belum Diproses -->
      <div class="modal fade" id="permintaanModal" tabindex="-1" role="dialog" aria-labelledby="permintaanModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="max-width: max-content;">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="permintaanModalLabel">Daftar Permintaan Belum Diproses</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalPermintaan">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingPermintaan" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data peminjaman...</p>
              </div>

              <div v-else>
                <div v-if="permintaanList.length === 0" class="alert alert-info">
                  Tidak ada peminjaman yang belum diproses.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Permintaan</th>
                        <th>Diminta Oleh</th>
                        <th>Alat/Mesin</th>
                        <th>No. Seri</th>
                        <th>Tgl Permintaan</th>
                        <th>Detail</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="permintaan in permintaanList" :key="permintaan.id">
                        <td>{{ permintaan.no_permintaan }}</td>
                        <td>{{ permintaan.users ? permintaan.users.nama : '-' }}</td>
                        <td>{{ permintaan.tools ? permintaan.tools.nama : '-' }}</td>
                        <td>
                          <span v-for="(noSeri, index) in permintaan.no_seri" :key="noSeri.id">
                            {{ noSeri.no_seri }}<span v-if="index < permintaan.no_seri.length - 1">, </span>
                          </span>
                        </td>
                        <td>{{ permintaan.tgl_permintaan || '-' }}</td>
                        <td>{{ permintaan.detail_permintaan || '-' }}</td>
                        <td class="text-center">
                          <button @click="viewDetailPermintaan(permintaan.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModalPermintaan">Tutup</button>
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
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Perbaikan Alat/Mesin yang Belum Diperbaiki
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="perbaikanStokCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ perbaikanStokCount }}
                  </a>
                </div>
                <div class="mt-2">
                  <a href="#" @click.prevent="showPerbaikanModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div>
              </div>
							<div class="col-auto">
								<i class="bi bi-tools fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Perbaikan Belum Diproses -->
      <div class="modal fade" id="perbaikanModal" tabindex="-1" role="dialog" aria-labelledby="perbaikanModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="max-width: max-content;">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="perbaikanModalLabel">Daftar Perbaikan Belum Diproses</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalPerbaikan">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingPerbaikan" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data peminjaman...</p>
              </div>

              <div v-else>
                <div v-if="perbaikanList.length === 0" class="alert alert-info">
                  Tidak ada peminjaman yang belum diproses.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Perbaikan</th>
                        <th>Alat/Mesin</th>
                        <th>No. Seri</th>
                        <th>Tgl Error</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="perbaikan in perbaikanList" :key="perbaikan.id">
                        <td>{{ perbaikan.no_perbaikan }}</td>
                        <td>{{ perbaikan.no_seri && perbaikan.no_seri.tools && perbaikan.no_seri.tools.nama || '-' }}</td>
                        <td>{{ perbaikan.no_seri ? perbaikan.no_seri.no_seri : '-' }}</td>
                        <td>{{ perbaikan.tgl_perbaikan || '-' }}</td>
                        <td class="text-center">
                          <button @click="viewDetailPerbaikan(perbaikan.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="closeModalPerbaikan">Tutup</button>
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
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Alat/Mesin yang Rusak
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="kerusakanStockCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ kerusakanStockCount }}
                  </a>
                </div>
                <div class="mt-2">
                  <a href="#" @click.prevent="showKerusakanModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div> 
              </div>
							<div class="col-auto">
								<i class="bi bi-exclamation-triangle fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Kerusakan Belum Diproses -->
      <div class="modal fade" id="kerusakanModal" tabindex="-1" role="dialog" aria-labelledby="kerusakanModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="max-width: max-content;">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="kerusakanModalLabel">Daftar Kerusakan Belum Diproses</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalKerusakan">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingKerusakan" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data peminjaman...</p>
              </div>

              <div v-else>
                <div v-if="kerusakanList.length === 0" class="alert alert-info">
                  Tidak ada peminjaman yang belum diproses.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Kerusakan</th>
                        <th>Alat/Mesin</th>
                        <th>No. Seri</th>
                        <th>Tgl Rusak</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="kerusakan in kerusakanList" :key="kerusakan.id">
                        <td>{{ kerusakan.no_kerusakan }}</td>
                        <td>{{ kerusakan.no_seri && kerusakan.no_seri.tools && kerusakan.no_seri.tools.nama || '-' }}</td>
                        <td>{{ kerusakan.no_seri ? kerusakan.no_seri.no_seri : '-' }}</td>
                        <td>{{ kerusakan.tgl_kerusakan || '-' }}</td>
                        <td class="text-center">
                          <button @click="viewDetailKerusakan(kerusakan.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Alat/Mesin yang Sedang Diproses Pemusnahan
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="pemusnahanStokCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ pemusnahanStokCount }}
                  </a>
                </div>
                <div class="mt-2">
                  <a href="#" @click.prevent="showPemusnahanModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div> 
              </div>
							<div class="col-auto">
								<i class="bi bi-fire fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Pemusnahan Selesai -->
      <div class="modal fade" id="pemusnahanModal" tabindex="-1" role="dialog" aria-labelledby="pemusnahanModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="max-width: max-content;">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="pemusnahanModalLabel">Daftar Pemusnahan yang Belum Dimusnahkan</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalPemusnahan">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingPemusnahan" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data peminjaman...</p>
              </div>

              <div v-else>
                <div v-if="pemusnahanList.length === 0" class="alert alert-info">
                  Tidak ada pemusnahan yang Sudah Selesai.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Pemusnahan</th>
                        <th>Alat/Mesin</th>
                        <th>No. Seri</th>
                        <th>Tgl Musnah</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="pemusnahan in pemusnahanList" :key="pemusnahan.id">
                        <td>{{ pemusnahan.no_pemusnahan }}</td>
                        <td>{{ pemusnahan.no_seri && pemusnahan.no_seri.tools && pemusnahan.no_seri.tools.nama || '-' }}</td>
                        <td>{{ pemusnahan.no_seri ? pemusnahan.no_seri.no_seri : '-' }}</td>
                        <td>{{ pemusnahan.tgl_pemusnahan || '-' }}</td>
                        <td class="text-center">
                          <button @click="viewDetailPemusnahan(pemusnahan.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Alat/Mesin yang Hilang dan Belum Diganti
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="kehilanganStokCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ kehilanganStokCount }}
                  </a>
                </div>
                <div class="mt-2">
                  <a href="#" @click.prevent="showKehilanganModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div> 
              </div>
							<div class="col-auto">
								<i class="bi bi-repeat fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Kehilangan Belum Diproses -->
      <div class="modal fade" id="kehilanganModal" tabindex="-1" role="dialog" aria-labelledby="kehilanganModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content" style="max-width: max-content;">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="kehilanganModalLabel">Daftar Kehilangan Belum Diproses</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalHilang">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingKehilangan" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data peminjaman...</p>
              </div>

              <div v-else>
                <div v-if="kehilanganList.length === 0" class="alert alert-info">
                  Tidak ada peminjaman yang belum diproses.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Kehilangan</th>
                        <th>Alat/Mesin</th>
                        <th>No. Seri</th>
                        <th>Tgl Hilang</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="kehilangan in kehilanganList" :key="kehilangan.id">
                        <td>{{ kehilangan.no_kehilangan }}</td>
                        <td>{{ kehilangan.no_seri && kehilangan.no_seri.tools && kehilangan.no_seri.tools.nama || '-' }}</td>
                        <td>{{ kehilangan.no_seri ? kehilangan.no_seri.no_seri : '-' }}</td>
                        <td>{{ kehilangan.tgl_kehilangan || '-' }}</td>
                        <td class="text-center">
                          <button @click="viewDetailKehilanagan(kehilangan.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Alat/Mesin yang Belum Dilakukan Perawatan Bulan ini
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="perawatanStokCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ perawatanStokCount }}
                  </a>
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" @click.prevent="showPerawatanModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div>
              </div>
							<div class="col-auto">
								<i class="bi bi-calendar2-week fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Perawatan Belum Dilakukan -->
      <div class="modal fade" id="perawatanModal" tabindex="-1" role="dialog" aria-labelledby="perawatanModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="perawatanModalLabel">Daftar Perawatan Belum Dilakukan</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalPerawatan">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingPerawatan" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data perawatan belum dilakukan...</p>
              </div>

              <div v-else>
                <div v-if="perawatanList.length === 0" class="alert alert-info">
                  Tidak ada perawatan yang belum dilakukan.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Perawatan</th>
                        <th>Alat/Mesin</th>
                        <th>No Seri</th>
                        <th>Tgl Perawatan</th>
                        <th>Waktu Perawatan</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tr v-for="perawatan in perawatanList" :key="perawatan.id">
                      <td>{{ perawatan.no_perawatan || '-' }}</td>
                      <td>{{ perawatan.no_seri && perawatan.no_seri.tools && perawatan.no_seri.tools.nama || '-' }}</td>
                      <td>{{ perawatan.no_seri && perawatan.no_seri.no_seri || '-' }}</td>
                      <td>{{ perawatan.tgl_perawatan || '-' }}</td>
                      <td>{{ perawatan.waktu_perawatan || '-' }}</td>
                      <td class="text-center">
                        <button class="btn-sm" @click="viewDetailPerawatan(perawatan.id)">
                            <i class="fas fa-eye text-info"></i> Detail
                          </button>
                      </td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

		</div>
    <!-- Card 3 -->
    <div class="row">
      <!-- Tanggal Peminjaman Lewat -->
      <div class="col-lg-12 mb-4">
        <div class="border-left-primary shadow h-100 py-2">
          <div class="card-body">
            <div class="row no-gutters align-items-center">
              <div class="col ml-2">
                <div class="text-xs font-weight-bold text-gray-900 text-uppercase mb-1">
                  Tanggal Peminjaman Lewat
                </div>
                <div class="h5 mb-0 font-weight-bold">
                  <a href="#" class="text-decoration-none"
                    :class="overDatePeminjamanStockCount > 0 ? 'text-danger' : 'text-gray-800'">
                    {{ overDatePeminjamanStockCount }}
                  </a>
                </div>
                <div class="mt-2">
                  <a href="#" @click.prevent="showOverDatePeminjamanModal" class="text-primary small text-decoration-none">
                    <i class="fas fa-eye text-primary"></i><b> Detail</b>
                  </a>
                </div> 
              </div>
							<div class="col-auto">
								<i class="bi bi-calendar2-x fa-2x mr-3" style="color: rgba(22, 158, 168, 0.2);"></i>
							</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Modal Tanggal Peminjaman Lewat -->
      <div class="modal fade" id="tglpeminjamanModal" tabindex="-1" role="dialog" aria-labelledby="tglpeminjamanModalLabel" aria-hidden="true" style="overflow-y: auto;">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="tglpeminjamanModalLabel">Daftar Tanggal Peminjaman yang Lewat</h5>
              <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" @click="closeModalTanggalPeminjaman">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div v-if="loadingTanggalPeminjaman" class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p>Memuat data tanggal peminjaman yang lewat...</p>
              </div>

              <div v-else>
                <div v-if="tglpeminjamanList.length === 0" class="alert alert-info">
                  Tidak ada tanggal peminjaman yang lewat.
                </div>

                <div v-else class="table-responsive">
                  <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                      <tr>
                        <th>No. Peminjaman</th>
                        <th>Peminjam</th>
                        <th>Alat/Mesin</th>
                        <th>No. Seri</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Detail</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="peminjaman in tglpeminjamanList" :key="peminjaman.id">
                        <td>{{ peminjaman.no_peminjaman }}</td>
                        <td>{{ peminjaman.users ? peminjaman.users.karyawan.nama : '-' }}</td>
                        <td>{{ peminjaman.tools ? peminjaman.tools.nama : '-' }}</td>
                        <td>
                          <span v-for="(noSeri, index) in peminjaman.no_seri" :key="noSeri.id">
                            {{ noSeri.no_seri }}<span v-if="index < peminjaman.no_seri.length - 1">, </span>
                          </span>
                        </td>
                        <td>{{ formatDate(peminjaman.tgl_pinjam) }}</td>
                        <td>{{ formatDate(peminjaman.tgl_kembali) }}</td>
                        <td>{{ peminjaman.detail_peminjaman || '-' }}</td>
                        <td class="text-center">
                          <button class="btn-sm btn-primary" @click="viewDetailTanggalPeminjaman(peminjaman.id)">
                            <i class="fas fa-eye text-white"></i> Detail
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
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
              <div class="card-header py-3 d-flex justify-content-between align-items-center">
                  <h6 class="m-0 font-weight-bold text-primary">Perawatan Alat/Mesin</h6>
                  <div class="date-filter">
                      <div class="input-group">
                          <input type="date" class="form-control" v-model="filter.start_date">
                          <span class="input-group-text mr-1 ml-1">s/d</span>
                          <input type="date" class="form-control" v-model="filter.end_date">
                          <button class="btn btn-primary mr-2 ml-2" @click="applyFilter">
                              <i class="fas fa-filter"></i> Filter
                          </button>
                          <button class="btn btn-secondary" @click="resetFilter">
                              <i class="fas fa-sync-alt"></i> Reset
                          </button>
                      </div>
                  </div>
              </div>
              <div class="card-body" v-if="!isloading">
                  <div class="alert alert-info" v-if="progressData.date_range">
                      Menampilkan data perawatan dari <strong>{{ formatDate(progressData.date_range.start) }}</strong> 
                      sampai <strong>{{ formatDate(progressData.date_range.end) }}</strong>
                  </div>
                  
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
            <div  class="mb-5 mt-5 text-center small">
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
      stokList: [],
      loadingStok: false,
			peminjamanStokCount: 0,
      peminjamanList: [],
      loadingPeminjaman: false,
			permintaanStokCount: 0,
      permintaanList: [],
      loadingPermintaan: false,
			perbaikanStokCount: 0,
      perbaikanList: [],
      loadingPerbaikan: false,
			kerusakanStockCount: 0,
      kerusakanList: [],
      loadingKerusakan: false,
			pemusnahanStokCount: 0,
      pemusnahanList: [],
      loadingPemusnahan: false,
			kehilanganStokCount: 0,
      kehilanganList: [],
      loadingKehilangan: false,
			perawatanStokCount: 0,
      perawatanList: [],
      loadingPerawatan: false,
      progressData: {
                      belum_dilakukan: 0,
                      dalam_proses: 0,
                      selesai: 0,
                      total: 0,
                      counts: {
                          belum_dilakukan: 0,
                          dalam_proses: 0,
                          selesai: 0
                      },
                      date_range: {
                          start: '',
                          end: ''
                      },
                    },
      filter: {
                start_date: '',
                end_date: ''
              },
      loading: true,
      chartData: {
        labels: [],
        data: [],
        colors: []
      },
      pieChart: null,
      overDatePeminjamanStockCount: 0,
      loadingTanggalPeminjaman: false,
      tglpeminjamanList: [],
    }
  },
  created() {
    // Set default ke awal dan akhir bulan ini
    this.setDefaultDateRange();
    this.fetchProgressData();
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
    this.fetchOverDatePeminjaman();
  },
  methods: {
    async fetchOverDatePeminjaman() {
      try {
        const response = await axios.get('/api/v1/peminjaman/tgl-lewat/count');
        if (response.data.success) {
          this.overDatePeminjamanStockCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah over date peminjaman');
      }
    },

    async showOverDatePeminjamanModal() {
      try {
        this.loadingTanggalPeminjaman = true;
        const response = await axios.get('/api/v1/peminjaman/tgl-lewat/list');
        

        if (response.data.success) {
          this.tglpeminjamanList = response.data.data;
          // console.log(this.tglpeminjamanList);
          $('#tglpeminjamanModal').modal('show');
        } else {
          throw new Error( response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching tanggal peminjaman list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat tanggal peminjaman');
      } finally {
        this.loadingTanggalPeminjaman = false;
      }
    },

    viewDetailTanggalPeminjaman(id) {
      $('#tglpeminjamanModal').modal('hide');
      this.$router.push(`/manajer-mtc/peminjaman/detail/${id}`);
    },

    async fetchLowStockCount() {
      try {
        const response = await axios.get('/api/v1/tools/low-stock/count');
        if (response.data.success) {
          this.lowStockCount = response.data.total_low_stock;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah stok tools <= 1');
        // Anda bisa menambahkan notifikasi error di sini
      }
    },

    async showStokModal() {
      try {
        this.loadingStok = true;
        const response = await axios.get('/api/v1/tools/low-stock/list');
        
        if (response.data.success) {
          this.stokList = response.data.data;
          $('#stokModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching stok tools list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar perbaikan');
      } finally {
        this.loadingStok = false;
      }
    },

    viewDetailStok() {
      $('#stokModal').modal('hide');
      this.$router.push('/manajer-mtc/master-data');
    },

		async fetchPeminjamanStokCount() {
      try {
        const response = await axios.get('/api/v1/peminjaman/belum-diproses/count');
        if (response.data.success) {
          this.peminjamanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching peminjaman count:', error);
        this.$toast.error('Gagal memuat jumlah peminjaman');
      }
    },
    
    async showPeminjamanModal() {
      // if (this.peminjamanStokCount === 0) {
      //   this.$toast.info('Tidak ada peminjaman yang belum diproses');
      //   return;
      // }
      
      try {
        this.loadingPeminjaman = true;
        const response = await axios.get('/api/v1/peminjaman/belum-diproses/list');
        
        if (response.data.success) {
          this.peminjamanList = response.data.data;
          $('#peminjamanModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching peminjaman list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar peminjaman');
      } finally {
        this.loadingPeminjaman = false;
      }
    },

    viewDetailPeminjaman(id) {
      // Tutup modal terlebih dahulu
      $('#peminjamanModal').modal('hide');
      
      // Navigasi ke halaman detail
      this.$router.push(`/manajer-mtc/peminjaman/detail/${id}`);
    },

    closeModal() {
      $('#peminjamanModal').modal('hide');      
    },

    closeModalPermintaan() {
      $('#permintaanModal').modal('hide');
    },

    closeModalPerbaikan() {
      $('#perbaikanModal').modal('hide');
    },

    closeModalKerusakan() {
      $('#kerusakanModal').modal('hide');
    },

    closeModalPemusnahan() {
      $('#pemusnahanModal').modal('hide');
    },

    closeModalHilang() {
      $('#kehilanganModal').modal('hide');
    },

    closeModalStok() {
      $('#stokModal').modal('hide');
    },

    closeModalPerawatan() {
      $('#perawatanModal').modal('hide');
    },

    closeModalTanggalPeminjaman() {
      $('#tglpeminjamanModal').modal('hide');
    },

		async fetchPermintaanStokCount() {
      try {
        const response = await axios.get('/api/v1/permintaan/belum-diproses/count');
        if (response.data.success) {
          this.permintaanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah permintaan');
        // Anda bisa menambahkan notifikasi error di sini
      }
    },
    async showPermintaanModal() {
      // if (this.permintaanStokCount === 0) {
      //   this.$toast.info('Tidak ada permintaan yang belum diproses');
      //   return;
      // }

      try {
        this.loadingPermintaan = true;
        const response = await axios.get('/api/v1/permintaan/belum-diproses/list');
        
        if (response.data.success) {
          this.permintaanList = response.data.data;
          $('#permintaanModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching permintaan list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar permintaan');
      } finally {
        this.loadingPermintaan = false;
      }
    },

    viewDetailPermintaan(id) {
      // Tutup modal terlebih dahulu
      $('#permintaanModal').modal('hide');
      
      // Navigasi ke halaman detail
      this.$router.push(`/manajer-mtc/permintaan/detail/${id}`);
    },

		async fetchPerbaikanStokCount() {
      try {
        const response = await axios.get('/api/v1/perbaikan/belum/count');
        if (response.data.success) {
          this.perbaikanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah perbaikan');
        // Anda bisa menambahkan notifikasi error di sini
      }
    },

    async showPerbaikanModal() {
      // if (this.perbaikanStokCount === 0) {
      //   this.$toast.info('Tidak ada perbaikan yang belum diproses');
      //   return;
      // }

      try {
        this.loadingPerbaikan = true;
        const response = await axios.get('/api/v1/perbaikan/belum-diproses/list');
        
        if (response.data.success) {
          this.perbaikanList = response.data.data;
          $('#perbaikanModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching perbaikan list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar perbaikan');
      } finally {
        this.loadingPerbaikan = false;
      }
    },
    
    viewDetailPerbaikan(id) {
      // Tutup modal terlebih dahulu
      $('#perbaikanModal').modal('hide');
      
      // Navigasi ke halaman detail
      this.$router.push('/kondisi-error');
    },

		async fetchKerusakanStockCount() {
      try {
        const response = await axios.get('/api/v1/kerusakan/belum/count');
        if (response.data.success) {
          this.kerusakanStockCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah kerusakan');
        // Anda bisa menambahkan notifikasi error di sini
      }
    },

    async showKerusakanModal() {
      // if (this.kerusakanStockCount === 0) {
      //   this.$toast.info('Tidak ada kerusakan yang belum diproses');
      //   return;
      // }

      try {
        this.loadingKerusakan = true;
        const response = await axios.get('/api/v1/kerusakan/belum/list');
        
        if (response.data.success) {
          this.kerusakanList = response.data.data;
          $('#kerusakanModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching kerusakan list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar kerusakan');
      } finally {
        this.loadingKerusakan = false;
      }
    },

    viewDetailKerusakan(id) {
      // Tutup modal terlebih dahulu
      $('#kerusakanModal').modal('hide');
      
      // Navigasi ke halaman detail
      this.$router.push({ name: 'm-kondisi-detail-rusak', params: { id } });
    },

		async fetchPemusnahanStokCount() {
      try {
        const response = await axios.get('/api/v1/pemusnahan/selesai/count');
        if (response.data.success) {
          this.pemusnahanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah pemusnahan');
        // Anda bisa menambahkan notifikasi error di sini
      }
    },

    async showPemusnahanModal() {
      // if (this.pemusnahanStokCount === 0) {
      //   this.$toast.info('Tidak ada pemusnahan yang selesai');
      //   return;
      // }

      try {
        this.loadingPemusnahan = true;
        const response = await axios.get('/api/v1/pemusnahan/selesai/list');
        
        if (response.data.success) {
          this.pemusnahanList = response.data.data;
          $('#pemusnahanModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching pemusnahan list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar pemusnahan');
      } finally {
        this.loadingPemusnahan = false;
      }
    },

    viewDetailPemusnahan(id) {
      // Tutup modal terlebih dahulu
      $('#pemusnahanModal').modal('hide');
      
      // Navigasi ke halaman detail
      this.$router.push({ name: 'kondisi-detail-proses-musnah', params: { id } });
    },
  
		async fetchKehilanganStokCount() {
      try {
        const response = await axios.get('/api/v1/kehilangan/belum/count');
        if (response.data.success) {
          this.kehilanganStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah kehilangan');
        // Anda bisa menambahkan notifikasi error di sini
      }
    },

    async showKehilanganModal() {
      // if (this.kehilanganStokCount === 0) {
      //   this.$toast.info('Tidak ada kehilangan yang belum diproses');
      //   return;
      // }

      try {
        this.loadingKehilangan = true;
        const response = await axios.get('/api/v1/kehilangan/belum/list');
        
        if (response.data.success) {
          this.kehilanganList = response.data.data;
          $('#kehilanganModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching kehilangan list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar kehilangan');
      } finally {
        this.loadingKehilangan = false;
      }
    },

    viewDetailKehilanagan(id) {
      // Tutup modal terlebih dahulu
      $('#kehilanganModal').modal('hide');
      
      // Navigasi ke halaman detail
      this.$router.push({ name: 'm-data-detail-baru-hilang', params: { id } });
    },

		async fetchPerawatanStokCount() {
      try {
        const response = await axios.get('/api/v1/perawatan/belum/count');
        if (response.data.success) {
          this.perawatanStokCount = response.data.count;
        }
      } catch (error) {
        console.error('Error fetching low stock count:', error);
        this.$toast.error('Gagal memuat jumlah perawatan yang belum dilakukan');
        // Anda bisa menambahkan notifikasi error di sini
      }
    },

    async showPerawatanModal() {
      try {
        this.loadingPerawatan = true;
        const response = await axios.get('/api/v1/perawatan/belum/list');
        
        if (response.data.success) {
          this.perawatanList = response.data.data;
          $('#perawatanModal').modal('show');
        } else {
          throw new Error(response.data.message || 'Invalid response format');
        }
      } catch (error) {
        console.error('Error fetching perawatan belum dilakukan perawatan list:', error);
        this.$toast.error(error.response?.data?.message || 'Gagal memuat daftar perbaikan');
      } finally {
        this.loadingPerawatan = false;
      }
    },

    viewDetailPerawatan() {
      $('#perawatanModal').modal('hide');
      this.$router.push('/admin-mtc/jadwal-perawatan');
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
        const response = await axios.get(`/api/v1/peminjaman/chart/monthly-all-status?year=${this.selectedYear}`);
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
        const response = await axios.get(`/api/v1/permintaan/chart/monthly-all-status?year=${this.selectedYearPermintaan}`);
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
      if (this.chart) {
        this.chart.destroy();
      }

      const ctx = this.$refs.monthlyLoansChart.getContext('2d');
      this.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'],
          datasets: [
            {
              label: 'Belum Diproses',
              data: chartData.belum_diproses,
              backgroundColor: 'rgba(108, 117, 125, 0.7)',
              borderColor: 'rgba(108, 117, 125, 1)',
              borderWidth: 1
            },
            {
              label: 'Menunggu Diambil',
              data: chartData.menunggu_diambil,
              backgroundColor: 'rgba(255, 193, 7, 0.7)',
              borderColor: 'rgba(255, 193, 7, 1)',
              borderWidth: 1
            },
            {
              label: 'Dipinjam',
              data: chartData.dipinjam,
              backgroundColor: 'rgba(0, 123, 255, 0.7)',
              borderColor: 'rgba(0, 123, 255, 1)',
              borderWidth: 1
            },
            {
              label: 'Ditolak',
              data: chartData.ditolak,
              backgroundColor: 'rgba(220, 53, 69, 0.7)',
              borderColor: 'rgba(220, 53, 69, 1)',
              borderWidth: 1
            },
            {
              label: 'Selesai',
              data: chartData.selesai,
              backgroundColor: 'rgba(40, 167, 69, 0.7)',
              borderColor: 'rgba(40, 167, 69, 1)',
              borderWidth: 1
            }
          ]
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
              },
              stacked: false // atau true jika ingin stacked chart
            },
            x: {
              stacked: false // atau true jika ingin stacked chart
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
                  return `${context.dataset.label}: ${context.raw}`;
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
          datasets: [
          {
              label: 'Belum Diproses',
              data: chartDataPermintaan.belum_diproses,
              backgroundColor: 'rgba(108, 117, 125, 0.7)',
              borderColor: 'rgba(108, 117, 125, 1)',
              borderWidth: 1
            },
            {
              label: 'Menunggu Diambil',
              data: chartDataPermintaan.menunggu_diambil,
              backgroundColor: 'rgba(255, 193, 7, 0.7)',
              borderColor: 'rgba(255, 193, 7, 1)',
              borderWidth: 1
            },
            {
              label: 'Digunakan',
              data: chartDataPermintaan.digunakan,
              backgroundColor: 'rgba(0, 123, 255, 0.7)',
              borderColor: 'rgba(0, 123, 255, 1)',
              borderWidth: 1
            },
            {
              label: 'Ditolak',
              data: chartDataPermintaan.ditolak,
              backgroundColor: 'rgba(220, 53, 69, 0.7)',
              borderColor: 'rgba(220, 53, 69, 1)',
              borderWidth: 1
            },
          ]
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
              },
              stacked: false // atau true jika ingin stacked chart
            },
            x: {
              stacked: false // atau true jika ingin stacked chart
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
    setDefaultDateRange() {
        const now = new Date();
        const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
        const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        
        this.filter.start_date = this.formatDateForInput(firstDay);
        this.filter.end_date = this.formatDateForInput(lastDay);
    },
    async fetchProgressData() {
        try {
            this.loading = true;
            const params = {
                start_date: this.filter.start_date,
                end_date: this.filter.end_date
            };
            
            const response = await axios.get('/api/inventory/perawatan/progress', { params });
            
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
    applyFilter() {
        // Validasi tanggal
        if (new Date(this.filter.start_date) > new Date(this.filter.end_date)) {
            this.$toast.error('Tanggal mulai tidak boleh lebih besar dari tanggal akhir');
            return;
        }
        
        this.fetchProgressData();
    },
    resetFilter() {
        this.setDefaultDateRange();
        this.fetchProgressData();
    },
    formatDate(dateString) {
        const options = { year: 'numeric', month: 'long', day: 'numeric' };
        return new Date(dateString).toLocaleDateString('id-ID', options);
    },
    formatDateForInput(date) {
        return date.toISOString().split('T')[0];
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
        item.style.marginBottom = '10px';
        item.style.marginTop = '10px';
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
.modal {
  z-index: 1050 !important; /* Modal di atas backdrop */
}

.table th, .table td {
  vertical-align: middle;
}

/* .btn-sm {
  padding: 0.25rem 0.5rem;
  font-size: 0.875rem;
  line-height: 1.5;
  border-radius: 0.2rem;
} */

.modal-content {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  max-width: 1050px;
  text-align: center;
}

/* .modal-dialog {
  position: relative;
  width: auto;
  margin: .5rem;
  pointer-events: none;
} */

.modal-header .close {
  opacity: 1;
  font-size: 1.5rem;
  line-height: 1;
  padding: 0.5rem;
  margin: -0.5rem -0.5rem -0.5rem auto;
}

.modal-header .close:hover {
  opacity: 0.8;
}

.modal-footer .btn {
  min-width: 80px;
}
</style>