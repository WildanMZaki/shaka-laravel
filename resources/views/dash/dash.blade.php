@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

              <div class="row mb-lg-3 mb-2">
                <div class="col-lg-4 mb-2 mb-lg-0">
                  <div class="card h-100">
                    <div class="card-body mt-mg-1">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-2">Total Pendapatan</h6>
                          <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                            <h4 class="mb-0 me-2">Rp 700.000</h4>
                            <!-- <small class="text-danger mt-1">-18%</small> -->
                          </div>
                          <!-- <small class="text-warning"></small> -->
                        </div>
                        <a href="#">
                          <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-circle shadow-sm">
                              <i class="mdi mdi-cash mdi-24px"></i>
                            </div>
                          </div>
                        </a>
                      </div>
                      
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 mb-2 mb-lg-0">
                  <div class="card h-100">
                    <div class="card-body mt-mg-1">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-2">Barang Masuk</h6>
                          <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                            <h4 class="mb-0 me-2">100 botol</h4>
                            <!-- <small class="text-warning mt-1">+1 berlangsung</small> -->
                          </div>
                          <!-- <small>Yearly Project</small> -->
                        </div>
                        <a href="#">
                          <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-circle shadow-sm">
                              <i class="mdi mdi-bottle-soda-classic-outline mdi-24px"></i>
                            </div>
                          </div>
                        </a>
                      </div>
                
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 mb-2 mb-lg-0">
                  <div class="card h-100">
                    <div class="card-body mt-mg-1">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <h6 class="mb-2">Pengajuan Cuti</h6>
                          <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                            <h4 class="mb-0 me-2">1</h4>
                            <!-- <small class="text-danger mt-1">-18%</small> -->
                          </div>
                          <!-- <small>Yearly Project</small> -->
                        </div>
                        <a href="#">
                          <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-circle shadow-sm">
                              <i class="mdi mdi-contacts-outline mdi-24px"></i>
                            </div>
                          </div>
                        </a>
                      </div>
                
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-lg-3 mb-2">
                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title m-0 me-2">Statistik</h5>
                      </div>
                      <!-- <p class="mt-3"><span class="fw-medium">Total 48.5% growth</span> 😎 this month</p> -->
                    </div>
                    <div class="card-body">
                      <div class="row g-3">
                        <div class="col-md-3 col-6">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-label-dark rounded shadow">
                                <i class="mdi mdi-account-group-outline mdi-24px"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <div class="small mb-1">Karyawan</div>
                              <div class="d-flex">
                                <h5 class="mb-0">4</h5>
                                <small class="text-success">(3 hadir)</small>
                              </div>

                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-label-success rounded shadow">
                                <i class="mdi mdi-trending-up mdi-24px"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <div class="small mb-1">Penjualan hari ini</div>
                              <h5 class="mb-0">22</h5>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 col-6">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-label-danger rounded shadow txt-label-danger">
                                <i class="mdi mdi-trending-down mdi-24px"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <div class="small mb-1">Pengeluaran bulan ini</div>
                              <h5 class="mb-0">Rp 200.000</h5>
                            </div>
                          </div>
                        </div>
                        
                        <div class="col-md-3 col-6">
                          <div class="d-flex align-items-center">
                            <div class="avatar">
                              <div class="avatar-initial bg-label-info rounded shadow txt-label-info">
                                <i class="mdi mdi-currency-usd mdi-24px"></i>
                              </div>
                            </div>
                            <div class="ms-3">
                              <div class="small mb-1">Laba bulan ini</div>
                              <h5 class="mb-0">Rp 500.000</h5>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mb-lg-3 mb-2">
                <div class="col-12">
                  <div class="card pb-5">
                    <div class="d-flex align-items-center justify-content-between m-4">
                      <h5 class="card-title m-0 me-2">List Penjualan</h5>
                      <!-- <button type="button" class="btn btn-success">
                        <i class="mdi mdi-plus mdi-24px"></i> Tambah Device
                      </button> -->
                    </div>
                    <div class="table-responsive">
                      <table class="table">
                        <thead class="table-light">
                          <tr>
                            <th class="text-truncate">Karyawan</th>
                            <th class="text-truncate">Penjualan</th>
                            <th class="text-truncate">Insentif</th>
                            <th class="text-truncate">Status Target</th>
                            <th class="text-truncate">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td class="text-truncate">Lewis Martin</td>
                            <td class="text-truncate">10 botol</td>
                            <td class="text-truncate">Rp 30.000</td>
                            <td class="text-truncate">
                              <div class="bg-label-success rounded-pill p-2 d-inline-flex align-items-center">
                                <i class="mdi mdi-check mdi-24px me-1"></i> Tercapai
                              </div>
                            </td>
                            <td class="text-truncate d-flex">
                              <a href="#" class="me-2">
                                <div class="avatar">
                                  <div class="avatar-initial bg-label-info rounded-3 shadow-sm">
                                    <i class="mdi mdi-account-details-outline mdi-24px"></i>
                                  </div>
                                </div>
                              </a>
                            </td>
                          </tr>
                          <tr>
                            <td class="text-truncate">Madun Kusnadi</td>
                            <td class="text-truncate">11 botol</td>
                            <td class="text-truncate">Rp 33.000</td>
                            <td class="text-truncate">
                              <div class="bg-label-danger rounded-pill p-2 d-inline-flex align-items-center">
                                <i class="mdi mdi-close-circle-outline mdi-24px me-1"></i> Gagal
                              </div>
                            </td>
                            <td class="text-truncate d-flex">
                              <a href="#" class="me-2">
                                <div class="avatar">
                                  <div class="avatar-initial bg-label-info rounded-3 shadow-sm">
                                    <i class="mdi mdi-account-details-outline mdi-24px"></i>
                                  </div>
                                </div>
                              </a>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
@endsection
