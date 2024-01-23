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

    <div class="modal fade" id="modal-detail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-3 d-flex d-lg-block justify-content-center">
                            <img src="{{ asset('assets/img/avatars').'/'.rand(1,7).'.png' }}" alt="Avatar" class="img-fluid detail">
                        </div>
                        <div class="col-lg-9 col-12 mb-3">
                            <div class="row mt-2">
                                <div class="col">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control detail" placeholder="Nama" readonly/>
                                </div>
                            </div>
                            <div class="row g-1 my-lg-3 mt-2 mt-lg-0">                                
                                <div class="col-lg-6">
                                    <label class="form-label">NIK</label>
                                    <input type="text" name="nik" class="form-control detail" placeholder="NIK" readonly/>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Jabatan</label>
                                    <input type="text" name="position" class="form-control detail" placeholder="Jabatan" readonly/>                            
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Nomor Whatsapp</label>
                            <input type="text" name="phone" class="form-control detail" placeholder="Nomor Whatsapp" readonly/>
                        </div>                        
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control detail" placeholder="Email" readonly/>
                        </div>
                        <div class="col-lg-4 mb-3">
                            <label class="form-label">Team Leader</label>
                            <input type="text" name="leader" class="form-control detail" placeholder="Team Leader" readonly/>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Submit</button>
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
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-3 d-flex d-lg-block justify-content-center">
                                <img src="{{ asset('assets/img/avatars').'/'.rand(1,7).'.png' }}" alt="Avatar" id="preview-foto-karyawan" class="img-fluid">
                            </div>
                            <div class="col-lg-9 col-12 mb-3">
                                <div class="row mt-2">
                                    <div class="col">
                                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control store" placeholder="Masukkan nama karyawan" />
                                        <span class="invalid-feedback" id="name-invalid-msg"></span>
                                    </div>
                                </div>
                                <div class="row g-1 my-lg-3 mt-2 mt-lg-0">
                                    <div class="col-lg-6">
                                        <label for="Foto" class="form-label">Tambah Foto (Opsional)</label>
                                        <input type="file" name="photo" id="photo" class="form-control store wize-upload-image" data-wz-target="#preview-foto-karyawan">
                                        <span class="invalid-feedback" id="photo-invalid-msg"></span>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" name="nik" class="form-control store" placeholder="Masukkan nik karyawan" oninput="mustDigit(this)" />
                                        <span class="invalid-feedback" id="nik-invalid-msg"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1">
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Nomor Whatsapp <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control store" placeholder="Masukkan nomor whatsapp" oninput="mustDigit(this)" />
                                <span class="invalid-feedback" id="phone-invalid-msg"></span>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="text" name="password" class="form-control store" placeholder="Masukkan password untuk karyawan" />
                                <span class="invalid-feedback" id="password-invalid-msg"></span>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control store" placeholder="Masukkan email" />
                                <span class="invalid-feedback" id="email-invalid-msg"></span>
                            </div>
                        </div>
                        <div class="row g-1">
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select name="position" id="position" class="form-select store">
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}" {{ $position->id == 4 && !count($team_leaders) ? "disabled" : "" }}>{{ $position->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="position-invalid-msg"></span>
                            </div>
                            <div class="col-lg-6 col-12 mb-3">
                                <label class="form-label">Pilih Team Leader (Untuk Sales)<span class="text-danger">*</span></label>
                                <select name="tl_id" id="tl_id" class="form-select store" {{ count($team_leaders) ? '' : "disabled"}}>
                                    <option value="">-- Pilih Team Leader --</option>
                                    @foreach ($team_leaders as $team_leader)
                                        <option value="{{ $team_leader->id }}">{{ $team_leader->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="tl_id-invalid-msg"></span>
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
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
@endpush

@push('js')
    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>
    <script src="{{ asset('libs') }}/wizecode/applier.js"></script>
    <script>
        const wize = new Wize();
        const wizeTable = new WizeTable();
        const detailImgDefault = $('img.detail').attr('src');
        let table;
        $(document).ready(() => {
            wizeTable.init({
                title: 'Daftar Karyawan',
                url_delete: '{!! route("employees.delete") !!}',
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
                },
                addon_delete: (data) => {
                    $('#tl_id').html('<option value="">-- Pilih Team Leader --</option>');
                    if (data.leaders.length > 0) {
                        $('#position option[value="4"]').removeAttr("disabled");
                        $('#position').val(4);
                        $('#tl_id').removeAttr('disabled');
                        data.leaders.forEach(leader => {
                            $('#tl_id').append(`
                                <option value="${leader.id}">${leader.name}</option>
                            `);
                        });
                    } else {
                        $('#position').val(3);
                        $('#position option[value="4"]').attr("disabled", 'disabled');
                        $('#tl_id').attr('disabled', 'disabled');
                    }
                },
            })
            wize.activate_tooltips();
        });

        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
            console.log(id);
            const url = '{{ route("employee.detail", "") }}';
            wize.ajax({
                url: url + '/' + id,
                method: 'GET',
                successDefault: false,
                addon_success: (employee) => {
                    $('img.detail').attr('src', (employee.photo ? STORAGE + employee.photo : detailImgDefault));
                    Swal.close();
                    $('.detail[name="name"]').val(employee.name);
                    $('.detail[name="nik"]').val(employee.nik);
                    $('.detail[name="phone"]').val(employee.phone);
                    $('.detail[name="email"]').val(employee.email);
                    $('.detail[name="position"]').val(employee.position);
                    $('.detail[name="leader"]').val(employee.leader);
                    $('#modal-detail').modal('show');
                },
            });
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


        $('#form-add').on('submit', function(e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const data = new FormData(this);

            wize.ajax({
                url,
                data,
                method: "POST",
                inputSelector: '.store[name="{key}"]',
                modalSelector: '#modal-add',
                addon_success: (data) => {
                    wizeTable.reload();
                    $(this).get(0).reset();
                    $('#tl_id').html('<option value="">-- Pilih Team Leader --</option>');
                    if (data.leaders.length > 0) {
                        $('#position option[value="4"]').removeAttr("disabled");
                        $('#position').val(4);
                        $('#tl_id').removeAttr('disabled');
                        data.leaders.forEach(leader => {
                            $('#tl_id').append(`
                                <option value="${leader.id}">${leader.name}</option>
                            `);
                        })
                    } else {
                        $('#position option[value="4"]').attr("disabled", 'disabled');
                        $('#tl_id').attr('disabled', 'disabled');
                    }
                },
            });
        });

        $(document).on('change', '#position', function () {
            if ($(this).val() == 4) {
                $('#tl_id').removeAttr('disabled');
            } else {
                $('#tl_id').attr('disabled', 'disabled');
            }
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
