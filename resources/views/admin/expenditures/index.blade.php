@extends('layouts.app')

@section('title', 'Penjualan')

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
                        <form action="{!! route('expenditures') !!}" method="get">
                            <h4 class="card-title">Filter</h4>
                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label for="expenditures_daterange" class="form-label">Rentang Tanggal Penjualan</label>
                                    <input type="text" id="expenditures_daterange" name="expenditures_daterange" value="" class="form-control">
                                    <input type="hidden" name="start_date" id="start_date" value="{{ $start_date }}">
                                    <input type="hidden" name="end_date" id="end_date" value="{{ $end_date }}">
                                </div>
                                <div class="col-lg-3 mb-3">
                                    <label for="leader_id" class="form-label">Berdasarkan Leader</label>
                                    <select class="select-merk store form-select" name="leader_id" id="leader_id" data-placeholder="Pilih Merk Barang" data-allow-clear="1">
                                        <option value="" {{ $leaderSelected ? "" : "selected"}}>Semua leader</option>
                                        @foreach ($leaders as $leader)
                                            <option value="{{ $leader->id }}" {{ $leaderSelected == $leader->id ? "selected" : ""}}>{{ $leader->name }} | {{ $leader->access->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-2 offset-lg-3 d-flex align-items-end justify-content-end mb-3">
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
                                    <th>Nama Leader</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! 
                                    $table->orderColumns([
                                        'id', 'tanggal', 'leader_name', 'nominal', 'keterangan',
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
                    <h5 class="modal-title" id="">Tambah Pengeluaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-add" action="{!! route('expenditures.store') !!}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="user_id" class="form-label">Pilih Leader <span class="text-danger">*</span></label>
                                <select class="select-merk store form-select" name="user_id" id="user_id" data-placeholder="Pilih Merk Barang" data-allow-clear="1">
                                    <option value="" selected disabled>Pilih leader</option>
                                    @foreach ($leaders as $leader)
                                        <option value="{{ $leader->id }}">{{ $leader->name }} | {{ $leader->access->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="user_id-invalid-msg"></span>
                                @empty($leaders)
                                    <small class="text-danger">*** Ups.. sepertinya belum ada leader yang ditambahkan</small>
                                @endempty
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="expenditures_date">Tanggal Pengeluaran <span class="text-danger">*</span></label>
                                <input type="date" name="expenditures_date" id="expenditures_date" class="form-control store">
                                <span class="invalid-feedback" id="expenditures_date-invalid-msg"></span>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="nominal">Nominal<span class="text-danger">*</span></label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="rp-addon">Rp.</span>
                                    <input type="text" class="form-control store" id="nominal" name="nominal" placeholder="Masukkan nominal pengeluaran" aria-label="Nominal Pengeluaran" aria-describedby="rp-addon" oninput="mustInRupiahCurrency(this)">
                                    <span class="invalid-feedback" id="nominal-invalid-msg"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="note" class="form-label">Keterangan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control store" id="note" name="note" placeholder="Masukkan keterangan pengeluaran">
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
                $('input[name="expenditures_daterange"]').daterangepicker({
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
                title: 'Riwayat Pengeluaran',
                url_delete: '{!! route("expenditures.delete") !!}',
                columns: [
                    'tanggal', 'leader_name', 'nominal', 'keterangan',
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-plus",
                    color: "btn-primary",
                    text: "Tambah Pengeluaran",
                    action: () => {
                        $('#modal-add').modal('show');
                    },
                },
            })
            wize.activate_tooltips();

            $('#expenditures_date').flatpickr({
                enableTime: false,
                dateFormat: "j M Y",
                defaultDate: new Date(),
                maxDate: new Date(),
            });
        });

        $('#form-add').on('submit', function(e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const data = {
                user_id: $('.store[name="user_id"]').val(),
                expenditures_date: $('.store[name="expenditures_date"]').val(),
                nominal: validInt($('.store[name="nominal"]').val()),
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

    </script>
@endpush
