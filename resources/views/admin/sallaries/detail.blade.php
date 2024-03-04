@extends('layouts.app')

@section('title', 'Detail Penggajian')

@push('css')
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('jsvendor')
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.js"></script>
@endpush

@push('js')
    <script src="{{ asset('libs') }}/wizecode/applier.js"></script>
    <script src="{{ asset('libs') }}/wizecode/Wize.js"></script>
    <script src="{{ asset('libs') }}/wizecode/WizeTable.js"></script>
@endpush
