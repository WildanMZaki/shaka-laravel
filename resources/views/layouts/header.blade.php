<nav
    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
    id="layout-navbar">
    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
            <i class="mdi mdi-menu mdi-24px"></i>
        </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">

        <ul class="navbar-nav flex-row align-items-center ms-auto">
        <!-- User -->
            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                <a
                    class="nav-link dropdown-toggle hide-arrow p-0"
                    href="javascript:void(0);"
                    data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                        <img src="{{ $auth->photoPath() }}" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end mt-3 py-2">
                <li>
                    <a class="dropdown-item pb-2 mb-1 change-my-profile" href="#">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 me-2 pe-1">
                                <div class="avatar avatar-online">
                                    <img src="{{ $auth->photoPath() }}" alt class="w-px-40 h-auto rounded-circle" />
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 name-of-user">{{ $auth->name }}</h6>
                                <small class="text-muted">{{ $auth->access->name }}</small>
                            </div>
                        </div>
                    </a>
                </li>
                <li>
                    <div class="dropdown-divider my-1"></div>
                </li>
                <li class="logout-btn">
                    <a class="dropdown-item" href="javascript:void(0);">
                        <i class="mdi mdi-logout me-1 mdi-20px"></i>
                        <span class="align-middle">Log out</span>
                    </a>
                </li>
                </ul>
            </li>
        <!--/ User -->
        </ul>
    </div>
</nav>

<div class="modal fade" id="modal-edit-my-profile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFullTitle">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="update-profile" action="{{ route('settings.change_profile') }}" method="post" style="display: contents" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" class="form-control myprofile" name="name" required autocomplete="off">
                        <span class="invalid-feedback" id="name-invalid-msg"></span>
                    </div>
                    <div class="mb-3">
                        <label for="phone">Nomor Telepon</label>
                        <input type="text" class="form-control myprofile" name="phone" required autocomplete="off" oninput="mustDigit(this)">
                        <span class="invalid-feedback" id="phone-invalid-msg"></span>
                    </div>
                    <div class="mb-3">
                        <label for="name">Password</label>
                        <input type="text" class="form-control myprofile" name="password" autocomplete="off">
                        <span class="invalid-feedback" id="password-invalid-msg"></span>
                        <small>Abaikan jika tidak ingin mengganti password</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
