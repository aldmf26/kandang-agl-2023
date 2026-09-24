<header class="mb-5">
    @include('components.theme.header2')
    <nav class="main-navbar ">
        <div class="container font-bold">
            <ul>
                <li class="menu-item">
                    <a href="{{ route('dashboard') }}"
                        class='menu-link {{ request()->route()->getName() == 'dashboard' ? 'active_navbar_new' : '' }}'>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('penjualan.index') }}"
                        class='menu-link {{ request()->route()->getName() == 'penjualan.index' ? 'active_navbar_new' : '' }}'>
                        <span>Penjualan</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('dashboard_kandang.harian') }}"
                        class='menu-link {{ in_array(request()->route()->getName(), ['dashboard_kandang.harian', 'dashboard_kandang.perencanaan', 'dashboard_kandang.populasi', 'dashboard_kandang.input_harian', 'dashboard_kandang.telur']) ? 'active_navbar_new' : '' }}'>
                        <span>Harian</span>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{ route('dashboard_kandang.laporan') }}"
                        class='menu-link {{ in_array(request()->route()->getName(), ['dashboard_kandang.laporan', 'dashboard_kandang.laporan_export', 'dashboard_kandang.laporan_kandang_harian']) ? 'active_navbar_new' : '' }}'>
                        <span>Laporan</span>
                    </a>
                </li>
                {{-- @php
                    $navbar = DB::table('navbar_kandang')->orderBy('urutan', 'ASC')->get();

                @endphp
                @foreach ($navbar as $d)
                    @php
                        $string = $d->isi;
                        $string = str_replace(['[', ']', "'"], '', $string);
                        $array = explode(', ', $string);
                    @endphp
                    <li class="menu-item">
                        <a href="{{ route($d->route) }}"
                            class='menu-link 
                    {{ in_array(request()->route()->getName(), $array) ? 'active_navbar_new' : '' }}'>
                            <span>{{ ucwords($d->nama) }}</span>
                        </a>
                    </li>
                @endforeach --}}
            </ul>
        </div>
    </nav>

</header>
