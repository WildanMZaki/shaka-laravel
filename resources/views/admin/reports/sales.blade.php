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
                    <table class="table table-bordered table-sm rounded-1">
                        <thead>
                            <tr>
                                <th>SPG</th>
                                @php
                                    $supTot = 0;
                                    $totEachDates = [];
                                @endphp
                                @foreach ($dates as $date)
                                    @php $totEachDates[$date] = 0; @endphp
                                    <th class="px-0 text-center">{{ date('d', strtotime($date)) }}</th>
                                @endforeach
                                <th class="text-center px-1">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                @php $total = 0; @endphp
                                @foreach ($dates as $date)
                                    @php
                                        $qty = $user->selling()->whereDate('created_at', $date)->sum('qty');
                                        $totEachDates[$date] += $qty;
                                        $total += intval($qty);
                                        $showQty = (!intval($qty)) ? '' : $qty;
                                        $col = 'info';
                                        if ($user->access_id == 6) {
                                            $col = ($qty >= $target_default) ? 'success' : 'danger';
                                        }
                                    @endphp
                                    <td class="text-{{ $col }} px-0 text-center">{{ $showQty }}</td>
                                @endforeach
                                <td class="text-center px-1">{{ \App\Helpers\Muwiza::ribuan($total) }}</td>
                            </tr>
                            @php $supTot += $total; @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <th>Total</th>
                            @foreach ($dates as $date)
                                <td class="text-center px-1">{{ ($totEachDates[$date] != 0) ? \App\Helpers\Muwiza::ribuan($totEachDates[$date]) : '' }}</td>
                            @endforeach
                            <th class="text-center px-1">{{ \App\Helpers\Muwiza::ribuan($supTot) }}</th>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
