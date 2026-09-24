<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <style>
            .abu {
                background-color: #ffffff !important;
                color: rgb(37, 37, 37);
            }

            .putih {
                background-color: #ffffff !important;
                color: rgb(37, 37, 37);
            }

            .abuGelap {
                background-color: #433b3b !important;
                color: rgb(37, 37, 37);
            }

            .merah {
                background-color: #ffffff !important;
                color: red;
                font-weight: bold;
            }
        </style>
        <h5 class="float-start mt-1">{{ $title }} ~ {{ tanggal(date('Y-m-d')) }}</h5>

        <div class="row justify-content-end">

            <div class="col-lg-12">
                <a href="{{ route('data_kandang.index') }}" class="btn btn-sm btn-primary float-end">Perencanaan Chick In
                    - Out</a>
                <a href="{{ route('data_chickin.index') }}" class="me-2 btn btn-sm btn-primary float-end">Laporan Chick
                    In - Out</a>
                {{-- <a href="https://ternak.ptagafood.com/produk_telur" class="me-2 btn btn-sm btn-primary float-end">Link
                    Dashboard</a> --}}

            </div>
        </div>

    </x-slot>
    <x-slot name="cardBody">
        @include('dashboard_kandang.tabel.stokTelur')
        <section class="row">
            @if (session()->has('error'))
                <div class="col-lg-12">
                    <x-theme.alert pesan="kontak dr anto kalo ada yg merah" />
                </div>
            @endif

            @include('dashboard_kandang.tabel.penjualanUmum')
            @include('dashboard_kandang.tabel.inputKandangHarian')
            @include('dashboard_kandang.tabel.stok_ayam')
            @include('dashboard_kandang.tabel.pakan')


            @include('dashboard_kandang.modal.tambah_pakan')
            @include('dashboard_kandang.modal.tambah_obat_pakan')
            @include('dashboard_kandang.modal.tambah_obat_air')
            @include('dashboard_kandang.modal.tambah_obat_ayam')
            @include('dashboard_kandang.modal.transfer_ayam')
            @include('dashboard_kandang.modal.penjualan_ayam')
            @include('dashboard_kandang.modal.history_pakvit')
            @include('dashboard_kandang.modal.edit_kandang')
            @include('dashboard_kandang.modal.opname_rak')
        </section>
    </x-slot>
    @include('dashboard_kandang.tabel.inputKandangHarian_js')
</x-theme.app>
