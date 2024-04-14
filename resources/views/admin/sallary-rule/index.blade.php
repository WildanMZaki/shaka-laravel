@extends('layouts.app')

@section('title', 'Aturan Penggajian')

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
                                <h4 class="p-0 m-0">Aturan Penggajian</h4>
                            </div>
                            <div class="iamright d-flex d-md-block justify-content-end">
                                <button class="btn btn-primary d-none" id="btn-download-contract">
                                    <i class="ti ti-download me-2"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-lg-6 col-12">
            <div class="row mb-3">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5>Aturan Umum</h5>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="#target-harian" class="form-label">Target Jual Harian</label>
                                    <div class="d-flex align-items-start">
                                        <div class="input-group me-1 has-validation">
                                            @php $v = $general['Target Jual Harian SPG Freelancer']; @endphp
                                            <input
                                                type="text"
                                                value="{{ $v }}"
                                                data-default="{{ $v }}"
                                                data-alias="Target Harian"
                                                name="target-harian"
                                                placeholder="Target Jual Harian"
                                                data-rule="Target Jual Harian SPG Freelancer"
                                                id="target-harian"
                                                class="form-control is-int observ-change"
                                                data-wz-vrules="required,number"
                                                aria-label="Target Jual Harian"
                                                aria-describedby="target-unit"
                                                oninput="mustDigit(this)"
                                            >
                                            <span class="input-group-text" id="target-unit">Botol</span>
                                            <div class="invalid-feedback" id="target-harian-invalid"></div>
                                        </div>
                                        <button class="btn btn-primary d-none save-general-rule" type="button" id="save-target-harian">Simpan</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="#gaji-botolan" class="form-label">Default Gaji Botolan</label>
                                    <div class="d-flex align-items-start">
                                        <div class="input-group me-1 has-validation">
                                            <span class="input-group-text" id="default-botolan">Rp.</span>
                                            @php $v = \App\Helpers\Muwiza::ribuan($general['Default Gaji Botolan']); @endphp
                                            <input
                                                type="text"
                                                value="{{ $v }}"
                                                data-default="{{ $v }}"
                                                data-alias="Default gaji botolan"
                                                name="gaji-botolan"
                                                placeholder="Default Gaji Botolan"
                                                data-rule="Default Gaji Botolan"
                                                id="gaji-botolan"
                                                class="form-control is-int observ-change"
                                                data-wz-vrules="required,number"
                                                aria-label="Default Gaji Botolan"
                                                aria-describedby="default-botolan"
                                                oninput="mustInRupiahCurrency(this)"
                                            >
                                            <div class="invalid-feedback" id="gaji-botolan-invalid"></div>
                                        </div>
                                        <button class="btn btn-primary d-none save-general-rule" type="button" id="save-gaji-botolan">Simpan</button>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="#bpjs-monthly" class="form-label">Nominal BPJS Bulanan</label>
                                    <div class="d-flex align-items-start">
                                        <div class="input-group me-1 has-validation">
                                            <span class="input-group-text" id="bpjs-bulanan">Rp.</span>
                                            @php $v = \App\Helpers\Muwiza::ribuan($general['Nominal BPJS Bulanan']); @endphp
                                            <input
                                                type="text"
                                                value="{{ $v }}"
                                                data-default="{{ $v }}"
                                                data-alias="Nominal BPJS bulanan"
                                                name="bpjs-monthly"
                                                placeholder="Target Jual Harian"
                                                data-rule="Nominal BPJS Bulanan"
                                                id="bpjs-monthly"
                                                class="form-control is-int observ-change"
                                                data-wz-vrules="required,number"
                                                aria-label="Default Gaji Botolan"
                                                aria-describedby="bpjs-bulanan"
                                                oninput="mustInRupiahCurrency(this)"
                                            >
                                            <div class="invalid-feedback" id="bpjs-monthly-invalid"></div>
                                        </div>
                                        <button class="btn btn-primary d-none save-general-rule" type="button" id="save-bpjs-monthly">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5>Insentif Team Leader</h5>
    
                            {{-- Uang absen --}}
                            @php
                                $data['title'] = 'Insentif Harian';
                                $data['access_id'] = 5;
                                $data['position'] = 'Team Leader';
                                $data['period'] = 'daily';
                                $data['insentives'] = $daily_insentives->filter(function ($item) {
                                    return $item->access_id == 5;
                                });
                            @endphp
                            @includeIf('admin.sallary-rule.partials.table', $data)
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            <h5>Insentif SPG Training</h5>
    
                            {{-- Uang absen --}}
                            @php
                                $data['title'] = 'Insentif Harian';
                                $data['access_id'] = 7;
                                $data['position'] = 'SPG Training';
                                $data['period'] = 'daily';
                                $data['insentives'] = $daily_insentives->filter(function ($item) {
                                    return $item->access_id == 7;
                                });
                            @endphp
                            @includeIf('admin.sallary-rule.partials.table', $data)
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="card">
                <div class="card-body">
                    <h5>Insentif SPG Freelancer</h5>

                    {{-- Uang absen --}}
                    @php
                        $data['title'] = 'Uang absen';
                        $data['access_id'] = 6;
                        $data['position'] = 'SPG Freelancer';
                        $data['period'] = 'daily';
                        $data['insentives'] = $daily_insentives->filter(function ($item) {
                            return $item->access_id == 6;
                        });
                    @endphp
                    @includeIf('admin.sallary-rule.partials.table', $data)
                    
                    {{-- Mingguan --}}
                    @php
                        $data['title'] = 'Bonus Mingguan';
                        $data['access_id'] = 6;
                        $data['position'] = 'SPG Freelancer';
                        $data['period'] = 'weekly';
                        $data['insentives'] = $weekly_insentives->filter(function ($item) {
                            return $item->access_id == 6;
                        });
                    @endphp
                    @includeIf('admin.sallary-rule.partials.table', $data)
                    
                    {{-- Bulanan --}}
                    @php
                        $data['title'] = 'Bonus Bulanan';
                        $data['access_id'] = 6;
                        $data['position'] = 'SPG Freelancer';
                        $data['period'] = 'monthly';
                        $data['type'] = 'thing';
                        $data['insentives'] = $monthly_insentives->filter(function ($item) {
                            return $item->access_id == 6;
                        });
                    @endphp
                    @includeIf('admin.sallary-rule.partials.table', $data)
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Tambah Insentif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-add" action="{!! route('rules.insentives.store') !!}" method="post">
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="position" class="form-label">Jabatan</label>
                            <input type="text" class="form-control store" name="position" id="position" placeholder="Posisi" readonly>
                            <span class="invalid-feedback" id="position-invalid-msg"></span>
                        </div>
                    </div>
                    <input type="hidden" name="access_id" id="access_id">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="period_label" class="form-label">Periode</label>
                            <input type="text" class="form-control store" name="period_label" id="period_label" placeholder="Periode" readonly>
                            <span class="invalid-feedback" id="period_label-invalid-msg"></span>
                        </div>
                    </div>
                    <input type="hidden" name="period" id="period">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="sales_qty" class="form-label store">Target <span class="text-danger">*</span></label>
                            <input type="text" class="form-control store" name="sales_qty" id="sales_qty" placeholder="Masukkan target penjualan" oninput="mustDigit(this)">
                            <span class="invalid-feedback" id="sales_qty-invalid-msg"></span>
                        </div>
                    </div>
                    <input type="hidden" name="type" id="type">
                    <div class="row" id="insentive-money-row">
                        <div class="col">
                            <label class="form-label" for="insentive">Insentif <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="rp-addon">Rp.</span>
                                <input type="text" class="form-control store" id="insentive" name="insentive" placeholder="Masukkan insentif" aria-label="Insentif" aria-describedby="rp-addon" oninput="mustInRupiahCurrency(this)">
                                <span class="invalid-feedback" id="insentive-invalid-msg"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="insentive-thing-row">
                        <div class="col">
                            <label for="insentive_thing" class="form-label">Insentif <span class="text-danger">*</span></label>
                            <input type="text" class="form-control store" name="insentive_thing" id="insentive_thing" placeholder="Masukkan insentif">
                            <span class="invalid-feedback" id="insentive_thing-invalid-msg"></span>
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

<div class="modal fade" id="modal-edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Edit Insentif</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-edit" action="{!! route('rules.insentives.update') !!}" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id" id="insentive_id">
                    <input type="hidden" name="edit_access_id" id="edit_access_id">
                    <input type="hidden" name="edit_period" id="edit_period">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_position" class="form-label">Jabatan</label>
                            <input type="text" class="form-control update" name="position" id="edit_position" placeholder="Posisi" readonly>
                            <span class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_period_label" class="form-label">Periode</label>
                            <input type="text" class="form-control update" name="period_label" id="edit_period_label" placeholder="Periode" readonly>
                            <span class="invalid-feedback"></span>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="edit_sales_qty" class="form-label">Target <span class="text-danger">*</span></label>
                            <input type="text" class="form-control update" name="edit_sales_qty" id="edit_sales_qty" placeholder="Masukkan target penjualan" oninput="mustDigit(this)">
                            <span class="invalid-feedback" id="edit_sales_qty-invalid-msg"></span>
                        </div>
                    </div>
                    <div class="row" id="insentive-money-row-edit">
                        <div class="col">
                            <label class="form-label" for="edit_insentive">Insentif <span class="text-danger">*</span></label>
                            <div class="input-group mb-3">
                                <span class="input-group-text" id="rp-addon">Rp.</span>
                                <input type="text" class="form-control update" id="edit_insentive" name="edit_insentive" placeholder="Masukkan insentif" aria-label="Insentif" aria-describedby="rp-addon" oninput="mustInRupiahCurrency(this)">
                                <span class="invalid-feedback" id="edit_insentive-invalid-msg"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row d-none" id="insentive-thing-row-edit">
                        <div class="col">
                            <label for="edit_insentive_thing" class="form-label">Insentif <span class="text-danger">*</span></label>
                            <input type="text" class="form-control update" name="edit_insentive_thing" id="edit_insentive_thing" placeholder="Masukkan insentif">
                            <span class="invalid-feedback" id="edit_insentive_thing-invalid-msg"></span>
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
@endpush

@push('js')
    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WzValidator.js"></script>

    <script>
        const wize = new Wize();
        const v = new WzValidator({
            clear: (el) => {
                $(el).removeClass('is-invalid');
                const id = $(el).attr('id');
                $(`#${id}-invalid`).html('');
                $(`#save-${id}`).attr('disabled', false);
            },
            error: (msg, el) => {
                $(el).addClass('is-invalid');
                const id = $(el).attr('id');
                $(`#${id}-invalid`).html(msg);
                $(`#save-${id}`).attr('disabled', true);
            }
        });

        $('.observ-change').on('input', function () {
            v.validate(this);

            const id = $(this).attr('id');
            const val = $(this).val();
            const def = $(this).get(0).dataset.default;
            if (val != def) {
                $(`#save-${id}`).removeClass('d-none');
            } else {
                $(`#save-${id}`).addClass('d-none');
            }
        });

        $('.save-general-rule').on('click', function () {
            const btnId = $(this).attr('id');
            const inputId = '#' + btnId.replace('save-', '');
            const rule = $(inputId).data('rule');
            const rawVal = $(inputId).val();
            const val = $(inputId).hasClass('is-int') ? validInt(rawVal) : rawVal;
            wize.ajax({
                url: '{!! route("settings.change") !!}',
                method: 'PUT',
                data: {
                    rule: rule,
                    value: val,
                },
                addon_success: (msg) => {
                    $(`#${btnId}`).addClass('d-none');
                    $(inputId).get(0).dataset.default = rawVal;
                }
            });
        });

        const periodsLabel = {
            daily: 'Harian',
            weekly: 'Mingguan',
            monthly: 'Bulanan',
        };

        $('.add-insentive').on('click', function () {
            const access_id = $(this).data('access_id');
            const period = $(this).data('period');
            let type = $(this).data('type');
            if (type == undefined) {
                type = 'money';
            }
            const position = $(this).data('position');

            $('#access_id').val(access_id);
            $('#position').val(position);

            $('#period_label').val(periodsLabel[period]);
            $('#period').val(period);

            $('#type').val(type);

            if (type == 'money') {
                $('#insentive-money-row').removeClass('d-none');
                $('#insentive-thing-row').addClass('d-none');
            } else {
                $('#insentive-money-row').addClass('d-none');
                $('#insentive-thing-row').removeClass('d-none');
            }

            $('#modal-add').modal('show');
        });

        function getId(period, access_id) {
            return `#table-body-${period}-${access_id}`;
        }
        function reload(tableId, data) {
            $(tableId).html('');
            data.forEach((ins, i) => {
                const titleId = tableId.replace('body', 'title');
                const title = ($(titleId).html()).toLowerCase();
                const deleteQuestion = `Hapus ${title} ${ins.access.name} dengan nilai ${ins.insentive}?`;
                $(tableId).append(`
                    <tr>
                    <td>${i+1}</td>
                    <td>${ins.sales_qty}</td>
                    <td>${ins.insentive}</td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-icon btn-label-warning edit-insentive"
                            data-id="${ins.id}"
                            data-access_id="${ins.access_id}"
                            data-position="${ins.access.name}"
                            data-period="${ins.period}"
                            data-type="${ins.type}"
                            data-sales_qty="${validInt(ins.sales_qty)}"
                            data-insentive="${(ins.insentive).replace('Rp ', '')}"
                            >
                            <i class="ti ti-edit"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-icon btn-label-danger delete-insentive"
                            data-id="${ins.id}"
                            data-access_id="${ins.access_id}"
                            data-period="${ins.period}"
                            data-question="${deleteQuestion}"
                            >
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
                `);
            });
            $('.edit-insentive').on('click', editingEvent);
            $('.delete-insentive').on('click', deletingEvent);
        }

        $('#form-add').on('submit', function (e) {
            e.preventDefault();

            const url = $(this).attr('action');
            const nominal = validInt($('#insentive').val());
            const data = {};
            const serialize = $(this).serialize();

            serialize.split('&').forEach(el => {
                const [key, value] = el.split('=');
                data[key] = value.replaceAll('%20', ' ');
            });

            data.insentive = nominal ? nominal : '';
            const period = $('#period').val();
            const access_id = $('#access_id').val();

            wize.ajax({
                url,
                data,
                method: "POST",
                inputSelector: '.store[name="{key}"]',
                modalSelector: '#modal-add',
                addon_success: (resp) => {
                    reload(getId(period, access_id), resp.data);
                },
            });
        });

        $('#modal-add').on('hidden.bs.modal', function () {
            $(this).find('#form-add').get(0).reset();
        });

        function editingEvent() {
            const id = $(this).data('id');
            const access_id = $(this).data('access_id');
            const period = $(this).data('period');
            const type = $(this).data('type');
            const position = $(this).data('position');
            const sales_qty = $(this).data('sales_qty');
            const insentive = $(this).data('insentive');

            $('#insentive_id').val(id);
            $('#edit_access_id').val(access_id);
            $('#edit_period').val(period);
            $('#edit_position').val(position);
            $('#edit_period_label').val(periodsLabel[period]);
            $('#edit_sales_qty').val(sales_qty);

            if (type == 'money') {
                $('#edit_insentive').val(insentive);
                $('#insentive-money-row-edit').removeClass('d-none');
                $('#insentive-thing-row-edit').addClass('d-none');
            } else {
                $('#edit_insentive_thing').val(insentive);
                $('#insentive-money-row-edit').addClass('d-none');
                $('#insentive-thing-row-edit').removeClass('d-none');
            }

            $('#modal-edit').modal('show');
        }
        $('.edit-insentive').on('click', editingEvent);

        $('#form-edit').on('submit', function (e) {
            e.preventDefault();

            const url = $(this).attr('action');
            const nominal = validInt($('#edit_insentive').val());
            const data = {};
            const serialize = $(this).serialize();

            serialize.split('&').forEach(el => {
                const [key, value] = el.split('=');
                data[key] = value.replaceAll('%20', ' ');
            });

            data.edit_insentive = nominal ? nominal : '';
            const period = $('#edit_period').val();
            const access_id = $('#edit_access_id').val();

            wize.ajax({
                url,
                data,
                method: "PUT",
                inputSelector: '.update[name="{key}"]',
                modalSelector: '#modal-edit',
                addon_success: (resp) => {
                    reload(getId(period, access_id), resp.data);
                },
            });
        });

        function deletingEvent() {
            const id = $(this).data('id');
            const period = $(this).data('period');
            const access_id = $(this).data('access_id');
            const url = '{!! route("rules.insentives.delete", "") !!}';
            const question = $(this).data('question');

            Swal.fire({
                text: question,
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
                        url: `${url}/${id}`,
                        method: 'DELETE',
                        data: {},
                        addon_success: (resp) => {
                            reload(getId(period, access_id), resp.data);
                        } 
                    });
                }
            });
        }
        $('.delete-insentive').on('click', deletingEvent);
    </script>
@endpush
