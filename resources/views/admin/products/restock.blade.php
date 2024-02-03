@extends('layouts.app')

@section('title', 'Restock Barang')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.css" />
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-start align-items-center">
                        <a href="{{ $routeBack }}" class="fw-bold me-2">
                            <i class="ti ti-arrow-left fs-3 m-0 p-0"></i>
                        </a>
                        <h3 class="m-0 p-0">Restock Produk</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('products.restock') }}" id="restock-product" method="POST">
                            @csrf
                            @php
                                $isOptionEmpty = count($merkOptions) == 0; 
                            @endphp
                            <div class="row">                                
                                <div class="col-lg-6 col-xl-6">
                                    <label for="select-merk" class="form-label">Pilih Merk <span class="text-danger">*</span></label>
                                    <select class="select-merk apply-select2 storeInput" name="merk_id" id="merk_id" data-placeholder="Pilih Merk Barang" data-allow-clear="1">
                                        @if (!isset($product))
                                            <option value=""></option>
                                        @endif
                                        @foreach ($merkOptions as $merkOption)
                                            <option value="{{ $merkOption->id }}">{{ $merkOption->merk }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="merk_id-invalid-msg"></span>
                                    @if ($isOptionEmpty)
                                        <small class="text-danger">*** Ups.. sepertinya belum ada produk yang pernah ditambahkan</small>
                                    @endif                            
                                </div>

                                <div class="col-lg-3 col-xl-3 mt-3 mt-lg-0 mt-xl-0">
                                    <label class="form-label" for="transaction_date">Tanggal Transaksi <span class="text-danger">*</span></label>
                                    <input type="date" name="transaction_date" id="transaction_date" class="form-control storeInput">
                                    <span class="invalid-feedback" id="transaction_date-invalid-msg"></span>
                                </div>
                                <div class="col-lg-3 col-xl-3 mt-3 mt-lg-0 mt-xl-0">
                                    <label class="form-label" for="expiration_date">Tanggal Kadaluwarsa <span class="text-danger">*</span></label>
                                    <input type="date" name="expiration_date" id="expiration_date" class="form-control storeInput">
                                    <span class="invalid-feedback" id="expiration_date-invalid-msg"></span>
                                </div>
                            </div>

                            <div class="row mt-lg-3">
                                <div class="col-lg-4 col-xl-4 mt-3 mt-lg-0 mt-xl-0">
                                    <label class="form-label" for="qty">Jumlah <span class="text-danger">*</span></label>
                                    <input type="number" name="qty" id="qty" class="form-control storeInput" min="1" placeholder="Masukkan Jumlah Barang" oninput="mustBeAtLeast(this, 1, countTotal)">
                                    <span class="invalid-feedback" id="qty-invalid-msg"></span>
                                </div>
                                <div class="col-lg-4 col-xl-4 mt-3 mt-lg-0 mt-xl-0">
                                    <label for="unit" class="form-label">Satuan <span class="text-danger">*</span></label>
                                    <select class="select-unit apply-select2 storeInput" name="unit" id="unit" data-hide-search="1">
                                        @foreach ($units as $unit)
                                            <option value="{{$unit->qty}}">{{ $unit->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="invalid-feedback" id="unit-invalid-msg"></span>
                                </div>
                                <div class="col-lg-4 col-xl-4 mt-3 mt-lg-0 mt-xl-0">
                                    <label class="form-label" for="total_barang">Total Barang</label>
                                    <input type="text" name="total_barang" id="total_barang" class="form-control disabled" disabled placeholder="0">
                                </div>
                            </div>

                            <div class="row mt-lg-3">
                                <div class="col-lg-3 col-xl-3 mt-3 mt-lg-0 mt-xl-0">
                                    <label class="form-label" for="price_total">Harga Beli <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="rp-addon">Rp.</span>
                                        <input type="text" class="form-control storeInput" id="price_total" name="price_total" placeholder="Masukkan harga beli" aria-label="Harga Beli" aria-describedby="rp-addon" oninput="mustInRupiahCurrency(this, thousandsId, countSatuanPrice)">
                                        <span class="invalid-feedback" id="price_total-invalid-msg"></span>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-xl-3 mt-3 mt-lg-0 mt-xl-0">
                                    <label class="form-label" for="price">Harga Satuan</label>
                                    <input type="text" name="price" id="price" class="form-control disabled" disabled placeholder="Rp 0">
                                </div>       
                                <div class="col-lg-6 col-xl-6 mt-3 mt-lg-0 mt-xl-0">
                                    <label class="form-label" for="description">Keterangan <i>(Opsional)</i></label>
                                    <input type="text" class="form-control storeInput" id="description" name="description" placeholder="Tambahkan keterangan (Opsional)">
                                    <span class="invalid-feedback" id="description-invalid-msg"></span>
                                </div>                         
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>                
                </div>
            </div>
        </div>
    </div>
@endsection

@push('jsvendor')
    <script src="{{ asset('assets') }}/vendor/libs/select2/select2.js"></script>
    <script src="{{ asset('assets') }}/vendor/libs/flatpickr/flatpickr.js"></script>
@endpush

@push('js')
    <script src="{{ asset('libs') }}/wizecode/applier.js"></script>
    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script>
        // CallBack Functions: 
        // 1. Ketika input jumlah barang, maka hitung input total:
        const countTotal = (jumlah) => {
            const jum = Number(jumlah);
            const satuan = Number($('#unit').val());

            $('#total_barang').val(thousandsId(jum * satuan) + ' botol');

            let totalNominal = $('#price_total').val();

            if (totalNominal) {
                countSatuanPrice(totalNominal);
            }
        }
        $(document).ready(() => {
            $('#unit').change(function() {
                const qty = parseInt($('#qty').val());
                const satuan = Number($(this).val());

                if (qty) {
                    $('#total_barang').val(thousandsId(qty * satuan) + ' botol');
                }

                let totalNominal = $('#price_total').val();

                if (totalNominal) {
                    countSatuanPrice(totalNominal);
                }
            });
        });

        // 2. Ketika input harga total, hitungkan harga satuan
        const countSatuanPrice = (nominal) => {
            const validNominal = validInt(nominal);
            const validTotal = validInt($('#total_barang').val());

            if (validTotal) {
                const priceItem = ceilToHundreds(validNominal/validTotal);
                $('#price').val(rupiah(priceItem));
            }
        }

        $('#transaction_date').flatpickr({
            enableTime: false,
            dateFormat: "j M Y",
            defaultDate: new Date(),
        });

        // Initialize expiration_date datepicker
        $('#expiration_date').flatpickr({
            enableTime: false,
            dateFormat: "j M Y",
            defaultDate: new Date().setMonth(new Date().getMonth() + 2),
        });
    </script>
    <script>
        const wize = new Wize();
        const back = '{{ $routeBack }}';

        $(document).on('submit', '#restock-product', function (e) {
            e.preventDefault();
            const url = $(this).attr('action');
            const data = {
                type: $('#type').val(),
                merk_id: $('#merk_id').val(),
                qty: $('#qty').val(),
                unit: $('#unit').val(),
                price_total: validInt($('#price_total').val()),
                price: validInt($('#price').val()),
                transaction_date: $('#transaction_date').val(),
                expiration_date: $('#expiration_date').val(),
                description: $('#description').val(),
            };

            wize.ajax({
                url,
                data,
                method: "POST",
                inputSelector: '.storeInput[name="{key}"]',
                addon_success: () => {
                    setTimeout(() => {
                        window.location.href = back;
                    }, 1500);
                },
            });
            
        })
    </script>
@endpush
