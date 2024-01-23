<!DOCTYPE html>

<html
  lang="id"
  class="light-style layout-wide customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('assets') }}"
  data-template="vertical-menu-template-free">
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base_url" content="{{ asset('') }}">
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>
        {{ config('app.company', 'Shaka Pratama') }}
         - 
        {{ \Str::ucfirst(\Str::lower($__env->yieldContent('title'))) }}
    </title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets') }}/img/favicon/favicon.png" />

    @includeIf('layouts.commons.styles')

    <!-- Page CSS -->
    <!-- Page -->
    @stack('css')
    <!-- Helpers -->
    <script src="{{ asset('assets') }}/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets') }}/js/config.js"></script>
  </head>

  <body>
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        @includeIf('layouts.menu')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->

          @includeIf('layouts.header')

          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->
                @yield('content')
            <!-- / Content -->

            <!-- Footer -->
            <footer class="content-footer footer bg-footer-theme">
              <div class="container-xxl">
                <div
                  class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
                  <div class="text-body mb-2 mb-md-0">
                    ©
                    <script>
                      document.write(new Date().getFullYear());
                    </script>
                  </div>
                </div>
              </div>
            </footer>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>        

    @includeIf('layouts.commons.scripts')

    <!-- Vendors JS -->
    @stack('jsvendor')

    <!-- Main JS -->
    <script src="{{ asset('assets') }}/js/main.js"></script>

    <!-- Page JS -->
    <script>
      const STORAGE = '{{ asset("storage") }}' + '/';
      $('.logout-btn').click(() => {
        Swal.fire({
            text: 'Anda yakin ingin log out?',
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes",
            customClass: {
              confirmButton: "btn btn-primary",
              cancelButton: "btn btn-outline-danger ms-1",
            },
            buttonsStyling: false,
          }).then(function (result) {
            if (result.value) {
              window.location.href = '{{ route("logout") }}';
            }
          });
      });
    </script>
    @stack('js')
  </body>
</html>
