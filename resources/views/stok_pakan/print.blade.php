    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title }}</title>

        <link rel="stylesheet" href="{{ asset('theme') }}/assets/css/main/app.css">
        <link rel="stylesheet" href="{{ asset('theme') }}/assets/css/pages/fontawesome.css">
        <style>
            table {
                font-size: 11px;
            }

            .dhead {
                background-color: #435EBE !important;
                color: white;
            }

            .dborder {
                border-color: #435EBE
            }
        </style>
    </head>

    <body>
        
        <div class="py-5 px-5 container">
            <div class="row tbl1">
                <div class="col-lg-12">
                    <img src="https://agrilaras.putrirembulan.com/assets/img/logo.png" alt="Logo" width="150px">
                    <table class="float-end mt-3">
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
        
            <section class="row tbl2">
                <div class="col-lg-12">
                    <h6 class="text-center">Opname Pakan & Vitamin</h6>
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
                                    <td align="right">{{ number_format($stokProgram, 0) }}</td>
                                    <td align="right">{{ number_format($d->stok, 0) }}</td>
                                    <td align="right">{{ number_format($d->stok - $stokProgram, 0) }}</td>
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

        </div>
    <script>
        window.print()
    </script>
    </body>

    </html>
