<hr class="text-dark">
<div class="row mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="my-0 py-0 fw-bold" id="table-title-{{ $period }}-{{ $access_id }}">{{ $title }}</h6>
        <button class="btn btn-icon btn-label-success add-insentive" data-access_id="{{ $access_id }}" data-position="{{ $position }}" data-period="{{ $period }}" {{ isset($type) ? "data-type=$type" : '' }}>
            <i class="ti ti-plus"></i>
        </button>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Target</th>
                <th>Insentif</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="table-body-{{ $period }}-{{ $access_id }}">
            @php
                $n = 1;
            @endphp
            @foreach ($insentives as $ins)
                <tr>
                    <td>{{ $n++ }}</td>
                    <td>{{ \App\Helpers\Muwiza::ribuan($ins->sales_qty) }}</td>
                    @php
                        $insValue = $ins->type == 'money' ? \App\Helpers\Muwiza::rupiah($ins->insentive) : $ins->insentive;
                        $lcTitle = strtolower($title);
                        $deleteQuestion = "Hapus $lcTitle $position dengan nilai $insValue?";
                    @endphp
                    <td>{{ $insValue }}</td>
                    <td>
                        <button
                            type="button"
                            class="btn btn-icon btn-label-warning edit-insentive"
                            data-id="{{ $ins->id }}"
                            data-sales_qty="{{ $ins->sales_qty }}"
                            data-insentive="{{ str_replace('Rp ', '', $insValue) }}"
                            data-position="{{ $ins->access->name }}"
                            data-access_id="{{ $ins->access_id }}"
                            data-period="{{ $ins->period }}"
                            data-type="{{ $ins->type }}"
                            >
                            <i class="ti ti-edit"></i>
                        </button>
                        <button
                            type="button"
                            class="btn btn-icon btn-label-danger delete-insentive"
                            data-id="{{ $ins->id }}"
                            data-access_id="{{ $ins->access_id }}"
                            data-period="{{ $ins->period }}"
                            data-question="{{ $deleteQuestion }}"
                            >
                            <i class="ti ti-trash"></i>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    {{-- <div class="row g-1">
        <div class="col-lg-5 col-12">
            <label for="#qty-uang-absen-spg-freelancer" class="form-label">Target</label>
            <input type="text" class="form-control" readonly id="qty-uang-absen-spg-freelancer">
        </div>
        <div class="col-lg-5 col-12">
            <label for="#insentive-uang-absen-spg-freelancer" class="form-label">Insentif</label>
            <input type="text" class="form-control" readonly id="insentive-uang-absen-spg-freelancer">
        </div>
        <div class="col-lg-2 col-12 d-flex justify-content-end align-items-end">
            <button type="button" class="save-uang-absen btn btn-primary">Edit</button>
        </div>
    </div> --}}
</div>