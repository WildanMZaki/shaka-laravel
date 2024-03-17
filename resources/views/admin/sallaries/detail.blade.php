@extends('layouts.app')

@section('title', 'Detail Penggajian')

@push('css')
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-lg-center">
                            <div class="iamleft">
                                <h4 class="p-0 m-0">Detail Penggajian ({{ $sallary->user->name }})</h4>
                                <p class="p-0 m-0">Periode : {{ $period }}</p>
                            </div>
                            <div class="iamright d-flex d-md-block justify-content-end">
                                <button class="btn btn-primary" id="btn-count-repeat" data-sallary_id="{{ $sallary_id }}">
                                    <i class="ti ti-repeat me-2"></i> Hitung Ulang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4>Kehadiran</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($workDays as $date)
                                @php
                                    $badgeColor = 'danger';
                                    $isAlfa = in_array($date, $presences->tanggalTidakHadir);
                                    $presence = null;
                                    if (!$isAlfa) {
                                        $result = array_filter($presences->presences->toArray(), function($element) use ($date) {
                                            return $element['date'] === $date;
                                        });
                                        $presence = reset($result);
                                        $badgeColor = $presence['flag'] == 'hadir' ? 'success' : 'warning';
                                    }
                                    $badgeColors = [
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ \App\Helpers\Muwiza::simpleDate($date) }}</td>
                                    <td>
                                        <small class="badge bg-label-{{ $badgeColor }}">{{ $isAlfa ? 'Tidak hadir' : ucfirst($presence['flag']) }}</small>
                                    </td>
                                    <td>
                                        @if ($presence !== null)
                                            <small class="badge bg-label-{{ $badgeColors[$presence['status']] }}">{{ $isAlfa ? '' : ucfirst($presence['status']) }}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-body">
                    <h4>Penjualan</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Kuantitas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($salesData as $sale)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ \App\Helpers\Muwiza::simpleDate($sale->date) }}</td>
                                    <td>{{ \App\Helpers\Muwiza::ribuan($sale->total_qty) }} Botol</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4>Piutang</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Tipe</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $no = 1;
                            @endphp
                            @foreach ($piutang as $utang)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ \App\Helpers\Muwiza::simpleDate($utang->date) }}</td>
                                    <td>{{ \App\Helpers\Muwiza::ribuan((int)$utang->nominal) }}</td>
                                    <td>{{ $utang->status }}</td>
                                    <td>{{ $utang->type }}</td>
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

@push('jsvendor')
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
@endpush

@push('js')
    <script src="{{ asset('libs') }}/wizecode/applier.js"></script>
    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>

    <script>
        const wize = new Wize();

        $(document).on('click', '#btn-count-repeat', function (e) {
            const sallary_id = $(this).data('sallary_id');
            Swal.fire({
                text: "Hitung ulang penggajian",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: "btn btn-primary",
                    cancelButton: "btn btn-outline-danger ms-1",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.value) {
                    wize.ajax({
                        url: '{!! route("sallaries.recount") !!}',
                        method: 'POST',
                        data: {sallary_id},
                        addon_success: (response) => {
                            window.location.reload();
                        } 
                    });
                }
            });
        });
    </script>
@endpush
