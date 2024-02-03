@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
  $presenceEmployee = \App\Helpers\Fun::presenceEmployee();
  $products = \App\Helpers\Fun::getProducts();

@endphp
    <div class="container-xxl flex-grow-1 container-p-y">

      <div class="row mb-lg-3 mb-2">
        <div class="col-lg-3 mb-2 mb-lg-0">
          <div class="card h-100">
            <div class="card-body mt-mg-1">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2">Total Pendapatan</h6>
                  <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                    <h4 class="mb-0 me-2">{{ \App\Helpers\Fun::getIncome() }}</h4>
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
        <div class="col-lg-3 mb-2 mb-lg-0">
          <div class="card h-100">
            <div class="card-body mt-mg-1">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2">Barang Masuk</h6>
                  <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                    <h4 class="mb-0 me-2">{{ $products->in < 999999 ? ($products->in_formatted . ' botol') : $products->in_formatted }}</h4>
                    <!-- <small class="text-warning mt-1">+1 berlangsung</small> -->
                  </div>
                  <!-- <small>Yearly Project</small> -->
                </div>
                <a href="{{ route('products.restocks.list') }}">
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
        <div class="col-lg-3 mb-2 mb-lg-0">
          <div class="card h-100">
            <div class="card-body mt-mg-1">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2">Stok</h6>
                  <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                    <h4 class="mb-0 me-2">{{ $products->remaining < 999999 ? ($products->remaining_formatted . ' botol') : $products->remaining_formatted }}</h4>
                    <!-- <small class="text-warning mt-1">+1 berlangsung</small> -->
                  </div>
                  <!-- <small>Yearly Project</small> -->
                </div>
                <a href="{{ route('products') }}">
                  <div class="avatar">
                    <div class="avatar-initial bg-label-success rounded-circle shadow-sm">
                      <i class="ti ti-bottle"></i>
                    </div>
                  </div>
                </a>
              </div>
        
            </div>
          </div>
        </div>
        <div class="col-lg-3 mb-2 mb-lg-0">
          <div class="card h-100">
            <div class="card-body mt-mg-1">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-2">Pengajuan Cuti</h6>
                  <div class="d-flex flex-wrap align-items-center mb-2 pb-1">
                    <h4 class="mb-0 me-2">{{ $presenceEmployee->total_izin }}</h4>
                    <!-- <small class="text-danger mt-1">-18%</small> -->
                  </div>
                  <!-- <small>Yearly Project</small> -->
                </div>
                <a href="{{ route('presences') }}">
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
                        <h5 class="mb-0">{{ $presenceEmployee->total_karyawan }}</h5>
                        <small class="text-success">({{ $presenceEmployee->total_hadir }} hadir)</small>
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
                      <h5 class="mb-0">{{ $products->sold < 999999 ? ($products->sold_formatted . ' botol') : $products->sold_formatted }}</h5>
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
                      <div class="small mb-1">Pengeluaran 2 pekan</div>
                      <h5 class="mb-0">{{ \App\Helpers\Fun::getExpenditures() }}</h5>
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
                      <div class="small mb-1">Laba 2 pekan</div>
                      <h5 class="mb-0">{{ \App\Helpers\Fun::getProfit() }}</h5>
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
              <h5 class="card-title m-0 me-2">List Penjualan Hari Ini</h5>
              <h6 class="m-0">{{ \App\Helpers\Muwiza::today() }}</h6>
            </div>
            <div class="table-responsive">
              <table class="table">
                <thead class="table-light">
                  <tr>
                    <th class="text-truncate">Nama SPG</th>
                    <th class="text-truncate">Jabatan</th>
                    <th class="text-truncate">Penjualan</th>
                    <th class="text-truncate">Pendapatan</th>
                    <th class="text-truncate">Status Target</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($sallesment as $spg)
                  @php
                      $sale = $spg->selling()->whereDate('created_at', now());
                      $selling = $sale->sum('qty');
                      $income = $sale->sum('total');
                  @endphp
                    <tr>
                      <td class="text-truncate">{{ $spg->name }}</td>
                      <td class="text-truncate">{{ $spg->access->name }}</td>
                      <td class="text-truncate">{{ \App\Helpers\Muwiza::ribuan($selling) }} botol</td>
                      <td class="text-truncate">{{ \App\Helpers\Muwiza::rupiah($income) }}</td>
                      <td class="text-truncate">
                        @if ($spg->access_id == 6)
                            @if ($selling >= $targetSPGFreelancer)
                              <div class="badge bg-label-success">
                                <i class="mdi mdi-check me-1"></i> Tercapai
                              </div>
                            @else
                              <div class="badge bg-label-danger">
                                <i class="mdi mdi-close-circle-outline me-1"></i> Belum tercapai
                              </div>
                            @endif
                        @else
                           <div class="badge bg-label-warning">
                              <i class="ti ti-clock me-1"></i> Tidak ditarget
                            </div>
                        @endif
                        
                      </td>
                      
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
