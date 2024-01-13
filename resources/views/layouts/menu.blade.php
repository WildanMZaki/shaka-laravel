<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('dashboard') }}" class="app-brand-link py-3 px-2">
            <img src="{{ asset('assets/img/menulogo.png') }}" alt="Logo Shaka Pratama" class="img-fluid" style="height: 35px">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="mdi menu-toggle-icon d-xl-block align-middle mdi-20px"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    @php
        $menus = \App\Helpers\MuwizaView::getMenu($auth->access_id);        
    @endphp
    <ul class="menu-inner py-1">
        @foreach ($menus as $menu)
            @if ($menu->type != 'separator')
                @if ($menu->subMenus->count())
                    <li class="menu-item {{ Route::is($menu->route) ? 'active open' : '' }}">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                            <div data-i18n="{{ $menu->name }}">{{ $menu->name }}</div>
                        </a>
                        <ul class="menu-sub">
                            @foreach ($menu->subMenus as $subMenu)    
                                <li class="menu-item {{ Route::is($subMenu->route) ? 'active' : '' }}">
                                    <a href="{!! route($subMenu->route) !!}" class="menu-link" >
                                        <div data-i18n="{{ $subMenu->name }}">{{ $subMenu->name }}</div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @else      
                    <li class="menu-item {{ Route::is($menu->route) ? 'active' : '' }}">
                        <a href="{!! route($menu->route) !!}" class="menu-link">
                            <i class="menu-icon tf-icons {{ $menu->icon }}"></i>
                            <div data-i18n="{{ $menu->name }}">{{ $menu->name }}</div>
                        </a>
                    </li>
                @endif
            @else
                <li class="menu-header fw-medium mt-4">
                    <span class="menu-header-text">{{ $menu->name }}</span>
                </li>
            @endif
        @endforeach

        {{-- <li class="menu-item active">
            <a href="#"
                class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
            <div data-i18n="Dashboard">Dashboard</div>
            </a>
        </li> --}}

        {{-- <li class="menu-header fw-medium mt-4">
            <span class="menu-header-text">Features</span>
        </li>
        
        <li class="menu-item">
            <a
            href="#"
            class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-bottle-soda-classic-outline"></i>
            <div data-i18n="Data Barang">Data Barang</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="#" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-chart-line"></i>
            <div data-i18n="Penjualan">Penjualan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="#" class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-contacts"></i>
            <div data-i18n="Presensi">Presensi</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="#"  class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-account-group-outline"></i>
            <div data-i18n="Karyawan">Karyawan</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="#"  class="menu-link">
            <i class="menu-icon tf-icons mdi mdi-currency-usd"></i>
            <div data-i18n="Penggajian">Penggajian</div>
            </a>
        </li>
        <li class="menu-item">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons mdi mdi-content-paste"></i>
            <div data-i18n="Reports">Laporan</div>
            </a>
            <ul class="menu-sub">
            <li class="menu-item">
                <a href="#" class="menu-link" >
                <div data-i18n="Basic">Kasbon</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link" >
                <div data-i18n="Basic">Penjualan</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="#" class="menu-link" >
                <div data-i18n="Basic">Gaji</div>
                </a>
            </li>
            </ul>
        </li> --}}

        <li class="menu-item logout-btn">
            <a href="#"  class="menu-link">
                <i class="menu-icon tf-icons mdi mdi-logout"></i>
                <div data-i18n="File Manager">Log out</div>
            </a>
        </li>
    
    </ul>
</aside>