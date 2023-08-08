<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">

    <section class="row">
        <form action="{{route('dashboard_kandang.save_edit_telur')}}" method="post">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-7">
                        <table class="table">
                            <tr>
                                <th class="dhead">Tanggal</th>
                                <th class="dhead">No Nota</th>
                                <th class="dhead">Customer</th>
                                <th class="dhead">HP</th>
                                @if ($invoice2->tgl != date('Y-m-d'))
                                <th class="dhead">Voucher Edit</th>
                                @endif
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" class="form-control tgl_nota" name="tgl"
                                        value="{{$invoice2->tgl}}">
                                </td>
                                <td>
                                    <input type="text" class="form-control nota_bk" name="no_nota"
                                        value="{{ $invoice2->no_nota }}" readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="customer" required
                                        value="{{$invoice2->customer}}">
                                </td>
                                <td>
                                    <input type="hidden" name="tipe" value="kg">
                                    <input type="text" class="form-control no_hp" name="no_hp" required
                                        value="{{$invoice2->no_hp}}">
                                </td>
                                @if ($invoice2->tgl != date('Y-m-d'))
                                <td>
                                    <input type="text" class="form-control" name="voucher">
                                </td>
                                @endif
                            </tr>
                        </table>
                    </div>
                </div>

            </div>

            <div class="col-lg-12">
                <hr style="border: 1px solid black">
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">

                    @csrf
                    <table class="table table-striped table-bordered" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="dhead" width="10%" rowspan="2">Produk </th>
                                <th style="text-align: center" class="dhead abu" colspan="3">Penjualan per pcs</th>
                                <th style="text-align: center" class="dhead putih" colspan="3">Penjualan per ikat</th>
                                <th style="text-align: center" class="dhead abuGelap" colspan="3">Penjualan per rak</th>
                                <th rowspan="2" class="dhead" width="10%"
                                    style="text-align: center; white-space: nowrap;">Total
                                    Rp
                                </th>
                                <th rowspan="2" class="dhead" width="5%">Aksi</th>
                            </tr>
                            <tr>
                                <th class="dhead abu" width="7%" style="text-align: center">Pcs</th>
                                <th class="dhead abu" width="7%" style="text-align: center">Kg</th>
                                <th class="dhead abu" width="10%" style="text-align: center;">Rp Pcs</th>

                                <th class="dhead putih" width="7%" style="text-align: center;">Ikat</th>
                                <th class="dhead putih" width="7%" style="text-align: center;">Kg</th>
                                <th class="dhead putih" width="10%" style="text-align: center;">Rp Ikat</th>

                                <th class="dhead abuGelap" width="7%" style="text-align: center;">Pcs</th>
                                <th class="dhead abuGelap" width="7%" style="text-align: center;">Kg Bersih <br> potong
                                    rak
                                </th>
                                {{-- <th class="dhead abuGelap" width="7%" style="text-align: center;">Rak</th> --}}
                                <th class="dhead abuGelap" width="10%" style="text-align: center;">Rp Rak</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice as $no => $i)
                            <tr class="baris{{$no+1}}">
                                @php
                                $rp_pcs = $i->pcs_pcs * $i->rp_pcs;
                                $rp_ikat = ($i->kg_ikat - $i->ikat) * $i->rp_ikat;
                                $rak_kali = round($i->rak_kg * 0.12,1);
                                $rp_kg = $i->kg_kg * $i->rp_kg;
                                $total_rp = $rp_pcs + $rp_ikat + $rp_kg;

                                @endphp
                                <td>
                                    <select name="id_produk[]" class="select2_add" required>
                                        <option value="">-Pilih Produk-</option>
                                        @foreach ($produk as $p)
                                        <option value="{{$p->id_produk_telur}}" {{$i->id_produk == $p->id_produk_telur ?
                                            'SELECTED' : ''}}>{{$p->nm_telur}}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control pcs pcs{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right; font-size: 12px;" value="{{$i->pcs_pcs}}">
                                    <input type="hidden" class="form-control  pcs_biasa{{$no+1}}" name="pcs_pcs[]"
                                        value="{{$i->pcs_pcs}}">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control kg_pcs kg_pcs{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right; font-size: 12px;" value="{{$i->kg_pcs}}">
                                    <input type="hidden" class="form-control  kg_pcs_biasa{{$no+1}}" name="kg_pcs[]"
                                        value="{{$i->kg_pcs}}">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control rp_pcs pcs{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;" value="{{$i->rp_pcs}}">
                                    <input type="hidden" class="form-control rp_pcs_biasa rp_pcs_biasa{{$no+1}}"
                                        name="rp_pcs[]" value="{{$i->rp_pcs}}">
                                    <input type="hidden" class="ttl_rp_pcs{{$no+1}}"
                                        value="{{$i->pcs_pcs * $i->rp_pcs}}">
                                </td>
                                <!-- Jual Ikat -->
                                <td align="right">
                                    <input type="text" class="form-control ikat ikat{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;" value="{{$i->ikat}}" name="ikat[]">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control kg_ikat kg_ikat{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;" value="{{$i->kg_ikat}}"
                                        name="kg_ikat[]">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control rp_ikat rp_ikat{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;" value="{{$i->rp_ikat}}">
                                    <input type="hidden" class="form-control  rp_ikat_biasa{{$no+1}}" name="rp_ikat[]"
                                        value="{{$i->rp_ikat}}">
                                    <input type="hidden" class="ttl_rp_ikat{{$no+1}}" value="{{$rp_ikat}}">
                                </td>
                                <!-- Jual Ikat -->
                                <!-- Jual Kg -->
                                <td align="right">
                                    <input type="text" class="form-control" name="pcs_kg[]" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;" value="{{$i->pcs_kg}}">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control kg_kg kg_kg{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;" value="{{$i->kg_kg}}" name="kg_kg[]">
                                </td>
                                {{-- <td align="right">
                                    <input type="text" class="form-control rak_kg rak_kg1" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;" value="0" name="rak_kg[]">
                                </td> --}}
                                <td align="right">
                                    <input type="text" class="form-control rp_kg rp_kg{{$no+1}}" count="{{$no+1}}"
                                        style="text-align: right;font-size: 12px;"
                                        value="{{number_format($i->rp_kg,0,',','.')}}">
                                    <input type="hidden" class="form-control  rp_kg_biasa rp_kg_biasa{{$no+1}}"
                                        name="rp_kg[]" value="{{$i->rp_kg}}">
                                    <input type="hidden" class="ttl_rp_kg{{$no+1}}" value="{{$rp_kg}}">
                                </td>
                                <!-- Jual Kg -->

                                <td align="right" class="ttl_rp{{$no+1}}">{{number_format($total_rp)}}</td>
                                <td style="vertical-align: top;">
                                    <button type="button" class="btn rounded-pill remove_baris_kg" count="{{$no+1}}"><i
                                            class="fas fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tbody id="tb_baris_mtd">

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="12">
                                    <button type="button" class="btn btn-block btn-lg tbh_baris_mtd"
                                        style="background-color: #435EBE; color: white; font-size: 14px; padding: 13px;">
                                        <i class="fas fa-plus"></i> Tambah Baris Baru

                                    </button>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="11"></th>
                                <th><button type="submit" class="btn btn-primary ">Simpan</button></th>
                            </tr>
                        </tfoot>


                    </table>

                </div>
                <!-- <div id="loadpcs"></div> -->
            </div>
        </form>
    </section>
    @section('scripts')
    <script>
        $(document).ready(function() {
            $(document).on("keyup", ".pcs", function() {
                var count = $(this).attr("count");
                
                var input = $(this).val();
                input = input.replace(/[^\d\,]/g, "");
                input = input.replace(".", ",");
                input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                if (input === "") {
                    $(this).val("");
                    $('.pcs_biasa' + count).val(0)
                } else {
                    $(this).val(input);
                    input = input.replaceAll(".", "");
                    input2 = input.replace(",", ".");
                    $('.pcs_biasa' + count).val(input2)
                }
                var pcs = $('.pcs_biasa' + count).val()
                var rp_satuan = $('.rp_pcs_biasa' + count).val();
                total = parseFloat(rp_satuan) * parseFloat(pcs);
                $('.ttl_rp_pcs' + count).val(total);

                var total_pcs = $('.ttl_rp_pcs' + count).val();
                var total_ikat = $('.ttl_rp_ikat' + count).val();
                var total_kg = $('.ttl_rp_kg' + count).val();

                var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                var total_rupiah = total_all.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $('.ttl_rp' + count).text(total_rupiah);
            });

            $(document).on("keyup", ".rp_pcs", function() {
                var count = $(this).attr("count");
                
                var input = $(this).val();
                input = input.replace(/[^\d\,]/g, "");
                input = input.replace(".", ",");
                input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                if (input === "") {
                    $(this).val("");
                    $('.rp_pcs_biasa' + count).val(0)
                } else {
                    $(this).val(input);
                    input = input.replaceAll(".", "");
                    input2 = input.replace(",", ".");
                    $('.rp_pcs_biasa' + count).val(input2)
                }
                var pcs = $('.pcs_biasa' + count).val()
                var rp_satuan = $('.rp_pcs_biasa' + count).val();

                total = parseFloat(rp_satuan) * parseFloat(pcs);
                $('.ttl_rp_pcs' + count).val(total);

                var total_pcs = $('.ttl_rp_pcs' + count).val();
                var total_ikat = $('.ttl_rp_ikat' + count).val();
                var total_kg = $('.ttl_rp_kg' + count).val();

                var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                var total_rupiah = total_all.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $('.ttl_rp' + count).text(total_rupiah);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $(document).on("keyup", ".ikat", function() {
                var count = $(this).attr("count");
                var ikat = $('.ikat' + count).val();
                var kg = $('.kg_ikat' + count).val();
                var rp_ikat = $('.rp_ikat_biasa' + count).val();


                var total = (parseFloat(kg) - parseFloat(ikat)) * parseFloat(rp_ikat);
                $('.ttl_rp_ikat' + count).val(total);

                var total_pcs = $('.ttl_rp_pcs' + count).val();
                var total_ikat = $('.ttl_rp_ikat' + count).val();
                var total_kg = $('.ttl_rp_kg' + count).val();

                var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                var total_rupiah = total_all.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $('.ttl_rp' + count).text(total_rupiah);
            });
            $(document).on("keyup", ".kg_ikat", function() {
                var count = $(this).attr("count");
                var ikat = $('.ikat' + count).val();
                var kg = $('.kg_ikat' + count).val();
                var rp_ikat = $('.rp_ikat_biasa' + count).val();

                var total = (parseFloat(kg) - parseFloat(ikat)) * parseFloat(rp_ikat);
                $('.ttl_rp_ikat' + count).val(total);

                var total_pcs = $('.ttl_rp_pcs' + count).val();
                var total_ikat = $('.ttl_rp_ikat' + count).val();
                var total_kg = $('.ttl_rp_kg' + count).val();

                var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                var total_rupiah = total_all.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $('.ttl_rp' + count).text(total_rupiah);
            });
            $(document).on("keyup", ".rp_ikat", function() {
                var count = $(this).attr("count");
                var input = $(this).val();
                input = input.replace(/[^\d\,]/g, "");
                input = input.replace(".", ",");
                input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                if (input === "") {
                    $(this).val("");
                    $('.rp_ikat_biasa' + count).val(0);
                } else {
                    $(this).val(input);
                    input = input.replaceAll(".", "");
                    input2 = input.replace(",", ".");
                    $('.rp_ikat_biasa' + count).val(input2);
                }

                var ikat = $('.ikat' + count).val();
                var kg = $('.kg_ikat' + count).val();
                var rp_ikat = $('.rp_ikat_biasa' + count).val();




                var total = (parseFloat(kg) - parseFloat(ikat)) * parseFloat(rp_ikat);
                $('.ttl_rp_ikat' + count).val(total);
                var total_pcs = $('.ttl_rp_pcs' + count).val();
                var total_ikat = $('.ttl_rp_ikat' + count).val();
                var total_kg = $('.ttl_rp_kg' + count).val();

                var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                var total_rupiah = total_all.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $('.ttl_rp' + count).text(total_rupiah);
            });
        });
    </script>
    <script>
        $(document).ready(function() {
                $(document).on("keyup", ".kg_kg", function() {
                    var count = $(this).attr("count");
                    var kg = $('.kg_kg' + count).val();
                    var rak = $('.rak_kg' + count).val();
                    var rp_kg_biasa = $('.rp_kg_biasa' + count).val();
                    var rak_rumus = parseFloat(rak) * 0.12;
                    var rak_kali = Math.round(rak_rumus * 10) / 10;

                    var total = parseFloat(kg) * parseFloat(rp_kg_biasa);
                    $('.ttl_rp_kg' + count).val(total);

                    var total_pcs = $('.ttl_rp_pcs' + count).val();
                    var total_ikat = $('.ttl_rp_ikat' + count).val();
                    var total_kg = $('.ttl_rp_kg' + count).val();

                    var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                    var total_rupiah = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $('.ttl_rp' + count).text(total_rupiah);
                });
                // $(document).on("keyup", ".rak_kg", function() {
                //     var count = $(this).attr("count");
                //     var kg = $('.kg_kg' + count).val();
                //     var rak = $('.rak_kg' + count).val();
                //     var rp_kg_biasa = $('.rp_kg_biasa' + count).val();

                //     var rak_rumus = parseFloat(rak) * 0.12;
                //     var rak_kali = Math.round(rak_rumus * 10) / 10;


                //     var total = (parseFloat(kg) - parseFloat(rak_kali)) * parseFloat(rp_kg_biasa);
                //     $('.ttl_rp_kg' + count).val(total);


                //     var total_pcs = $('.ttl_rp_pcs' + count).val();
                //     var total_ikat = $('.ttl_rp_ikat' + count).val();
                //     var total_kg = $('.ttl_rp_kg' + count).val();

                //     var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                //     var total_rupiah = total_all.toLocaleString("id-ID", {
                //         style: "currency",
                //         currency: "IDR",
                //     });
                //     $('.ttl_rp' + count).text(total_rupiah);
                // });
                $(document).on("keyup", ".rp_kg", function() {
                    var count = $(this).attr("count");
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.rp_kg_biasa' + count).val(0);
                    } else {
                        $(this).val(input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.rp_kg_biasa' + count).val(input2);
                    }

                    var kg = $('.kg_kg' + count).val();
                    var rak = $('.rak_kg' + count).val();
                    var rp_kg_biasa = $('.rp_kg_biasa' + count).val();
                    var rak_rumus = parseFloat(rak) * 0.12;
                    var rak_kali = Math.round(rak_rumus * 10) / 10;

                    var total = parseFloat(kg) * parseFloat(rp_kg_biasa);
                    $('.ttl_rp_kg' + count).val(total);


                    var total_pcs = $('.ttl_rp_pcs' + count).val();
                    var total_ikat = $('.ttl_rp_ikat' + count).val();
                    var total_kg = $('.ttl_rp_kg' + count).val();

                    var total_all = parseFloat(total_pcs) + parseFloat(total_ikat) + parseFloat(total_kg);

                    var total_rupiah = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $('.ttl_rp' + count).text(total_rupiah);
                });

            });
    </script>
    <script>
        $(document).ready(function() {
            var count = 2;
            $(document).on("click", ".tbh_baris_mtd", function() {
                count = count + 1;
                $.ajax({
                    url: "/dashboard_kandang/tambah_baris_jual_mtd?count=" + count,
                    type: "Get",
                    success: function(data) {
                        $("#tb_baris_mtd").append(data);
                        $(".select").select2();
                    },
                });
            });

            $(document).on("click", ".remove_baris_kg", function() {
                var delete_row = $(this).attr("count");
                $(".baris" + delete_row).remove();
                $('.ttl_rpbiasa' + count).val(total);
                var total_all = 0;
                $(".ttl_rpbiasa").each(function() {
                    total_all += parseFloat($(this).val());
                });
                var totalRupiahall = total_all.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                var total_kredit = 0;
                $(".kredit_biasa").each(function() {
                    total_kredit += parseFloat($(this).val());
                });
                var total_all_kredit = total_all + total_kredit;


                var totalkreditall = total_all_kredit.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $(".total").text(totalRupiahall)
                $(".total_kredit").text(totalkreditall)

                // selisih
                var total_debit = 0;
                $(".debit_biasa").each(function() {
                    total_debit += parseFloat($(this).val());
                });
                var totaldebitall = total_debit.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $(".total_debit").text(totaldebitall);

                var selisih = Math.round(total_all + total_kredit) - total_debit;
                var selisih_total = selisih.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                if (Math.round(total_kredit + total_all) === total_debit) {
                    $(".cselisih").css("color", "green");
                    $(".button-save").removeAttr("hidden");
                } else {
                    $(".cselisih").css("color", "red");
                    $(".button-save").attr("hidden", true);
                }
                $(".selisih").text(selisih_total);

            });

            aksiBtn("form");
            $("form").on("keypress", function(e) {
                if (e.which === 13) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
    @endsection
</x-theme.app>