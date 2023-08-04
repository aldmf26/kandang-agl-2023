<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">

    <section class="row">
        <form action="{{ route('dashboard_kandang.save_penjualan_telur') }}" method="post">
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-6">
                        <table class="table">
                            <tr>
                                <th class="dhead">Tanggal</th>
                                <th class="dhead">No Nota</th>
                                <th class="dhead">Customer</th>
                                <th class="dhead">HP</th>
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" class="form-control tgl_nota" name="tgl"
                                        value="{{ date('Y-m-d') }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control nota_bk" name="no_nota" value="TM{{ $nota }}"
                                        readonly>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="customer" required>
                                </td>
                                <td>
                                    <input type="hidden" name="tipe" value="kg">
                                    <input type="text" class="form-control no_hp" name="no_hp" required>
                                </td>
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
                                <th class="dhead abuGelap" width="10%" style="text-align: center;">Rp Kg</th>


                            </tr>
                        </thead>
                        <tbody>
                            <tr class="baris1">

                                <td>
                                    <select name="id_produk[]" class="select2_add" required>
                                        <option value="">-Pilih Produk-</option>
                                        @foreach ($produk as $p)
                                        <option value="{{ $p->id_produk_telur }}">{{ $p->nm_telur }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control pcs pcs1" count="1"
                                        style="text-align: right; font-size: 12px;">
                                    <input type="hidden" class="form-control  pcs_biasa1" name="pcs_pcs[]" value="0">
                                </td>
                                <td align="right">

                                    <input type="text" class="form-control  kg_pcs_biasa1" name="kg_pcs[]"
                                        style="text-align: right; font-size: 12px;" value="0">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control rp_pcs pcs1" count="1"
                                        style="text-align: right;font-size: 12px;">
                                    <input type="hidden" class="form-control rp_pcs_biasa rp_pcs_biasa1" name="rp_pcs[]"
                                        value="0">
                                    <input type="hidden" class="ttl_rp_pcs1" value="0">
                                </td>
                                <!-- Jual Ikat -->
                                <td align="right">
                                    <input type="text" class="form-control ikat ikat1" count="1"
                                        style="text-align: right;font-size: 12px;" value="0" name="ikat[]">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control kg_ikat kg_ikat1" count="1"
                                        style="text-align: right;font-size: 12px;" value="0" name="kg_ikat[]">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control rp_ikat rp_ikat1 " count="1"
                                        style="text-align: right;font-size: 12px;">
                                    <input type="hidden" class="form-control  rp_ikat_biasa1" name="rp_ikat[]"
                                        value="0">
                                    <input type="hidden" class="ttl_rp_ikat1" value="0">
                                </td>
                                <!-- Jual Ikat -->
                                <!-- Jual Kg -->
                                <td align="right">
                                    <input type="text" class="form-control" name="pcs_kg[]" count="1"
                                        style="text-align: right;font-size: 12px;" value="0">
                                </td>
                                <td align="right">
                                    <input type="text" class="form-control kg_kg kg_kg1" count="1"
                                        style="text-align: right;font-size: 12px;" value="0" name="kg_kg[]">
                                </td>
                                {{-- <td align="right">
                                    <input type="text" class="form-control rak_kg rak_kg1" count="1"
                                        style="text-align: right;font-size: 12px;" value="0" name="rak_kg[]">
                                </td> --}}
                                <td align="right">
                                    <input type="text" class="form-control rp_kg rp_kg1" count="1"
                                        style="text-align: right;font-size: 12px;">
                                    <input type="hidden" class="form-control  rp_kg_biasa rp_kg_biasa1" name="rp_kg[]"
                                        value="0">
                                    <input type="hidden" class="ttl_rp_kg1" value="0">
                                </td>
                                <!-- Jual Kg -->
                                <td align="right" class="ttl_rp1"></td>
                                <td style="vertical-align: top;">
                                    <button type="button" class="btn rounded-pill remove_baris_kg" count="1"><i
                                            class="fas fa-trash text-danger"></i>
                                    </button>
                                </td>
                            </tr>


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

                $(document).on("click", ".remove_baris_mtd", function() {
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