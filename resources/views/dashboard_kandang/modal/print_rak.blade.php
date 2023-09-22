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
    
        <section class="row tbl2">
            <div class="col-lg-12">
                <h6 class="text-center">Opname Rak Telur</h6>
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

    </div>
<script>
    window.print()
</script>
</body>

</html>
