<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">

    <section class="row">
        <form action="{{ route('dashboard_kandang.update_transfer') }}" method="post">
            @csrf
            <div class="col-lg-12">
                <div class="row">
                    <div class="col-lg-5">
                        <table class="table">
                            <tr>
                                <th class="dhead">Tanggal</th>
                                <th class="dhead">No Nota</th>
                                @if ($tgl != date('Y-m-d'))
                                    <th class="dhead">Voucher Edit</th>
                                @endif
                            </tr>
                            <tr>
                                <td>
                                    <input type="date" class="form-control tgl_nota" name="tgl"
                                        value="{{ $tgl }}">
                                </td>
                                <td>
                                    <input type="text" class="form-control nota_bk" name="no_nota"
                                        value="{{ $nota }}" readonly>
                                </td>
                                @if ($tgl != date('Y-m-d'))
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
                                <th width="20%" class="dhead" style="vertical-align: middle" rowspan="2">Produk
                                </th>
                                <th class="dhead text-center" colspan="2">Per Pcs</th>
                                <th class="dhead text-center" colspan="2">Per Ikat</th>
                                <th class="dhead text-center" colspan="3">Rak</th>
                                <th width="5%" class="dhead text-center" rowspan="2">Aksi</th>
                            </tr>
                            <tr>

                                <th class="dhead abu" width="7%" style="text-align: center">Pcs</th>
                                <th class="dhead abu" width="7%" style="text-align: center">Kg</th>
                                {{-- <th class="dhead" width="10%" style="text-align: center;">Rp Pcs</th> --}}

                                <th class="dhead putih" width="7%" style="text-align: center;">Ikat</th>
                                <th class="dhead putih" width="7%" style="text-align: center;">Kg</th>
                                {{-- <th class="dhead" width="10%" style="text-align: center;">Rp Ikat</th> --}}

                                <th class="dhead abuGelap" width="7%" style="text-align: center;">Pcs</th>
                                <th class="dhead abuGelap" width="7%" style="text-align: center;">Kg</th>
                                <th class="dhead abuGelap" width="7%" style="text-align: center;">Rak</th>
                                {{-- <th class="dhead" width="10%" style="text-align: center;">Rp Rak</th> --}}
                                {{-- <th class="dhead" width="5%">Aksi</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $d)
                                <tr class="baris1">
                                    <input type="hidden" name="id_invoice_mtd[]" value="{{ $d->id_invoice_mtd }}">
                                    <td>
                                        <select name="id_produk[]" class="select2_add" required>
                                            <option value="">-Pilih Produk-</option>
                                            @foreach ($produk as $p)
                                                <option {{ $p->id_produk_telur == $d->id_produk ? 'selected' : '' }}
                                                    value="{{ $p->id_produk_telur }}">{{ $p->nm_telur }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td align="right">
                                        <input type="text" class="form-control pcs pcs1" count="1"
                                            style="text-align: right; font-size: 12px;" value="{{ $d->pcs_pcs }}">
                                        <input type="hidden" class="form-control  pcs_biasa1" name="pcs_pcs[]"
                                            value="{{ $d->pcs_pcs }}">
                                    </td>
                                    <td align="right">
                                        <input type="text" class="form-control kg_pcs kg_pcs1"
                                            value="{{ $d->kg_pcs }}" count="1"
                                            style="text-align: right; font-size: 12px;">
                                        <input type="hidden" class="form-control  kg_pcs_biasa1" name="kg_pcs[]"
                                            value="{{ $d->kg_pcs }}">
                                    </td>

                                    <!-- Jual Ikat -->
                                    <td align="right">
                                        <input type="text" class="form-control ikat ikat1" count="1"
                                            style="text-align: right;font-size: 12px;" value="{{ $d->ikat }}"
                                            name="ikat[]">
                                    </td>
                                    <td align="right">
                                        <input type="text" class="form-control kg_ikat kg_ikat1" count="1"
                                            style="text-align: right;font-size: 12px;" value="{{ $d->kg_ikat }}"
                                            name="kg_ikat[]">
                                    </td>
                                    <!-- Jual Kg -->
                                    <td align="right">
                                        <input type="text" class="form-control" name="pcs_kg[]" count="1"
                                            style="text-align: right;font-size: 12px;" value="{{ $d->pcs_kg }}">
                                    </td>
                                    <td align="right">
                                        <input type="text" class="form-control kg_kg kg_kg1" count="1"
                                            style="text-align: right;font-size: 12px;" value="{{ $d->kg_kg }}"
                                            name="kg_kg[]">
                                    </td>
                                    <td align="right">
                                        <input type="text" class="form-control rak_kg rak_kg1" count="1"
                                            style="text-align: right;font-size: 12px;" value="{{ $d->rak_kg }}"
                                            name="rak_kg[]">
                                    </td>
                                    <!-- Jual Kg -->
                                    <td align="center" style="vertical-align: top;">
                                        <button type="button" class="btn rounded-pill remove_baris_tf "
                                            count="1"><i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                        <tbody id="tb_baris_mtd">

                        </tbody>
                        <tfoot>

                            <tr>
                                <th colspan="9"><button type="submit"
                                        class="btn btn-primary float-end">Simpan</button>
                                </th>
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

                    var total = (parseFloat(kg) - parseFloat(rak_kali)) * parseFloat(rp_kg_biasa);
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
                $(document).on("keyup", ".rak_kg", function() {
                    var count = $(this).attr("count");
                    var kg = $('.kg_kg' + count).val();
                    var rak = $('.rak_kg' + count).val();
                    var rp_kg_biasa = $('.rp_kg_biasa' + count).val();

                    var rak_rumus = parseFloat(rak) * 0.12;
                    var rak_kali = Math.round(rak_rumus * 10) / 10;


                    var total = (parseFloat(kg) - parseFloat(rak_kali)) * parseFloat(rp_kg_biasa);
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

                    var total = (parseFloat(kg) - parseFloat(rak_kali)) * parseFloat(rp_kg_biasa);
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
                        url: "/dashboard_kandang/tbh_baris_transfer_mtd?count=" + count,
                        type: "Get",
                        success: function(data) {
                            $("#tb_baris_mtd").append(data);
                            $(".select").select2();
                        },
                    });
                });

                $(document).on("click", ".remove_baris_tf", function() {
                    var delete_row = $(this).attr("count");
                    $(".baris" + delete_row).remove();
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
