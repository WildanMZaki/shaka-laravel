@extends('layouts.app')

@section('title', 'Penjualan')

@push('css')
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
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
                                    <td id="select-all-container"></td>
                                    <th>Tanggal</th>
                                    <th>Nama SPG</th>
                                    <th>Produk</th>
                                    <th>Jumlah</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! 
                                    $table->orderColumns([
                                        'id', 'tanggal', 'spg_name', 'merk', 'qty', 'pendapatan',
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
                    <h5 class="modal-title" id="">Tambah Penjualan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-add" action="{!! route('sales.store') !!}" method="post">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col mb-3">
                                <label for="product_id" class="form-label">Pilih Merk <span class="text-danger">*</span></label>
                                <select class="select-merk store form-select" name="product_id" id="product_id" data-placeholder="Pilih Merk Barang" data-allow-clear="1">
                                    <option value="" selected disabled>Pilih Merk Barang</option>
                                    @foreach ($activeProducts as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">{{ $product->merk }} | {{ $product->stock }} tersedia</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="product_id-invalid-msg"></span>
                                @empty($activeProducts)
                                    <small class="text-danger">*** Ups.. sepertinya tidak ada produk aktif, atau stok kurang</small>
                                @endempty                      
                            </div>
                        </div>
                        <div class="row">
                            <div class="col mb-3">
                                <label for="user_id" class="form-label">Pilih SPG <span class="text-danger">*</span></label>
                                <select class="select-merk store form-select" name="user_id" id="user_id" data-placeholder="Pilih Merk Barang" data-allow-clear="1">
                                    <option value="" selected disabled>Pilih SPG</option>
                                    @foreach ($spgs as $spg)
                                        <option value="{{ $spg->id }}">{{ $spg->name }} | {{ $spg->access->name }}</option>
                                    @endforeach
                                </select>
                                <span class="invalid-feedback" id="user_id-invalid-msg"></span>
                                @empty($spgs)
                                    <small class="text-danger">*** Ups.. sepertinya belum ada spg yang ditambahkan</small>
                                @endempty                      
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label" for="sales_date">Tanggal Penjualan <span class="text-danger">*</span></label>
                                <input type="date" name="sales_date" id="sales_date" class="form-control store">
                                <span class="invalid-feedback" id="sales_date-invalid-msg"></span>
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="qty">Jumlah (Dalam Botol)<span class="text-danger">*</span></label>
                                <input type="number" name="qty" id="qty" class="form-control store" min="1" placeholder="Masukkan Jumlah Barang" min="1">
                                <span class="invalid-feedback" id="qty-invalid-msg"></span>
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
            wizeTable.init({
                title: 'Riwayat Penjualan',
                url_delete: '{!! route("sales.delete") !!}',
                columns: [
                    'tanggal', 'spg_name', 'merk', 'qty', 'pendapatan',
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-plus",
                    color: "btn-primary",
                    text: "Tambah Penjualan",
                    action: () => {
                        $('#modal-add').modal('show');
                    },
                },
                addon_delete: (resp) => {
                    if (resp.hasOwnProperty('data')) {
                        if (resp.data.hasOwnProperty('activeProducts')) {
                            $('#product_id').html('<option value="" selected disabled>Pilih Merk Barang</option>');
                            resp.data.activeProducts.forEach(product => {
                                $('#product_id').html(`
                                    <option value="${product.id}" data-stock="${product.stock}">
                                        ${product.merk} | ${product.stock} tersedia
                                    </option>
                                `);
                            });
                        }
                    }
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

        $('#form-add').on('submit', function(e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const data = {
                product_id: $('.store[name="product_id"]').val(),
                user_id: $('.store[name="user_id"]').val(),
                sales_date: $('.store[name="sales_date"]').val(),
                qty: $('.store[name="qty"]').val(),
            };

            wize.ajax({
                url,
                data,
                method: "POST",
                inputSelector: '.store[name="{key}"]',
                modalSelector: '#modal-add',
                addon_success: (resp) => {
                    wizeTable.reload();
                    $('.store[name="product_id"]').val('');
                    $('.store[name="user_id"]').val('');
                    $('.store[name="qty"]').val('');
                    if (resp.hasOwnProperty('data')) {
                        if (resp.data.hasOwnProperty('activeProducts')) {
                            $('#product_id').html('<option value="" selected disabled>Pilih Merk Barang</option>');
                            resp.data.activeProducts.forEach(product => {
                                $('#product_id').html(`
                                    <option value="${product.id}" data-stock="${product.stock}">
                                        ${product.merk} | ${product.stock} tersedia
                                    </option>
                                `);
                            });
                        }
                    }
                },
            });
        });

    </script>
@endpush