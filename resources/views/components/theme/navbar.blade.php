<style>
    .layout-horizontal .main-navbar ul .menu-link {
        padding: .5rem .9rem;
        border: 1px solid transparent;
        border-radius: 9px;
        text-decoration: none;
        transition: background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .layout-horizontal .main-navbar ul .menu-link:hover {
        border-color: rgba(255, 255, 255, .12);
        background: rgba(255, 255, 255, .08);
        color: #fff;
    }

    .layout-horizontal .main-navbar ul .menu-link.active_navbar_new {
        border-color: rgba(255, 255, 255, .3);
        background: rgba(255, 255, 255, .17);
        box-shadow: 0 4px 12px rgba(25, 44, 96, .16), inset 0 0 0 1px rgba(255, 255, 255, .06);
        color: #fff !important;
        text-decoration: none;
    }

    @media (max-width: 1199.98px) {
        .layout-horizontal .main-navbar ul .menu-link.active_navbar_new {
            border-color: #435ebe;
            background: #435ebe;
            box-shadow: none;
            color: #fff !important;
        }
    }
</style>

<header class="mb-5">
    @include('components.theme.header2')
    <nav class="main-navbar" aria-label="Navigasi utama">
        <div class="container font-bold">
            <ul>
                @php
                    $routeName = request()->route()?->getName();
                    $ruteHarian = ['dashboard_kandang.harian', 'dashboard_kandang.perencanaan', 'dashboard_kandang.populasi', 'dashboard_kandang.input_harian', 'dashboard_kandang.telur'];
                    $ruteLaporan = ['dashboard_kandang.laporan', 'dashboard_kandang.laporan_export', 'dashboard_kandang.laporan_kandang_harian'];
                @endphp
                <li class="menu-item">
                    <a href="{{ route('dashboard') }}"
                        class="menu-link {{ $routeName == 'dashboard' ? 'active_navbar_new' : '' }}"
                        @if ($routeName == 'dashboard') aria-current="page" @endif>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('penjualan.index') }}"
                        class="menu-link {{ $routeName == 'penjualan.index' ? 'active_navbar_new' : '' }}"
                        @if ($routeName == 'penjualan.index') aria-current="page" @endif>
                        <span>Penjualan</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('dashboard_kandang.harian') }}"
                        class="menu-link {{ in_array($routeName, $ruteHarian) ? 'active_navbar_new' : '' }}"
                        @if (in_array($routeName, $ruteHarian)) aria-current="page" @endif>
                        <span>Harian</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('dashboard_kandang.laporan') }}"
                        class="menu-link {{ in_array($routeName, $ruteLaporan) ? 'active_navbar_new' : '' }}"
                        @if (in_array($routeName, $ruteLaporan)) aria-current="page" @endif>
                        <span>Laporan</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('gudang.index') }}"
                        class="menu-link {{ $routeName === 'gudang.index' || str_starts_with((string) $routeName, 'transaksi.penerimaan.') || $routeName === 'transaksi.faktur-pembelian.detail' ? 'active_navbar_new' : '' }}"
                        @if ($routeName === 'gudang.index' || str_starts_with((string) $routeName, 'transaksi.penerimaan.') || $routeName === 'transaksi.faktur-pembelian.detail') aria-current="page" @endif>
                        <span>Gudang</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

</header>
