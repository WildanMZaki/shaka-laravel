@extends('layouts.app')

@section('title', 'Report Absensi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{!! route('reports.sales') !!}" method="get" id="form-filter">
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
                            <div class="col-lg-2">
                                <label class="form-label" for="position">Jabatan</label>
                                <select name="position" class="form-select change-period filter" id="position">
                                    <option value="6,7" {{ $positionSelected == '6,7' ? 'selected' : '' }}>Semua SPG</option>
                                    <option value="6" {{ $positionSelected == '6' ? 'selected' : '' }}>SPG Freelancer</option>
                                    <option value="7" {{ $positionSelected == '7' ? 'selected' : '' }}>SPG Training</option>
                                    <option value="5" {{ $positionSelected == '5' ? 'selected' : '' }}>Leader</option>
                                </select>
                            </div>
                            <div class="col-lg-4 offset-lg-2 d-flex justify-content-end align-items-end">
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
                    <table class="table table-bordered table-sm rounded-1">
                        <thead>
                            <tr>
                                <th>Karyawan</th>
                                @foreach ($dates as $date)
                                    <th class="px-0 text-center">{{ date('d', strtotime($date)) }}</th>
                                @endforeach
                                <th class="text-center px-1">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    @foreach ($row as $key => $val)
                                        @php
                                            $isName = $key == 'name';
                                        @endphp
                                        <td class="{{ !$isName ? 'px-0 text-center' : ''}}">{{ $isName || $val == '' ? $val : \App\Helpers\Muwiza::ribuan(intval($val)) }}</td>                                        
                                    @endforeach
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
