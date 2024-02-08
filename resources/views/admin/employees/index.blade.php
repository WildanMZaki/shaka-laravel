@extends('layouts.app')

@section('title', 'List Karyawan')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
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
                            <div class="row g-1">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Nomor Whatsapp</label>
                                    <input type="text" name="phone" class="form-control detail" placeholder="Nomor Whatsapp" readonly/>
                                </div>                        
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control detail" placeholder="Email" readonly/>
                                </div>
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
                                <img src="{{ asset('assets/img/avatars').'/'.rand(1,7).'.png' }}" alt="Avatar" id="preview-foto-karyawan" class="img-fluid store">
                            </div>
                            <div class="col-lg-9 col-12 mb-3">
                                <div class="row mt-2 g-1">
                                    <div class="col-lg-6 col-12 mb-3">
                                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control store" placeholder="Masukkan nama karyawan" />
                                        <span class="invalid-feedback" id="name-invalid-msg"></span>
                                    </div>
                                    <div class="col-lg-6 col-12 mb-3">
                                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                        <select name="position" id="position" class="form-select store">
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="position-invalid-msg"></span>
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
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Edit Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-edit" action="{!! route('employee.update') !!}" method="post">
                    @method('PUT')
                    <input type="hidden" name="id" class="update">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-3 d-flex d-lg-block justify-content-center">
                                <img src="{{ asset('assets/img/avatars').'/'.rand(1,7).'.png' }}" alt="Avatar" id="preview-edit-foto-karyawan" class="img-fluid update">
                            </div>
                            <div class="col-lg-9 col-12 mb-3">
                                <div class="row mt-2 g-1">
                                    <div class="col-lg-6 col-12 mb-3">
                                        <label class="form-label">Nama <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control update" placeholder="Masukkan nama karyawan" />
                                        <span class="invalid-feedback" id="name-update-invalid-msg"></span>
                                    </div>
                                    <div class="col-lg-6 col-12 mb-3">
                                        <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                        <select name="position" id="position-update" class="form-select update">
                                            @foreach ($positions as $position)
                                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="invalid-feedback" id="position-update-invalid-msg"></span>
                                    </div>
                                </div>
                                <div class="row g-1 my-lg-3 mt-2 mt-lg-0">
                                    <div class="col-lg-6">
                                        <label for="Foto" class="form-label">Tambah Foto (Opsional)</label>
                                        <input type="file" name="photo" id="photo-update" class="form-control update wize-upload-image" data-wz-target="#preview-edit-foto-karyawan">
                                        <span class="invalid-feedback" id="photo-update-invalid-msg"></span>
                                    </div>
                                    <div class="col-lg-6">
                                        <label class="form-label">NIK <span class="text-danger">*</span></label>
                                        <input type="text" name="nik" class="form-control update" placeholder="Masukkan nik karyawan" oninput="mustDigit(this)" />
                                        <span class="invalid-feedback" id="nik-update-invalid-msg"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-1">
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Nomor Whatsapp <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control update" placeholder="Masukkan nomor whatsapp" oninput="mustDigit(this)" />
                                <span class="invalid-feedback" id="phone-update-invalid-msg"></span>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="text" name="password" class="form-control update" placeholder="Masukkan password untuk karyawan" />
                                <span class="invalid-feedback" id="password-update-invalid-msg"></span>
                            </div>
                            <div class="col-lg-4 col-12 mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control update" placeholder="Masukkan email" />
                                <span class="invalid-feedback" id="email-update-invalid-msg"></span>
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

    <div class="modal fade" id="modal-import" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="">Impor Data Karyawan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-import" action="" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="input-group">
                                <input type="file" name="excel" class="form-control" id="inputGroupFile04" aria-describedby="DownloadTemplate" aria-label="Upload">
                                <button class="btn btn-outline-secondary" type="button" id="DownloadTemplate">
                                    <i class="ti ti-download"></i> Template
                                </button>
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
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
@endpush

@push('js')
    <script>let eximUtil = false;</script>
    @if (env("IS_PAID_MORE", false))
        <script>
        eximUtil = [
            {
                custom: null,
                icon: "ti ti-file-export",
                color: "btn-label-success",
                text: "Export",
                action: () => {
                    window.location.href = '{{ route("employees.export") }}';
                },
            },
            {
                custom: null,
                icon: "ti ti-file-import",
                color: "btn-label-info",
                text: "Import",
                action: () => {
                    $('#modal-import').modal('show');
                },
            },
        ];
        </script>
    @endif

    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>
    <script src="{{ asset('libs') }}/wizecode/applier.js"></script>
    <script>
        const wize = new Wize();
        const wizeTable = new WizeTable();
        const detailImgDefault = $('img.detail').attr('src');
        const storeImgDefault = $('img.store').attr('src');
        const updateImgDefault = $('img.update').attr('src');
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
                    text: "Tambah",
                    action: () => {
                        $('#modal-add').modal('show');
                    },
                },
                btns: eximUtil,
            })
            wize.activate_tooltips();
        });

        $(document).on('click', '.btn-detail', function() {
            const id = $(this).data('id');
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
                    $('#modal-detail').modal('show');
                },
            });
        });

        $(document).on('click', '.btn-edit', function() {
            const id = $(this).data('id');
            const url = '{{ route("employee.detail", "") }}';
            wize.ajax({
                url: url + '/' + id,
                method: 'GET',
                successDefault: false,
                addon_success: (employee) => {
                    $('img.update').attr('src', (employee.photo ? STORAGE + employee.photo : detailImgDefault));
                    Swal.close();
                    $('.update[name="id"]').val(employee.id);
                    $('.update[name="name"]').val(employee.name);
                    $('.update[name="nik"]').val(employee.nik);
                    $('.update[name="phone"]').val(employee.phone);
                    $('.update[name="email"]').val(employee.email);
                    $('.update[name="position"]').val(employee.position_id);
                    $('#modal-edit').modal('show');

                    const inputElement = $('.update[name="name"]')[0];
                    inputElement.setSelectionRange(inputElement.value.length, inputElement.value.length);            
                    inputElement.focus();
                },
            });
        });


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
                    $('img.store').attr('src', storeImgDefault);
                },
            });
        });        
        
        $('#form-edit').on('submit', function(e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const data = new FormData(this);
            wize.ajax({
                url: url,
                data: data,
                method: "POST",
                inputSelector: '.update[name="{key}"]',
                modalSelector: '#modal-edit',
                addon_success: (data) => {
                    wizeTable.reload();
                    $(this).get(0).reset();    
                    $('img.udpate').attr('src', updateImgDefault);
                },
            });
        });        

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
