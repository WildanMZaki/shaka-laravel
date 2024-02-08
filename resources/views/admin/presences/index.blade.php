@extends('layouts.app')

@section('title', 'Detail Absensi')

@push('css')
<link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/magnific-popup/magnific-popup.css">
<style>
.text-overflow {
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-lg-center">
                            <div class="iamleft">
                                <h4 class="p-0 m-0">Kehadiran Hari Ini</h4>
                                <p class="p-0 m-0">{{ \App\Helpers\Muwiza::today() }}</p>
                            </div>
                            <div class="iamright d-flex d-md-block justify-content-end">
                                <button class="btn btn-primary btn-manual-add">
                                    <i class="ti ti-plus"></i> Tambah Manual
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-6 col-12">
                            
                        </div>
                        <div class="col-lg-6 col-12 d-flex justify-content-end">
                            <div class="form-check-reverse form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" data-rule="Auto Konfirmasi Absensi" id="auto-confirm" {{ $autoConfirm ? 'checked' : '' }}>
                                <label class="form-check-label" for="auto-confirm">Konfirmasi Otomatis</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-3">
        <div class="col-lg-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-4">
                            <h5 class="card-title">Hadir:</h5>
                        </div>
                        @if (count($presences))                        
                            <div class="col-lg-8 d-flex justify-content-end">
                                <button class="btn btn-success" id="confirm-all">
                                    <i class="ti ti-thumb-up me-1"></i> Konfirmasi Semua
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card-body" id="presences-data">
                    @if (!count($presences))
                        <div class="d-flex">
                            <div class="w-100 h-100 bg-light rounded-3 p-3 flex-fill d-flex  align-items-center justify-content-center flex-column">
                                <i class="ti ti-user-off fs-1"></i>
                                <p>
                                    Belum ada absensi masuk
                                </p>
                            </div>
                        </div>
                    @endif
                    @foreach ($presences as $presence)                        
                        <div class="row border rounded-3 p-2 g-0 mb-2 {{ $presence->status == 'pending' ? 'presence-is-pending' : ''}}">
                            @php
                                $presenceImg = asset('storage').'/'.$presence->photo;
                                $statusColor = [
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                ];
                            @endphp
                            <div class="col-4 text-center">
                                <a href="{{ $presenceImg }}" class="apply-magnific">
                                    <img src="{{ $presenceImg }}" alt="Foto Kehadiran" class="img-fluid" style="max-height: 120px">
                                </a>
                            </div>
                            <div class="col-7 px-2">
                                <b class="d-block text-overflow">{{ $presence->user->name }}</b>
                                <small>{{ $presence->user->access->name }}</small>                                
                                <h6 class="mt-2">Masuk: <u>{{ $presence->entry_at }}</u></h6>
                                <small class="badge bg-label-{{ $statusColor[$presence->status] }}">{{ ucfirst($presence->status) }}</small>
                            </div>
                            <div class="col-1">
                                <div class="dropdown">
                                <button
                                    class="btn p-0"
                                    type="button"
                                    id="transactionID"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">
                                    <i class="ti ti-dots-vertical ti-24px"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                    <a class="dropdown-item btn-presence-control" href="javascript:void(0);" data-status="approved" data-id="{{ $presence->id }}"><i class="ti ti-thumb-up me-2"></i> Konfirmasi</a>
                                    <a class="dropdown-item btn-presence-control" href="javascript:void(0);" data-status="rejected" data-id="{{ $presence->id }}"><i class="ti ti-thumb-down me-2"></i> Tolak</a>
                                </div>
                            </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @if (count($permits))
            <div class="col-lg-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-4">
                                <h5 class="card-title">Izin:</h5>
                            </div>
                            <div class="col-lg-8 d-flex justify-content-end">
                                <button class="btn btn-success" id="allow-all">
                                    <i class="ti ti-checks me-1"></i> Izinkan Semua
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="permits-data">
                        @foreach ($permits as $permit)                        
                            <div class="row border rounded-3 p-2 g-0 mb-2 {{ $permit->status == 'pending' ? 'permit-is-pending' : ''}}">
                                @php
                                    $permitImg = asset('storage').'/'.$permit->photo;
                                    $statusColor = [
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <div class="col-4 text-center">
                                    <a href="{{ $permitImg }}" class="apply-magnific">
                                        <img src="{{ $permitImg }}" alt="Foto Kehadiran" class="img-fluid" style="max-height: 120px">
                                    </a>
                                </div>
                                <div class="col-7 px-2">
                                    <b class="d-block text-overflow">{{ $permit->user->name }}</b>
                                    <small>{{ $permit->user->access->name }}</small>
                                    <h6 class="my-1">Alasan: {{ ucfirst($permit->note) }}</h6>                           
                                    <small class="badge bg-label-{{ $statusColor[$permit->status] }}">{{ ucfirst($permit->status) }}</small>
                                </div>
                                <div class="col-1">
                                    <div class="dropdown">
                                    <button
                                        class="btn p-0"
                                        type="button"
                                        id="transactionID"
                                        data-bs-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="ti ti-dots-vertical ti-24px"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                        <a class="dropdown-item btn-permit-detail"
                                            href="javascript:void(0);"
                                            data-status="detail"
                                            data-photo="{{ $permitImg }}"
                                            data-reason="{{ ucfirst($permit->flag) }}"
                                            data-note="{{ $permit->note }}"
                                            data-employee="{{ $permit->user->name }}"
                                            data-position="{{ $permit->user->access->name }}">
                                            <i class="ti ti-list-details me-2"></i> Detail
                                        </a>
                                        <a class="dropdown-item btn-permit-control" href="javascript:void(0);" data-status="approved" data-id="{{ $permit->id }}"><i class="ti ti-check me-2"></i> Setujui</a>
                                        <a class="dropdown-item btn-permit-control" href="javascript:void(0);" data-status="rejected" data-id="{{ $permit->id }}"><i class="ti ti-ban me-2"></i> Tolak</a>
                                    </div>
                                </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        @if (count($unpresences))
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-lg-6">
                                <h5 class="card-title">Belum Absen: {{ count($unpresences) }}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="unpresences-data">
                        <div class="row g-1">
                            @foreach ($unpresences as $unpresence)                        
                                <div class="col-lg-6 border shadow d-flex rounded-3 p-2 mb-2">
                                    @php
                                        $unpresenceImg = $unpresence->photo ? (asset('storage') . '/' . $unpresence->photo) : asset('assets/img/avatars').'/'.rand(1,7).'.png';
                                    @endphp
                                    <a href="{{ $unpresenceImg }}" class="apply-magnific">
                                        <img src="{{ $unpresenceImg }}" alt="" height="50" class="me-3">
                                    </a>
                                    <div class="d-flex flex-column">
                                        <h5 class="m-0 p-0">{{ $unpresence->name }}</h5>
                                        <small>{{ $unpresence->access->name }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="modal-detail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Izin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-3 d-flex d-lg-block justify-content-center">
                        <img src="" alt="Avatar" class="img-fluid detail">
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
                                <label class="form-label">Jabatan</label>
                                <input type="text" name="position" class="form-control detail" placeholder="Jabatan" readonly/>                            
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label">Alasan</label>
                                <input type="text" name="reason" class="form-control detail" placeholder="Alasan" readonly/>                            
                            </div>
                        </div>
                        <div class="row g-1">
                            <label for="permit-note" class="form-label">Catatan</label>
                            <textarea name="note" id="permit-note" class="form-control" readonly></textarea>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Tambah Manual</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @foreach ($unpresences as $unpresence)                        
                    <div class="row g-1">
                        <div class="col-12 border shadow d-flex justify-content-between align-items-center rounded-3 p-2 mb-2">
                            <div class="iamleft d-flex">
                                @php
                                    $unpresenceImg = $unpresence->photo ? (asset('storage') . '/' . $unpresence->photo) : asset('assets/img/avatars').'/'.rand(1,7).'.png';
                                @endphp
                                <a href="{{ $unpresenceImg }}" class="apply-magnific">
                                    <img src="{{ $unpresenceImg }}" alt="" height="50" class="me-3">
                                </a>
                                <div class="d-flex flex-column">
                                    <h5 class="m-0 p-0">{{ $unpresence->name }}</h5>
                                    <small>{{ $unpresence->access->name }}</small>
                                </div>
                            </div>
                            <div class="iamright">
                                <button class="btn btn-icon btn-label-success"><i class="mdi mdi-contacts"></i></button>
                                <button class="btn btn-icon btn-label-info"><i class="ti ti-car"></i></button>
                                <button class="btn btn-icon btn-label-warning"><i class="ti ti-mood-sad"></i></button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@endsection

@push('jsvendor')
<script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/magnific-popup/jquery.magnific-popup.min.js"></script>
@endpush

@push('js')
<script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
<script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>
<script src="{{ asset('libs') }}/wizecode/applier.js"></script>
<script>
    const wize = new Wize();
    const wizeTable = new WizeTable();

    function reloadPresences(presences) {
        $('#presences-data').html('');
        const statusColor = {
            'approved': 'success',
            'pending': 'warning',
            'rejected': 'danger',
        };

        presences.forEach(presence => {
            const img = STORAGE + presence.photo;
            const presenceHTML = `
                <div class="row border rounded-3 p-2 g-0 mb-2 ${presence.status == 'pending' ? 'presence-is-pending' : ''}">
                    <div class="col-4 text-center">
                        <a href="${img}" class="apply-magnific">
                            <img src="${img}" alt="Foto Kehadiran" class="img-fluid" style="max-height: 120px">
                        </a>
                    </div>
                    <div class="col-7 px-2">
                        <b class="d-block text-overflow">${presence.user.name}</b>
                        <small>${presence.user.access.name}</small>
                        <h6 class="mt-2">Masuk: <u>${presence.entry_at}</u></h6>
                        <small class="badge bg-label-${statusColor[presence.status]}">${ucfirst(presence.status)}</small>
                    </div>
                    <div class="col-1">
                        <div class="dropdown">
                            <button
                                class="btn p-0"
                                type="button"
                                id="transactionID"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="ti ti-dots-vertical ti-24px"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                <a class="dropdown-item btn-presence-control" href="javascript:void(0);" data-status="approved" data-id="${presence.id}"><i class="ti ti-thumb-up me-2"></i> Konfirmasi</a>
                                <a class="dropdown-item btn-presence-control" href="javascript:void(0);" data-status="rejected" data-id="${presence.id}"><i class="ti ti-thumb-down me-2"></i> Tolak</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#presences-data').append(presenceHTML);
        });
        wize.maginificatePopup();
    }

    function reloadPermits(permits) {
        $('#permits-data').html('');
        const statusColor = {
            'approved': 'success',
            'pending': 'warning',
            'rejected': 'danger',
        };

        permits.forEach(permit => {
            const img = STORAGE + permit.photo;
            const permitHTML = `
                <div class="row border rounded-3 p-2 g-0 mb-2 ${permit.status == 'pending' ? 'permit-is-pending' : ''}">
                    <div class="col-4 text-center">
                        <a href="${img}" class="apply-magnific">
                            <img src="${img}" alt="Foto Kehadiran" class="img-fluid" style="max-height: 120px">
                        </a>
                    </div>
                    <div class="col-7 px-2">
                        <b class="d-block text-overflow">${permit.user.name}</b>
                        <small>${permit.user.access.name}</small>
                        <h6 class="my-1">Alasan: ${ucfirst(permit.flag)}</h6>
                        <small class="badge bg-label-${statusColor[permit.status]}">${ucfirst(permit.status)}</small>
                    </div>
                    <div class="col-1">
                        <div class="dropdown">
                            <button
                                class="btn p-0"
                                type="button"
                                id="transactionID"
                                data-bs-toggle="dropdown"
                                aria-haspopup="true"
                                aria-expanded="false">
                                <i class="ti ti-dots-vertical ti-24px"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                <a class="dropdown-item btn-permit-detail"
                                    href="javascript:void(0);"
                                    data-status="detail"
                                    data-photo="${img}"
                                    data-reason="${ucfirst(permit.flag)}"
                                    data-note="${permit.note}"
                                    data-employee="${permit.user.name}"
                                    data-position="${permit.user.access.name}"
                                >
                                    <i class="ti ti-list-details me-2"></i> Detail
                                </a>
                                <a class="dropdown-item btn-permit-control" href="javascript:void(0);" data-status="approved" data-id="${permit.id}"><i class="ti ti-check me-2"></i> Setujui</a>
                                <a class="dropdown-item btn-permit-control" href="javascript:void(0);" data-status="rejected" data-id="${permit.id}"><i class="ti ti-ban me-2"></i> Tolak</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#permits-data').append(permitHTML);
        });
        wize.maginificatePopup();
    }


    $(document).ready(function () {
        wize.maginificatePopup();

        $(document).on('click', '.btn-manual-add', function () {
            $('#modal-add').modal('show');
        })
    })

    $(document).on('click', '#confirm-all', function (e) {
        const presencePending = $('.presence-is-pending').length;
        if (!presencePending) {
            Swal.fire({
                text: "Semua data telah dikonfirmasi atau ditolak",
                icon: "warning",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                showClass: {
                    popup: "animate__animated animate__shakeX",
                },
                buttonsStyling: false,
            });
        } else {
            Swal.fire({
                text: "Konfirmasi semua kehadiran yang berstatus 'Pending'?",
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
                        url: '{!! route("presences.confirm_all") !!}',
                        method: 'PUT',
                        data: {},
                        addon_success: (response) => {
                            reloadPresences(response.data.presences);
                        } 
                    });
                }
            });
        }
    });

    $(document).on('click', '#auto-confirm', function (e) {
        e.preventDefault();
        const rule = $(this).data('rule');
        const val = $(this).is(':checked');
        const title = !val ? 'Nonaktifkan Konfirmasi Otomatis?' : 'Aktifkan Konfirmasi Otomatis?';
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
                    url: '{!! route("settings.change") !!}',
                    method: 'PUT',
                    data: {
                        rule: rule,
                        value: val,
                    },
                    addon_success: (data) => {
                        $(this).prop('checked', val);
                        $('.confirm-btns').toggleClass('d-none');
                    } 
                });
            }
        });
    });

    $(document).on('click', '.btn-presence-control', function (e) {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const title = (status == 'approved') ? 'Konfirmasi kehadiran?' : 'Tolak kehadiran?';
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
                    url: '{!! route("presences.change") !!}',
                    method: 'PATCH',
                    data: {
                        id: id,
                        status: status,
                    },
                    addon_success: (response) => {
                        reloadPresences(response.data.presences);
                    } 
                });
            }
        });
    });

    $(document).on('click', '#allow-all', function (e) {
        const permitPending = $('.permit-is-pending').length;
        if (!permitPending) {
            Swal.fire({
                text: "Semua izin telah disetujui atau ditolak",
                icon: "warning",
                customClass: {
                    confirmButton: "btn btn-primary",
                },
                showClass: {
                    popup: "animate__animated animate__shakeX",
                },
                buttonsStyling: false,
            });
        } else {
            Swal.fire({
                text: "Setujui semua permohonan izin yang berstatus 'Pending'?",
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
                        url: '{!! route("presences.permits.allow_all") !!}',
                        method: 'PUT',
                        data: {},
                        addon_success: (response) => {
                            reloadPermits(response.data.permits);
                        } 
                    });
                }
            });
        }
    });

    $(document).on('click', '.btn-permit-control', function (e) {
        const id = $(this).data('id');
        const status = $(this).data('status');
        const title = (status == 'approved') ? 'Setujui Permohonan Izin?' : 'Tolak Permohonan Izin?';
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
                    url: '{!! route("presences.permits.change") !!}',
                    method: 'PATCH',
                    data: {
                        id: id,
                        status: status,
                    },
                    addon_success: (response) => {
                        reloadPermits(response.data.permits);
                    } 
                });
            }
        });
    });

    $(document).on('click', '.btn-permit-detail', function() {
        const photo = $(this).data('photo');
        const reason = $(this).data('reason');
        const employee = $(this).data('employee');
        const position = $(this).data('position');
        const note = $(this).data('note');
        $('img.detail').attr('src', photo);
        $('.detail[name="name"]').val(employee);
        $('.detail[name="position"]').val(position);
        $('.detail[name="reason"]').val(reason);
        $('textarea#permit-note').html(note);
        $('#modal-detail').modal('show');
    });
</script>
@endpush