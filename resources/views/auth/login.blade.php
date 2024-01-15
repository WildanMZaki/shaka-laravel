@extends('layouts.auth')

@section('title', 'Login')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets') }}/vendor/css/pages/page-auth.css" />
@endpush

@section('content')
    <div class="position-relative">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-4">
          <!-- Login -->
          <div class="card p-2">
            <!-- Logo -->
            <div class="app-brand justify-content-center mt-5">
              <a href="index.html" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                  
                </span>
                <span class="app-brand-text demo text-heading fw-semibold">Login</span>
              </a>
            </div>
            <!-- /Logo -->

            <div class="card-body mt-2">

              <form id="formAuthentication" class="mb-3" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-floating form-floating-outline mb-3">
                  <input 
                    type="text"
                    class="form-control @error('phone') is-invalid @enderror" 
                    name="phone" value="{{ old('phone') }}" required autocomplete="phone"
                    autofocus />
                    @error('phone')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                  <label for="phone">Nomor Whatsapp</label>
                </div>
                <div class="mb-3">
                  <div class="form-password-toggle">
                    <div class="input-group input-group-merge">
                      <div class="form-floating form-floating-outline">
                        <input
                          type="password"
                          id="password"
                          class="form-control @error('password') is-invalid @enderror"
                          name="password"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                          aria-describedby="password" />
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <label for="password">Password</label>
                      </div>
                      <span class="input-group-text cursor-pointer"><i class="mdi mdi-eye-off-outline"></i></span>
                    </div>
                  </div>
                </div>
                <div class="mb-3 d-flex justify-content-between">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me" />
                    <label class="form-check-label" for="remember-me"> Remember Me </label>
                  </div>
                </div>
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                </div>
              </form>
              
            </div>
          </div>
          <!-- /Login -->
          
        </div>
      </div>
    </div>
@endsection

@push('js')
    <script>

    </script>
@endpush