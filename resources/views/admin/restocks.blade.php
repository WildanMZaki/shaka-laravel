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
                                    <th>Tipe</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!! 
                                    $table->orderColumns([
                                        'id', 'restock_date', 'merk', 'qty', 'modal', 'expDate', 'type', 'actions'
                                    ])->resultHTML() 
                                !!}
                            </tbody>
                        </table>
                    </div>
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
                    'restock_date', 'merk', 'qty', 'modal', 'expDate', 'type', 'actions'
                ],
                defaultButton: {
                    custom: null,
                    icon: "ti ti-shopping-cart-plus",
                    color: "btn-primary",
                    text: "Restock Barang",
                    action: () => {
                        wize.show_loading();
                        window.location.href = '{{ route("product.restock") }}';
                    },
                }
            })
            wize.activate_tooltips();
        });
    </script>
@endpush
