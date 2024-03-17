@extends('layouts.app')

@section('title', 'Report Teams')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{!! route('reports.teams') !!}" method="get" id="form-filter">
                            <h4 class="card-title">Filter</h4>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label for="sales_daterange" class="form-label">Rentang Tanggal</label>
                                    <input type="text" id="sales_daterange" name="sales_daterange" value="" class="form-control">
                                    <input type="hidden" name="start_date" id="start_date" value="{{ $start_date }}">
                                    <input type="hidden" name="end_date" id="end_date" value="{{ $end_date }}">
                                </div>
                                <div class="col-lg-2 d-flex justify-content-end align-items-end mb-3">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="ti ti-adjustments-horizontal me-2"></i> Terapkan
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        @foreach ($result as $date => $teams)
            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>{{ \App\Helpers\Muwiza::longDate($date) }}</h4>
                            <div class="row">
                                @php $no = 1; @endphp
                                @foreach ($teams as $leader_id => $members)
                                    <div class="col-lg-4 p-2 mb-3">
                                        <div class="border rounded-3 shadow-sm p-2">
                                            <h5 class="text-center m-0 p-0 mb-2">Team {{ $no }}</h5>
                                            <h6>Leader: {{ $members[0]->leader->name }}</h6>
                                            <table class="table text-center">
                                                <thead>
                                                    <tr>
                                                        <th>SPG</th>
                                                        <th>Penjualan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $total = 0; @endphp
                                                    @foreach ($members as $member)
                                                        @php
                                                            $qty = $member->spg->selling()->whereDate('created_at', $date)->whereIn('status', ['done', 'processed'])->sum('qty');
                                                            $total += intval($qty);
                                                        @endphp
                                                        <tr>
                                                            <td>{{ $member->spg->name }}</td>
                                                            <td>{{ $qty }}</td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <tr>
                                                            <td>Total</td>
                                                            <td>{{ \App\Helpers\Muwiza::ribuan($total) }}</td>
                                                        </tr>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    @php $no++; @endphp
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

@endsection

@push('jsvendor')
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
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
            const startDate = moment($('#start_date').val(), 'YYYY-MM-DD').format('DD MMM YYYY');
            const endDate = moment($('#end_date').val(), 'YYYY-MM-DD').format('DD MMM YYYY');
            
            $(function() {
                $('input[name="sales_daterange"]').daterangepicker({
                    opens: 'left',
                    startDate: startDate,
                    endDate: endDate,
                    locale: {
                        format: 'DD MMM YYYY',
                    },
                }, function(start, end, label) {
                    const strDate = start.format('YYYY-MM-DD');
                    const eDate = end.format('YYYY-MM-DD');
                    $('#start_date').val(strDate);
                    $('#end_date').val(eDate);
                });
            });

            $('#sales_date').flatpickr({
                enableTime: false,
                dateFormat: "j M Y",
                defaultDate: new Date(),
                maxDate: new Date(),
            });
        });

    </script>
@endpush
