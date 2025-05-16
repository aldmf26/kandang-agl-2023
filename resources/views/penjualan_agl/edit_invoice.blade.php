<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-2">

            </div>
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{ route('edit_penjualan_telur') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row">

                <div class="col-lg-2 col-6">
                    <label for="">Tanggal</label>
                    <input type="date" class="form-control tgl_nota" name="tgl" value="{{ $invoice2->tgl }}">
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">No Nota</label>
                    <input type="text" class="form-control nota_bk" name="no_nota" value="{{ $nota }}"
                        readonly>
                    <input type="hidden" class="form-control " name="urutan" value="{{ $invoice2->urutan }}" readonly>
                    <input type="hidden" class="form-control " name="urutan_customer"
                        value="{{ $invoice2->urutan_customer }}" readonly>
                </div>
                <div class="col-lg-2 col-6">
                    <label for="">Customer</label>
                    <select name="customer" id="select2" class="" required>
                        <option value="">Pilih Customer</option>
                        @foreach ($customer as $s)
                            <option value="{{ $s->id_customer }}"
                                {{ $invoice2->id_customer == $s->id_customer ? 'selected' : '' }}>
                                {{ $s->nm_customer }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" name="id_customer" value="{{ $invoice2->id_customer }}">
                </div>



                <div class="col-lg-12">
                    <hr style="border: 1px solid black">
                </div>
                <div class="col-lg-12">
                    <table class="table table-striped ">
                        <thead>
                            <tr>
                                <th class="dhead" width="2%">#</th>
                                <th class="dhead" width="15%">Produk </th>
                                <th class="dhead" width="10%" style="text-align: right">Pcs</th>
                                <th class="dhead" width="10%" style="text-align: right">Kg Kotor</th>
                                <th class="dhead" width="10%" style="text-align: right">Potongan Rak</th>
                                <th class="dhead" width="10%" style="text-align: right">Kg Bersih</th>
                                <th class="dhead" width="10%" style="text-align: right">Rp Satuan</th>
                                <th class="dhead" width="10%">Tipe</th>
                                <th class="dhead" width="10%" style="text-align: right">Total Rp</th>
                                <th class="dhead" width="5%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoice as $i)
                                <tr class="baris{{ $loop->iteration }}">
                                    <td></td>
                                    <td>
                                        <select name="id_produk[]" class="select" required>
                                            <option value="">-Pilih Produk-</option>
                                            @foreach ($produk as $p)
                                                <option value="{{ $p->id_produk_telur }}" @selected($i->id_produk == $p->id_produk_telur)>
                                                    {{ $p->nm_telur }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td align="right">
                                        <input type="text" class="form-control pcs pcs{{ $loop->iteration }}"
                                            count="{{ $loop->iteration }}" style="text-align: right" required
                                            value="{{ $i->pcs }}">
                                        <input type="hidden" class="form-control  pcs_biasa{{ $loop->iteration }}"
                                            name="pcs[]" value="{{ $i->pcs }}">
                                    </td>
                                    <td align="right">
                                        <input type="text" class="form-control kg kg{{ $loop->iteration }}"
                                            count="{{ $loop->iteration }}" style="text-align: right" required
                                            value="{{ $i->kg }}">
                                        <input type="hidden"
                                            class="form-control kgbiasa kgbiasa{{ $loop->iteration }}" name="kg[]"
                                            count="{{ $loop->iteration }}" value="{{ $i->kg }}">
                                    </td>
                                    <td align="right">
                                        <input type="text" class="ikat{{ $loop->iteration }} form-control"
                                            name="ikat[]" value="{{ $i->ikat }}">
                                    </td>
                                    <td align="right">
                                        <input type="text"
                                            class="kg_jual kgminrakbiasa{{ $loop->iteration }} form-control"
                                            name="kg_jual[]" value="{{ $i->kg_jual }}"
                                            count="{{ $loop->iteration }}">
                                    </td>
                                    <td align="right">
                                        <input type="text"
                                            class="form-control rp_satuan rp_satuan{{ $loop->iteration }}"
                                            count="{{ $loop->iteration }}" style="text-align: right" required
                                            value="Rp {{ number_format($i->rp_satuan, 0, ',', '.') }}">

                                        <input type="hidden"
                                            class="form-control rp_satuanbiasa{{ $loop->iteration }}"
                                            name="rp_satuan[]" value="{{ $i->rp_satuan }}">
                                        <input type="hidden"
                                            class="form-control ttl_rpbiasa ttl_rpbiasa{{ $loop->iteration }}"
                                            name="total_rp[]" value="{{ $i->total_rp }}">

                                    </td>
                                    <td>
                                        <select name="tipe[]"
                                            class="form-control changetipe tipe{{ $loop->iteration }}" required
                                            count="{{ $loop->iteration }}">
                                            <option value="">Pilih</option>
                                            <option value="kg" @selected('kg' == $i->tipe)>Kg</option>
                                            <option value="pcs" @selected('pcs' == $i->tipe)>Pcs</option>
                                        </select>
                                    </td>
                                    <td align="right" class="ttl_rp{{ $loop->iteration }}">

                                        Rp {{ number_format($i->total_rp, 2, ',', '.') }}
                                    </td>

                                    <td style="vertical-align: top;">
                                        <button type="button" class="btn rounded-pill remove_baris_kg"
                                            count="{{ $loop->iteration }}"><i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach


                        </tbody>
                        <tbody id="tb_baris_kg">

                        </tbody>
                        <tbody>
                            <tr>
                                <td></td>
                                <td>Rak</td>
                                <td><input type="text" class="form-control  text-end" name="pcs_rak" required
                                        value="{{ $rak->pcs }}">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="10">
                                    <button type="button" class="btn btn-block btn-lg tbh_baris_kg"
                                        style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
                                        <i class="fas fa-plus"></i> Tambah Baris Baru

                                    </button>
                                </th>
                            </tr>
                        </tfoot>


                    </table>
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
                            <h6 class="total float-end">Rp {{ number_format($invoice2->total_rp, 2, ',', '.') }}</h6>
                            <input type="hidden" class="total_semua_biasa" name="total_penjualan"
                                value="{{ $invoice2->total_rp }}">
                        </div>

                    </div>
                    <div id="load_pembayaran"></div>




                </div>
            </section>
    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save ">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('jurnal') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>



    @section('scripts')
        <script>
            $(document).ready(function() {
                $(".select").select2();
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
