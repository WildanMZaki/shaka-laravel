<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{ asset('assets') }}/vendor/libs/jquery/jquery.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/popper/popper.js"></script>
<script src="{{ asset('assets') }}/vendor/js/bootstrap.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/node-waves/node-waves.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ asset('assets') }}/vendor/libs/sweetalert2/sweetalert2.js"></script>
<script src="{{ asset('assets') }}/vendor/js/menu.js"></script>

@if ($errors->any())
    <script>
        Swal.fire({
            text: '{{ $errors->first() }}',
            icon: 'error',
            showConfirmButton: false,
            timer: 1500,
            customClass: {
                confirmButton: 'btn btn-success'
            },
            buttonsStyling: false
        })
    </script>
@endif