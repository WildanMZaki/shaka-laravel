@extends('layouts.app')

@section('title', 'Insentif Bulanan')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{!! route('sallaries.monthly') !!}" method="get" id="form-filter">
                            <h4 class="card-title">Filter</h4>
                            <div class="row">
                                <div class="col-lg-2">
                                    <label class="form-label">Tahun</label>
                                    <select name="year_select" class="form-select change-period filter" id="year_select">
                                        @foreach ($yearsOption as $year)
                                            <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label">Bulan</label>
                                    <select name="month_select" class="form-select change-period filter" id="month_select">
                                        @foreach ($monthsOption as $month)
                                            <option value="{{ $month->value }}" {{ $month->value == intval($monthSelected) ? 'selected' : '' }}>{{ $month->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label for="employee_id" class="form-label">Berdasarkan Karyawan</label>
                                    <select class="select-merk store form-select" name="employee_id" id="employee_id" data-placeholder="Pilih Merk Barang" data-allow-clear="1">
                                        <option value="" {{ $employeeSelected ? "" : "selected"}}>Semua Karyawan</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $employeeSelected == $employee->id ? "selected" : ""}}>{{ $employee->name }} | {{ $employee->access->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 offset-lg-3 d-flex justify-content-end align-items-end mb-3">
                                    <button class="btn btn-primary" type="submit">Terapkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table" id="wize-table">
                            <thead>
                                <tr>
                                    <th>Periode</th>
                                    <th>Karyawan</th>
                                    <th>Penjualan</th>
                                    <th>Insentif</th>
                                    <th>Dihitung Pada</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! 
                                    $table->orderColumns([
                                        'period', 'employee', 'sales', 'insentive', 'counted_at',
                                    ])->resultHTML() 
                                !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Hitung Insentif Bulanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label" for="start_date">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control count">
                            <span class="invalid-feedback" id="start_date-invalid-msg"></span>
                        </div>
                    </div>
                    <div class="row mb-3 justify-content-end">
                        <div class="col d-flex justify-content-end">
                            <button type="button" class="btn btn-info" id="start-btn" data-url="{!! route('sallaries.monthly.generate') !!}">
                                <i class="ti ti-player-play"></i> Mulai Hitung
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('jsvendor')
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
@endpush

@push('js')
    <script src="{{ asset('libs') }}/wizecode/applier.js"></script>
    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>
    <script>
        const wize = new Wize();
        const wizeTable = new WizeTable();
        let table;
 
        $(document).ready(() => {
            wizeTable.init({
                title: 'Insentif Bulanan',
                columns: [
                    'period', 'employee', 'sales', 'insentive', 'counted_at',
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-calculator",
                    color: "btn-primary",
                    text: "Hitung Insentif Bulanan",
                    action: () => {
                        $('#modal-add').modal('show');
                    },
                },
            })
            wize.activate_tooltips();

            $('#start_date').flatpickr({
                enableTime: false,
                dateFormat: "j M Y",
                defaultDate: new Date('{{ $recomendedDate }}'),
                minDate: new Date('{{ $latestInsentive }}'),
                maxDate: new Date().setDate(new Date().getDate() - 20),
            });
        });

        $('#start-btn').on('click', function(e) {
            e.preventDefault();
            const url = $(this).data('url');

            const start_date = $('#start_date').val();
            const data = {start_date};

            wize.ajax({
                url,
                data,
                method: "POST",
                inputSelector: '.count[name="{key}"]',
                modalSelector: '#modal-add',
                addon_success: (resp) => {
                    wizeTable.reload();
                },
            });
        });

    </script>
@endpush
