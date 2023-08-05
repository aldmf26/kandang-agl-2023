<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-12">
                <h6 class="float-start">{{ $title }}</h6>
                <x-theme.button modal="T" href="/dashboard_kandang" icon="fa-home" addClass="float-end" teks="" />
            </div>

        </div>
        <div class="row">
            <div class="col-lg-3">
                <img src="https://agrilaras.putrirembulan.com/assets/img/logo.png" alt="Logo" width="150px">
            </div>
            <div class="col-lg-6">
                <table>
                    <tr>
                        <td style="padding: 5px">Tanggal</td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ Tanggal($data->tgl) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">No. Nota </td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $nota }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px">Admin</td>
                        <td style="padding: 5px">:</td>
                        <td style="padding: 5px">{{ $data->admin }}</td>
                    </tr>


                </table>
            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('save_opname_telur_mtd') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row">


                <div class="col-lg-12">
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
                                    {{number_format($telur->kg, 2, ',', '.')}}
                                </td>
                                <td align="right">
                                    {{ $telur->pcs_selisih }}
                                </td>
                                <td align="right">
                                    {{ number_format($telur->kg_selisih,2) }}
                                </td>
                            </tr>
                            @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
    </x-slot>




    @section('scripts')
    <script>
        $(document).ready(function() {
                $(document).on("keyup", ".pcs_opname", function() {
                    var count = $(this).attr("id_produk");
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.pcs_opname_biasa' + count).val(0)
                    } else {
                        $(this).val(input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.pcs_opname_biasa' + count).val(input2)
                    }

                    var pcs_program = $('.pcs_program' + count).val();

                    var selisih = parseFloat(pcs_program) - parseFloat(input2);

                    var total_selisih = parseInt(selisih).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                    $('.pcs_selisih' + count).val(total_selisih);
                    $('.pcs_selisih_biasa' + count).val(selisih);

                });
                $(document).on("keyup", ".kg_opname", function() {
                    var count = $(this).attr("id_produk");
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.kg_opname_biasa' + count).val(0)
                    } else {
                        $(this).val(input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.kg_opname_biasa' + count).val(input2)
                    }

                    var kg_program = $('.kg_program' + count).val();

                    var selisih = parseFloat(kg_program) - parseFloat(input2);

                    var total_selisih = selisih.toFixed(2).replace(".", ",");
                    total_selisih = total_selisih.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    $('.kg_selisih' + count).val(total_selisih);
                    $('.kg_selisih_biasa' + count).val(selisih);

                });
                aksiBtn("form");
            });
    </script>
    @endsection
</x-theme.app>