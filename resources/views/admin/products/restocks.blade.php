@extends('layouts.app')

@section('title', 'Riwayat Restock')

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
                                    <th>Tanggal</th>
                                    <th>Merk</th>
                                    <th>Kuantitas</th>
                                    <th>Modal</th>
                                    <th>Tanggal Expired</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! 
                                    $table->orderColumns([
                                        'id', 'restock_date', 'merk', 'qty', 'modal', 'expDate', 'actions'
                                    ])->resultHTML() 
                                !!}
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
                    <h5 class="modal-title" id="">Detail Transaksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-1">
                        <div class="col-lg-4 col-12 mb-3">
                            <label class="form-label">Merk</label>
                            <input type="text" name="merk" class="form-control detail" readonly />
                        </div>
                        <div class="col-lg-4 col-12 mb-3">
                            <label class="form-label">Kuantitas</label>
                            <input type="text" name="qty" class="form-control detail" readonly />
                        </div>
                        <div class="col-lg-4 col-12 mb-3">
                            <label class="form-label">Modal</label>
                            <input type="text" name="modal" class="form-control detail" readonly />
                        </div>
                    </div>
                    <div class="row g-1">
                        <div class="col-lg-6 col-12 mb-3">
                            <label class="form-label">Tanggal Transaksi</label>
                            <input type="text" name="restock_date" class="form-control detail" readonly />
                        </div>
                        <div class="col-lg-6 col-12 mb-3">
                            <label class="form-label">Tanggal Kadaluwarsa</label>
                            <input type="text" name="expiration_date" class="form-control detail" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label for="description_detail" class="form-label">Keterangan</label>
                            <textarea class="form-control" name="description" id="description_detail" readonly></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
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
    <script>
        const wize = new Wize();
        const wizeTable = new WizeTable();
        let table;
        $(document).ready(() => {
            wizeTable.init({
                title: 'Riwayat Belanja',
                url_delete: '{!! route("products.restocks.delete") !!}',
                columns: [
                    'restock_date', 'merk', 'qty', 'modal', 'expDate', 'actions'
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-shopping-cart-plus",
                    color: "btn-primary",
                    text: "Restock Barang",
                    action: () => {
                        wize.show_loading();
                        window.location.href = '{{ route("products.restocks.form") }}';
                    },
                }
            })
            wize.activate_tooltips();
        });

        $(document).on('click', '.btn-detail', function () {
            const id = $(this).data('id');
            wize.ajax({
                url: '{{ route("products.restocks.detail", "")}}'+`/${id}`,
                method: 'GET',
                successDefault: false,
                addon_success: (data) => {
                    Swal.close();
                    for (const key in data) {
                        if (Object.hasOwnProperty.call(data, key)) {
                            const element = data[key];
                            $(`.detail[name="${key}"]`).val(element);
                        }
                    }
                    $('#description_detail').html(data.description);
                    $('#modal-detail').modal('show');
                }
            })
        })
    </script>
@endpush
