<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-12">
                <h6 class="float-start">{{ $title }}</h6>
                <x-theme.button modal="T" href="/dashboard_kandang" icon="fa-home" addClass="float-end"
                    teks="" />
                <x-theme.button modal="T" href="/rak/print_opname/{{ $no_nota }}/print"
                    icon="fa-print" addClass="float-end print" teks="Print" />

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
                        <td style="padding: 5px">{{ Tanggal($history->tgl) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">No. Nota </td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $no_nota }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">Admin</td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $history->admin }}</td>
                    </tr>


                </table>
            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
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
                             $hargaSatuan = $getBiaya->ttl_rp / $getBiaya->ttl_debit;
                             $rupiah = $hargaSatuan * $history->selisih;
                         @endphp
                        <tr>
                            <td>1</td>
                            <td>Rak Telur</td>
                            <td class="text-end">{{ number_format($history->debit + $history->selisih, 0) }}</td>
                            <td class="text-end">{{ number_format($history->debit, 0) }}</td>
                            <td class="text-end">{{ number_format($history->selisih, 0) }}</td>
                            <td class="text-end">{{ number_format($hargaSatuan,1) }}</td>
                            <td class="text-end">{{ number_format($rupiah,1) }}</td>
                        </tr>
                    </tbody>
                    
                </table>
            </div>
        </section>
    </x-slot>
</x-theme.app>
