@extends('layouts.app')

@section('title', 'Kasbon')

@push('css')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-1.13.8/b-2.4.2/b-html5-2.4.2/r-2.5.0/datatables.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            
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
</script>
@endpush