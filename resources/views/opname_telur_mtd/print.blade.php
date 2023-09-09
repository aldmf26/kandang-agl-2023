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
    <div class="container">
        <div class="row">
            <div class="col-lg-12 float-start">
                <img src="https://agrilaras.putrirembulan.com/assets/img/logo.png" alt="Logo" width="150px">
                <table class="float-end mt-3">
                    <tr>
                        <td style="padding: 5px">Tanggal</td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ Tanggal($detail->tgl) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">No. Nota </td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $nota }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">Admin</td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $detail->admin }}</td>
                    </tr>
    
    
                </table>
            </div>
            {{-- <div class="col-lg-6 float-end">
            </div> --}}
        </div>
        <section class="row">
            <div class="col-lg-12">
                <h6 class="text-center">Opname Telur Martadah</h6>
                <hr style="border: 1px solid black">
            </div>
            <div class="col-lg-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead">#</th>
                            <th class="dhead">Nama Produk</th>
                            <th style="text-align: right" class="dhead">Pcs Program</th>
                            <th style="text-align: right" class="dhead">Kg Program</th>
                            <th style="text-align: right" class="dhead" width="15%">Pcs Aktual</th>
                            <th style="text-align: right" class="dhead" width="15%">Kg Aktual</th>
                            <th style="text-align: right" class="dhead" width="15%">Pcs Selisih</th>
                            <th style="text-align: right" class="dhead" width="15%">Kg Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produk as $no => $p)
                            @php
                                $telur = DB::selectOne("SELECT a.pcs_selisih,a.kg_selisih, b.id_produk_telur as
                        id_telur,b.nm_telur, SUM(a.pcs - a.pcs_kredit) as pcs, SUM(a.kg -
                        a.kg_kredit) as kg
                        FROM stok_telur as a
                        left JOIN telur_produk as b on b.id_produk_telur = a.id_telur
                        WHERE a.id_gudang = 1 and a.id_telur = '$p->id_produk_telur' AND a.nota_transfer = '$nota'
                        GROUP by a.id_telur");
                            @endphp
                            @if (!empty($telur->pcs))
                                <tr>
                                    <td>{{ $no + 1 }}</td>
                                    <td>{{ $p->nm_telur }}</td>
                                    <td align="right">{{ number_format($telur->pcs + $telur->pcs_selisih, 0) }}
                                    </td>
                                    <td align="right">{{ number_format($telur->kg + $telur->kg_selisih, 2) }}
                                    </td>
                                    <td align="right">
                                        {{ $telur->pcs }}
                                    </td>
                                    <td align="right">
                                        {{ number_format($telur->kg, 2, ',', '.') }}
                                    </td>
                                    <td align="right">
                                        {{ $telur->pcs_selisih }}
                                    </td>
                                    <td align="right">
                                        {{ number_format($telur->kg_selisih, 2) }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
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
