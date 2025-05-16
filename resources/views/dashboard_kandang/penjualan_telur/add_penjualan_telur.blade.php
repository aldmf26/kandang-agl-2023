<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-6">
                <x-theme.button modal="T" href="/produk_telur" icon="fa-home" addClass="float-end" teks="" />
            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('save_penjualan_telur') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row">

                <div class="col-lg-2 col-6">
                    <label for="">Tanggal</label>
                    <input type="date" class="form-control tgl_nota" name="tgl" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">No Nota</label>
                    <input type="text" class="form-control nota_bk" name="no_nota" value="TM{{ $nota }}"
                        readonly>
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">Customer</label>
                    <select name="customer" id="select2" class="" required>
                        <option value="">Pilih Customer</option>
                        @foreach ($customer as $s)
                            <option value="{{ $s->id_customer }}">{{ $s->nm_customer }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-12">
                    <hr style="border: 1px solid black">
                </div>
                <div class="col-lg-12">
                    <div id="loadkg"></div>

                </div>
                <div class="col-lg-5">

                </div>
                <div class="col-lg-7">

                    <hr style="border: 1px solid blue">


                    <div class="row">
                        <div class="col-lg-6">
                            <h6>Total</h6>
                        </div>
                        <div class="col-lg-6">
                            <h6 class="total float-end">Rp 0 </h6>
                            <input type="hidden" class="total_semua_biasa text-end" name="total_penjualan">
                        </div>

                    </div>


                </div>
            </section>
    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save ">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('penjualan_agrilaras') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>



    @section('scripts')
        <script>
            $(document).ready(function() {

                function loadkg() {
                    $.ajax({
                        type: "get",
                        url: "/loadkginvoice",
                        success: function(data) {
                            $("#loadkg").html(data);
                            $(".select").select2();
                        }
                    });
                }


                loadkg();


                $(document).on("keyup", ".pcs", function() {
                    var count = $(this).attr("count");
                    var input = $(this).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    var tipe = $('.tipe' + count).val();

                    if (input === "") {
                        $(this).val("");
                        $('.pcs_biasa' + count).val(0)
                    } else {
                        $(this).val(input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.pcs_biasa' + count).val(input2)
                    }
                    var kg = $('.kgbiasa' + count).val();
                    var ikat = parseFloat(input2) / 180;
                    var kg_jual = parseFloat(kg) - parseFloat(ikat.toFixed(1));
                    var pcs_biasa = $('.pcs_biasa' + count).val();

                    $('.ikat' + count).val(ikat.toFixed(1));
                    $('.kgminrak' + count).val(kg_jual.toFixed(1));
                    $('.kgminrakbiasa' + count).val(kg_jual.toFixed(1));

                    if (tipe != '') {
                        if (tipe == 'kg') {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(kg_jual);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        } else {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(pcs_biasa);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        }
                    }





                    var total_pcs = 0;
                    $(".pcs").each(function() {
                        total_pcs += parseFloat($(this).val());
                    });




                    var total_all = 0;
                    $(".ttl_rpbiasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });

                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });

                    $(".total").text(totalRupiahall);


                });
                $(document).on("keyup", ".kg", function() {
                    var count = $(this).attr("count");
                    var input = $(this).val();
                    var tipe = $('.tipe' + count).val();
                    // input = input.replace(/[^\d\,]/g, "");
                    // input = input.replace(".", ",");
                    // input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.kgbiasa' + count).val(0)
                    } else {
                        $(this).val(input);
                        // input = input.replaceAll(".", "");
                        // input2 = input.replace(",", ".");
                        $('.kgbiasa' + count).val(input)
                    }
                    var pcs = $('.pcs_biasa' + count).val();
                    var ikat = parseFloat(pcs) / 180;
                    var kg_jual = parseFloat(input) - parseFloat(ikat.toFixed(1));
                    var pcs_biasa = $('.pcs_biasa' + count).val();
                    $('.kgminrak' + count).text(kg_jual.toFixed(1));
                    $('.kgminrakbiasa' + count).val(kg_jual.toFixed(1));


                    if (tipe != '') {
                        if (tipe == 'kg') {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(kg_jual);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        } else {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(pcs_biasa);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        }
                    }



                    var total_all = 0;
                    $(".ttl_rpbiasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });
                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });



                    $(".total").text(totalRupiahall)




                });

                $(document).on("keyup", ".rp_satuan", function() {
                    var count = $(this).attr("count");
                    var input = $(this).val();
                    var tipe = $('.tipe' + count).val();
                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                        $('.rp_satuanbiasa' + count).val(0)
                    } else {
                        $(this).val("Rp " + input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        $('.rp_satuanbiasa' + count).val(input2)
                    }
                    var kg_jual = $('.kgminrakbiasa' + count).val();
                    var pcs_biasa = $('.pcs_biasa' + count).val();
                    if (tipe != '') {
                        if (tipe == 'kg') {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(kg_jual);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        } else {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(pcs_biasa);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        }
                    }



                    var total_all = 0;
                    $(".ttl_rpbiasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });
                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });


                    $(".total").text(totalRupiahall)

                });

                var count = 2;
                $(document).on("click", ".tbh_baris_kg", function() {
                    count = count + 1;
                    $.ajax({
                        url: "/tambah_baris_kg?count=" + count,
                        type: "Get",
                        success: function(data) {
                            $("#tb_baris_kg").append(data);
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


                });
                $(document).on('change', ".changetipe", function() {
                    var count = $(this).attr("count");
                    var tipe = $('.tipe' + count).val();

                    var kg_jual = $('.kgminrakbiasa' + count).val();
                    var pcs_biasa = $('.pcs_biasa' + count).val();

                    if (tipe == 'kg') {
                        var rp_satuan = $('.rp_satuanbiasa' + count).val();
                        total = parseFloat(rp_satuan) * parseFloat(kg_jual);
                        var totalRupiah = total.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                        });
                        $('.ttl_rp' + count).text(totalRupiah);
                        $('.ttl_rpbiasa' + count).val(total);
                    } else {
                        var rp_satuan = $('.rp_satuanbiasa' + count).val();
                        total = parseFloat(rp_satuan) * parseFloat(pcs_biasa);
                        var totalRupiah = total.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                        });
                        $('.ttl_rp' + count).text(totalRupiah);
                        $('.ttl_rpbiasa' + count).val(total);
                    };

                    var total_all = 0;
                    $(".ttl_rpbiasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });
                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $(".total").text(totalRupiahall)


                });
                $(document).on('keyup', ".kg_jual", function() {
                    var count = $(this).attr("count");

                    var kg_jual = $('.kgminrakbiasa' + count).val();
                    var pcs_biasa = $('.pcs_biasa' + count).val();
                    var tipe = $('.tipe' + count).val();
                    if (tipe != '') {
                        if (tipe == 'kg') {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(kg_jual);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        } else {
                            var rp_satuan = $('.rp_satuanbiasa' + count).val();
                            total = parseFloat(rp_satuan) * parseFloat(pcs_biasa);
                            var totalRupiah = total.toLocaleString("id-ID", {
                                style: "currency",
                                currency: "IDR",
                            });
                            $('.ttl_rp' + count).text(totalRupiah);
                            $('.ttl_rpbiasa' + count).val(total);
                        }
                    }
                    var total_all = 0;
                    $(".ttl_rpbiasa").each(function() {
                        total_all += parseFloat($(this).val());
                    });
                    var totalRupiahall = total_all.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $(".total").text(totalRupiahall)


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
