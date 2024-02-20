@extends('layouts.app')

@section('title', 'Kasbon')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{!! route('kasbons') !!}" method="get">
                        <h4 class="card-title">Filter</h4>
                        <div class="row">
                            <div class="col-lg-4 mb-3">
                                <label for="kasbon_daterange" class="form-label">Rentang Tanggal Kasbon</label>
                                <input type="text" id="kasbon_daterange" name="kasbon_daterange" value="" class="form-control">
                                <input type="hidden" name="start_date" id="start_date" value="{{ $start_date }}">
                                <input type="hidden" name="end_date" id="end_date" value="{{ $end_date }}">
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label for="employee_id" class="form-label">Berdasarkan Karyawan</label>
                                <select class="select-merk store form-select" name="employee_id" id="employee_id" data-placeholder="Pilih Karyawan" data-allow-clear="1">
                                    <option value="" {{ $employeeSelected ? "" : "selected"}}>Semua Karyawan</option>
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ $employeeSelected == $employee->id ? "selected" : ""}}>{{ $employee->name }} | {{ $employee->access->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3 mb-3">
                                <label for="type_filter" class="form-label">Berdasarkan Tipe</label>
                                <select class="select-merk store form-select" name="type_filter" id="type_filter" data-placeholder="Pilih Tipe" data-allow-clear="1">
                                    <option value="" {{ $typeSelected ? "" : "selected"}}>Semua Tipe</option>
                                    <option value="keep" {{ $typeSelected == 'keep' ? "selected" : ""}}>Keep</option>
                                    <option value="kasbon" {{ $typeSelected == 'kasbon' ? "selected" : ""}}>Kasbon</option>
                                </select>
                            </div>
                            <div class="col-lg-2 d-flex align-items-end justify-content-end mb-3">
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
                                <td id="select-all-container"></td>
                                <th>Tanggal</th>
                                <th>Karyawan</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            {!!
                                $table->orderColumns([
                                    'id', 'tanggal', 'employee', 'nominal', 'keterangan', 'status', 'actions'
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Tambah Data Kasbon</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-add" action="{!! route('kasbons.manual') !!}" method="post">
                <div class="modal-body">
                    <div class="row g-1">
                        <div class="col-lg-6 mb-3">
                            <label for="user_id" class="form-label">Karyawan <span class="text-danger">*</span></label>
                            <select class="select-user store form-select" name="user_id" id="user_id" data-placeholder="Pilih Karyawan" data-allow-clear="1">
                                <option value="" selected disabled>Pilih Karyawan</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} | {{ $employee->access->name }}</option>
                                @endforeach
                            </select>
                            <span class="invalid-feedback" id="user_id-invalid-msg"></span>
                            @empty($employees)
                                <small class="text-danger">*** Ups.. sepertinya belum ada karyawan yang ditambahkan</small>
                            @endempty
                        </div>
                        <div class="col-lg-6 mb-3">
                            <label for="type" class="form-label">Tipe <span class="text-danger">*</span></label>
                            <select class="select-type store form-select" name="type" id="type" data-placeholder="Pilih Tipe Kasbon" data-allow-clear="1">
                                <option value="keep" selected>Keep</option>
                                <option value="kasbon">Kasbon</option>
                            </select>
                            <span class="invalid-feedback" id="type-invalid-msg"></span>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-lg-6 mb-3">
                            <label class="form-label" for="kasbon_date">Tanggal kasbon <span class="text-danger">*</span></label>
                            <input type="date" name="kasbon_date" id="kasbon_date" class="form-control store">
                            <span class="invalid-feedback" id="kasbon_date-invalid-msg"></span>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="nominal">Nominal<span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="rp-addon">Rp.</span>
                                <input type="text" class="form-control store" id="nominal" name="nominal" placeholder="Masukkan nominal kasbon" aria-label="Nominal kasbon" aria-describedby="rp-addon" oninput="mustInRupiahCurrency(this)">
                                <span class="invalid-feedback" id="nominal-invalid-msg"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="note" class="form-label">Keterangan <i>(Opsional)</i></label>
                            <input type="text" class="form-control store" id="note" name="note" placeholder="Masukkan keterangan kasbon">
                            <span class="invalid-feedback" id="note-invalid-msg"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('jsvendor')
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/moment/moment.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js"></script>
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
                $('input[name="kasbon_daterange"]').daterangepicker({
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

            wizeTable.init({
                title: 'Riwayat Kasbon',
                url_delete: '{!! route("kasbons.delete") !!}',
                columns: [
                    'tanggal', 'employee', 'nominal', 'keterangan', 'status', 'actions'
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-plus",
                    color: "btn-primary",
                    text: "Tambah Data Kasbon",
                    action: () => {
                        $('#modal-add').modal('show');
                    },
                },
            })
            wize.activate_tooltips();

            $('#kasbon_date').flatpickr({
                enableTime: false,
                dateFormat: "j M Y",
                defaultDate: new Date(),
                maxDate: new Date(),
            });
        });

        $('#form-add').on('submit', function(e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const nominal = validInt($('.store[name="nominal"]').val());
            const data = {
                user_id: $('.store[name="user_id"]').val(),
                type: $('.store[name="type"]').val(),
                kasbon_date: $('.store[name="kasbon_date"]').val(),
                nominal: nominal ? nominal : '',
                note: $('.store[name="note"]').val(),
            };

            wize.ajax({
                url,
                data,
                method: "POST",
                inputSelector: '.store[name="{key}"]',
                modalSelector: '#modal-add',
                addon_success: (resp) => {
                    wizeTable.reload();
                    $('.store[name="user_id"]').val('');
                    $('.store[name="nominal"]').val('');
                    $('.store[name="note"]').val('');
                },
            });
        });

        $(document).on('click', '.btn-change-status', function (e) {
            const id = $(this).data('id');
            const status = $(this).data('status');
            const titles = {
                approved: 'Setujui pengajuan kasbon?',
                rejected: 'Tolak pengajuan kasbon?',
                paid: 'Tandai terbayar?',
            };
            const title = titles[status];
            Swal.fire({
                text: title,
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
                        url: '{!! route("kasbons.change_status") !!}',
                        method: 'PATCH',
                        data: {
                            id: id,
                            status: status,
                        },
                        addon_success: (response) => {
                            wizeTable.reload();
                        } 
                    });
                }
            });
        });

    </script>
@endpush