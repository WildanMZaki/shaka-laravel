@extends('layouts.app')

@section('title', 'List Karyawan')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <table class="table" id="wize-table">
                            <thead>
                                <tr>
                                    <td></td>
                                    <th>Nama</th>
                                    <th>Nomor Whatsapp</th>
                                    <th>Email</th>
                                    <th>Jabatan</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! $rows !!}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-add" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Tambah Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-add" action="{!! route('employee.store') !!}" method="post">
                    <input type="hidden" value="" name="id" class="store">
                    <div class="modal-body">
                        <div class="row g-1">
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control store" placeholder="Masukkan nama karyawan" />
                                <span class="invalid-feedback" id="name-invalid-msg"></span>
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" name="nik" class="form-control store" placeholder="Masukkan nik karyawan" oninput="mustDigit(this)" />
                                <span class="invalid-feedback" id="nik-invalid-msg"></span>
                            </div>
                        </div>
                        <div class="row g-1">
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Nomor Whatsapp <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control store" placeholder="Masukkan nomor whatsapp" oninput="mustDigit(this)" />
                                <span class="invalid-feedback" id="phone-invalid-msg"></span>
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control store" placeholder="Masukkan email" />
                                <span class="invalid-feedback" id="email-invalid-msg"></span>
                            </div>
                        </div>
                        <div class="row g-1">
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select name="position" id="position" class="form-select store">
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}">{{ $position->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="position-invalid-msg"></span>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Pilih Team Leader (Untuk Sales)<span class="text-danger">*</span></label>
                                <select name="tl_id" id="tl_id" class="form-select apply-select2 store" {{ count($team_leaders) ? '' : "disabled"}}>
                                    @foreach ($team_leaders as $team_leader)
                                        <option value="{{ $team_leader->id }}">{{ $team_leader->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="tl_id-invalid-msg"></span>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <label for="Foto" class="form-label">Tambah Foto (Opsional)</label>
                                <input type="file" name="photo" id="photo" class="form-control store">
                                <span class="invalid-feedback" id="photo-invalid-msg"></span>
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
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Edit Barang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-update" action="{!! route('product.update') !!}" method="post">
                    <input type="hidden" value="" name="id" class="update">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Merk</label>
                                <input type="text" name="merk" class="form-control update" placeholder="Edit Merk Barang" />
                                <span class="invalid-feedback" id="merk-invalid-msg"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label class="form-label">Harga Jual</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="rp-addon">Rp.</span>
                                    <input type="text" class="form-control update" id="sell_price" name="sell_price" placeholder="Edit Harga Jual" aria-label="Harga Jual" aria-describedby="rp-addon" oninput="mustInRupiahCurrency(this)">
                                    <span class="invalid-feedback" id="sell_price-invalid-msg"></span>
                                </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
@endpush

@push('js')
    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>
    <script src="{{ asset('libs') }}/wizecode/select2-caller.js"></script>
    <script>
        const wize = new Wize();
        const wizeTable = new WizeTable();
        let table;
        $(document).ready(() => {
            wizeTable.init({
                title: 'Daftar Karyawan',
                url_delete: '{!! route("employees") !!}',
                columns: [
                    'name', 'phone', 'email', 'position', 'status', 'actions'
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-user-plus",
                    color: "btn-primary",
                    text: "Tambah Karyawan",
                    action: () => {
                        $('#modal-add').modal('show');
                    },
                }
            })
            wize.activate_tooltips();
        });

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const merk = $(this).data('merk');
            const sell_price = $(this).data('sell_price');
            $('#modal-edit').find('input[name="id"].update').val(id);
            $('#modal-edit').find('input[name="merk"].update').val(merk);
            $('#modal-edit').find('input[name="sell_price"].update').val(sell_price);
            $('#modal-edit').modal('show')
        });
        $('#modal-edit').on('shown.bs.modal', function() {
            const inputElement = $(this).find('input[name="merk"]')[0];
            inputElement.setSelectionRange(inputElement.value.length, inputElement.value.length);
            // Setel fokus ke input, ini akan otomatis ke nilai terakhir
            inputElement.focus();
        })


        $('#form-update').on('submit', function(e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const data = {
                id: $('input[name="id"].update').val(),
                merk: $('input[name="merk"].update').val(),
                sell_price: validInt($('input[name="sell_price"].update').val()),
            };

            wize.ajax({
                url,
                data,
                method: "PUT",
                inputSelector: 'input[name="{key}"].update',
                modalSelector: '#modal-edit',
                addon_success: (data) => {
                    wizeTable.reload();
                },
            });
        })


        $(document).on('click', '.btn-active-control', function() {
            const title = $(this).attr('title') ?? $(this).data('bsOriginalTitle');
            const id = $(this).data('id');
            Swal.fire({
                text: `${title} karyawan`,
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
                        url: '{!! route("employee.active_control") !!}',
                        method: 'PATCH',
                        data: {
                            id: id,
                        },
                        addon_success: (data) => {
                            wizeTable.reload();
                        }
                    });
                }
            });
        })
    </script>
@endpush
