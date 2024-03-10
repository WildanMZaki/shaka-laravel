@extends('layouts.app')

@section('title', 'Report Keuangan')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{!! route('reports.finance') !!}" method="get" id="form-filter">
                            <h4 class="card-title">Filter</h4>
                            <div class="row">
                                <div class="col-lg-2">
                                    <label class="form-label">Tahun</label>
                                    <select name="year" class="form-select change-period filter" id="year">
                                        @foreach ($yearsOption as $year)
                                            <option value="{{ $year }}" {{ $year == $yearSelected ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2">
                                    <label class="form-label">Bulan</label>
                                    <select name="month" class="form-select change-period filter" id="month">
                                        @foreach ($monthsOption as $month)
                                            <option value="{{ $month->value }}" {{ $month->value == intval($monthSelected) ? 'selected' : '' }}>{{ $month->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-4 offset-lg-4 d-flex justify-content-end align-items-end">
                                    <button class="btn btn-primary me-3" type="submit" name="submit" value="fetch">
                                        <i class="ti ti-adjustments-horizontal me-2"></i> Terapkan
                                    </button>
                                    <button class="btn btn-success" type="submit" name="submit" value="export">
                                        <i class="ti ti-file-spreadsheet me-2"></i> Export
                                    </button>
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
                                    <th>Pendapatan</th>
                                    <th>Pengeluaran</th>
                                    <th>Penggajian</th>
                                    <th>Laba</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! 
                                    $rows
                                !!}
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
        const wizeTable = new WizeTable();

        $(document).ready(() => {

            wizeTable.init({
                title: 'Laporan Keuangan',
                columns: [
                    'period', 'income', 'expenditure', 'sallaries', 'profit'
                ],
                defaultButton: false,
            })
            wize.activate_tooltips();
        });

    </script>
@endpush
