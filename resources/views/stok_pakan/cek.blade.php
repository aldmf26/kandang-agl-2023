<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">

    <x-slot name="cardHeader">
       
        <div class="row justify-content-end">
            <div class="col-lg-12">
                <h6 class="float-start">{{ $title }}</h6>
                <x-theme.button modal="T" href="/dashboard_kandang" icon="fa-home" addClass="float-end"
                    teks="" />
                <x-theme.button modal="T" href="/dashboard_kandang/print_opname/{{$no_nota}}/print" icon="fa-print" addClass="float-end print"
                    teks="Print" />

            </div>

        </div>
        <div class="row tbl1">
            <div class="col-lg-3">
                <img src="https://agrilaras.putrirembulan.com/assets/img/logo.png" alt="Logo" width="150px">
            </div>
            <div class="col-lg-6">
                <table>
                    <tr>
                        <td style="padding: 5px">Tanggal</td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ Tanggal($history[0]->tgl) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">No. Nota </td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $no_nota }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">Admin</td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $history[0]->admin }}</td>
                    </tr>


                </table>
            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('save_opname_telur_mtd') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row tbl2">


                <div class="col-lg-12">
                    <hr style="border: 1px solid black">
                </div>
                <div class="col-lg-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">#</th>
                                <th class="dhead">Nama Produk</th>
                                <th style="text-align: right" class="dhead">Stok Program</th>
                                <th style="text-align: right" class="dhead" width="15%">Stok Aktual</th>
                                <th style="text-align: right" class="dhead" width="15%">Stok Selisih</th>
                                <th style="text-align: right" class="dhead" width="15%">Harga Satuan</th>
                                <th style="text-align: right" class="dhead" width="15%">Rupiah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttlRp = 0;
                            @endphp
                            @foreach ($history as $no => $d)
                                @php
                                    $stokProgram = $d->stok - $d->pcs + $d->pcs_kredit;
                                    $selisih = $d->stok - $stokProgram;
                                    $hargaSatuan = $d->sum_ttl_rp == 0 ? 0 : $d->sum_ttl_rp / $d->pcs_sum_ttl_rp;
                                    
                                    $selisihRupiah = $hargaSatuan * $selisih;
                                    $ttlRp += $selisih < 0 ? $selisihRupiah * -1 : $selisihRupiah;
                                @endphp
                                <tr>
                                    <td>{{ $no + 1 }}</td>
                                    <td>{{ $d->nm_produk }}</td>
                                    <td align="right">{{ number_format($stokProgram, 1) }}</td>
                                    <td align="right">{{ number_format($d->stok, 1) }}</td>
                                    <td align="right">{{ number_format($d->stok - $stokProgram, 1) }}</td>
                                    <td align="right">{{ number_format($hargaSatuan, 1) }}</td>
                                    <td align="right">
                                        {{ number_format($selisih < 0 ? $selisihRupiah * -1 : $selisihRupiah, 0) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center" colspan="6">Total </th>
                                <th class="text-end">{{ number_format($ttlRp, 0) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </section>
    </x-slot>
</x-theme.app>
