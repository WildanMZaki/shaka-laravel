@extends('layouts.app')

@section('title', 'Penjualan')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
    <style>
        .progress {
            background-color: #d8d8d8;
            border-radius: 20px;
            position: relative;
            margin: 15px 0;
            height: 30px;
            width: 100%;
        }

        .progress-done {
            background: linear-gradient(to left, rgb(121, 169, 237), rgb(77, 143, 237));
            box-shadow: 0 3px 3px -5px rgb(30, 119, 243), 0 2px 5px rgb(30, 119, 243);
            border-radius: 20px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            width: 0;
            opacity: 0;
            transition: 1s ease 0.3s;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{!! route('sallaries.list') !!}" method="get" id="form-filter">
                            <h4 class="card-title">Filter</h4>
                            <div class="row">
                                <div class="col-lg-3 mb-3">
                                    <label for="employee_id" class="form-label">Berdasarkan Karyawan</label>
                                    <select class="select-merk store form-select" name="employee_id" id="employee_id" data-placeholder="Pilih Merk Barang" data-allow-clear="1">
                                        <option value="" {{ $employeeSelected ? "" : "selected"}}>Semua Karyawan</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $employeeSelected == $employee->id ? "selected" : ""}}>{{ $employee->name }} | {{ $employee->access->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 d-flex justify-content-end align-items-end mb-3">
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
                                    <th>Pokok</th>
                                    <th>Insentif</th>
                                    <th>Kasbon</th>
                                    <th>Total</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! 
                                    $table->orderColumns([
                                        'period', 'employee', 'sales', 'main', 'total_insentif', 'kasbon', 'total', 'actions',
                                    ])->resultHTML() 
                                !!}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7">Total</td>
                                    <td id="totalSallary" class="fw-bold">{{ $totalSallary }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Hitung Penggajian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <div class="form-group">
                                <label class="form-label">Periode</label>
                                <div class="d-flex justify-content-between">
                                    <div class="item">
                                        <input type="text" name="period" class="form-control count" placeholder="" value="{{ $period }}" readonly />
                                        <span class="invalid-feedback" id="period-invalid-msg"></span>
                                    </div>
                                    <button type="button" class="btn btn-info" id="start-btn" data-url="{!! route('sallaries.generate') !!}">
                                        <i class="ti ti-player-play"></i> Mulai Hitung
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="progress">
                        <div class="progress-done">
                            0 %
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
        let intervalMonitoringProgressId;

        function setProgressDone(progress = 0) {
           const $progress = $('.progress-done');

            if ($progress.length > 0) {
                $progress.each(function() {
                    const $this = $(this);
                    $this.html(`${progress} %`);
                    $this.css('width', progress + '%');
                    $this.css('opacity', 1);
                });
            } else {
                console.error('Element with class "progress-done" not found.');
            }
        }

        function startMonitoring(intervalTime = 2000) {
            const monitoringURL = '{!! route("sallaries.monitor") !!}'
            intervalMonitoringProgressId = setInterval(function() {
                wize.ajax({
                    url: monitoringURL,
                    method: 'get',
                    successDefault: false,
                    showLoading: false,
                    addon_success: (response) => {
                        setProgressDone(response.progress);
                        if (response.progress == 100) {
                            stopMonitoring();
                            $('#modal-add').modal('hide');
                            wizeTable.reload();
                        }
                    },
                });
            }, intervalTime);
        }

        // Function to stop the action
        function stopMonitoring() {
            clearInterval(intervalMonitoringProgressId);
            console.log('Action stopped');
        }

        $(document).ready(() => {
            const startDate = moment($('#start_date').val(), 'YYYY-MM-DD').format('DD MMM YYYY');
            const endDate = moment($('#end_date').val(), 'YYYY-MM-DD').format('DD MMM YYYY');

            setProgressDone();

            wizeTable.init({
                title: 'Riwayat Penggajian',
                columns: [
                    'period', 'employee', 'sales', 'main', 'total_insentif', 'total', 'kasbon',  'actions',
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-calculator",
                    color: "btn-primary",
                    text: "Hitung Gaji",
                    action: () => {
                        $('#modal-add').modal('show');
                    },
                },
                callback_reload: (resp) => {
                    $('#totalSallary').html(resp.totalSallary);
                },
            })
            wize.activate_tooltips();

            $('#sales_date').flatpickr({
                enableTime: false,
                dateFormat: "j M Y",
                defaultDate: new Date(),
                maxDate: new Date(),
            });
        });

        $('#start-btn').on('click', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            $(this).addClass('d-none');
            wize.ajax({
                url,
                data: {},
                method: "POST",
                addon_success: (resp) => {
                    startMonitoring();
                },
            });
        });

    </script>
@endpush
