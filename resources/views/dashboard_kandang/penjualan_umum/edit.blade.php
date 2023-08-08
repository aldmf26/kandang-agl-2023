<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">

    <x-slot name="cardHeader">
        
        <h6 class="float-start mt-1">{{ $title }}</h6>
        <x-theme.btn_dashboard route="dashboard_kandang.index" />

    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('dashboard_kandang.update_penjualan') }}" method="post" class="save_jurnal">
            @csrf
            <section class="row">
                <div class="col-lg-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="4%" class="dhead">Tanggal</th>
                                <th width="9%" class="dhead">No Nota</th>
                                <th width="10%" class="dhead">Pelanggan</th>
                                @if ($getPenjualan->tgl != date('Y-m-d'))
                                    <th class="dhead" width="10%">Voucher Edit</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input value="{{ $getPenjualan->tgl }}" type="date" value="{{ date('Y-m-d') }}"
                                        class="form-control" name="tgl">
                                </td>
                                <td>
                                    <input readonly value="PAGL-{{ $getPenjualan->urutan }}" type="text" required
                                        class="form-control">
                                    <input value="{{ $getPenjualan->urutan }}" type="hidden" required
                                        class="form-control" name="no_nota">
                                </td>
                            
                                <td>
                                    <input type="text" name="id_customer" class="form-control" value="{{ $getPenjualan->id_customer }}">
                                </td>
                                @if ($getPenjualan->tgl != date('Y-m-d'))
                                <td>
                                    <input type="text" name="voucher" class="form-control">
                                </td>
                                @endif
                                
                               
                            </tr>
                        </tbody>


                    </table>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width="20%" class="dhead">Produk</th>
                                <th width="5%" class="dhead">Qty</th>
                                <th width="10%" class="dhead text-end">Harga Satuan</th>
                                <th width="10%" class="dhead text-end">Total Rp</th>
                                <th width="5%" class="text-center dhead">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getProduk as $no => $p)
                                <tr>
                                    <td>
                                        <select name="id_produk[]" required class="form-control select2 produk-change"
                                            id="">
                                            <option value="">- Pilih Produk -</option>
                                            @foreach ($produk as $d)
                                                <option {{ $d->id_produk == $p->id_produk ? 'selected' : '' }}
                                                    value="{{ $d->id_produk }}">{{ $d->nm_produk }}
                                                    ({{ strtoupper($d->satuan->nm_satuan) }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input count="{{$no+1}}" name="qty[]" value="{{ $p->qty }}" type="text"
                                            class="form-control qty qty{{$no+1}}">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control dikanan setor-nohide text-end"
                                            value="Rp. {{ number_format($p->rp_satuan, 2) }}" count="{{$no+1}}">
                                        <input type="hidden" class="form-control dikanan setor-hide setor-hide{{$no+1}}"
                                            value="{{ $p->rp_satuan }}" name="rp_satuan[]">
                                    </td>
                                    <td>
                                        <input readonly type="text"
                                            class="form-control dikanan ttlrp-nohide{{$no+1}} text-end"
                                            value="Rp. {{ number_format($p->total_rp, 2) }}" count="{{$no+1}}">
                                        <input type="hidden" class="form-control dikanan ttlrp-hide ttlrp-hide{{$no+1}}"
                                            value="{{ $p->total_rp }}" name="total_rp[]">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tbody id="tbh_baris">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="9">
                                    <button type="button" class="btn btn-block btn-lg tbh_baris"
                                        style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
                                        <i class="fas fa-plus"></i> Tambah Produk Baru
                                    </button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                    

                </div>
            </section>
    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{ route('penjualan2.index') }}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>



    @section('scripts')
        <script>
            $('.select2').select2()
            var count = 3;
            plusRow(count, 'tbh_baris', 'tbh_add')
            plusRow2(count, 'tbh_pembayaran', 'tbh_pembayaran')

            $(document).on("keyup", ".setor-nohide", function() {
                var count = $(this).attr("count");
                var input = $(this).val();
                var qty = $('.qty' + count).val()

                input = input.replace(/[^\d\,]/g, "");
                input = input.replace(".", ",");
                input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                if (input === "") {
                    $(this).val("");
                    $('.setor-hide' + count).val(0)
                } else {
                    $(this).val("Rp " + input);
                    input = input.replaceAll(".", "");
                    input2 = input.replace(",", ".");
                    var ttl_rp = parseFloat(input) * parseFloat(qty)
                    $(".ttlrp-nohide" + count).val(ttl_rp.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    }));
                    $(".ttlrp-hide" + count).val(ttl_rp);
                    $('.setor-hide' + count).val(input2)
                }


                var total_rpAtas = 0;
                $(".ttlrp-hide").each(function() {
                    total_rpAtas += parseFloat($(this).val());
                });

                var totalRupiah = total_rpAtas.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $(".total_kredit").text(totalRupiah);

                var total_debit = 0;
                $(".debit").each(function() {
                    total_debit += parseFloat($(this).val());
                });
                var total_kredit = 0;
                $(".kredit").each(function() {
                    total_kredit += parseFloat($(this).val());
                });

                var selisih = total_rpAtas - total_debit - total_kredit;
                var selisih_total = selisih.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                });
                $(".total").text(selisih_total);

                if (selisih === 0) {
                    $(".cselisih").css("color", "green");
                    $(".button-save").removeAttr("hidden");
                } else {
                    $(".cselisih").css("color", "red");
                    $(".button-save").attr("hidden", true);
                }
                $(".selisih").text(selisih_total);
            });

            function pbyr(classNohide, classHide, classHideLawan) {
                $(document).on('keyup', '.' + classNohide, function() {
                    var count = $(this).attr('count')
                    var input = $(this).val()
                    var total_debit = 0;
                    var total_pbyrDebit = 0;
                    var total_pbyrKredit = 0;

                    $(".ttlrp-hide").each(function() {
                        total_debit += parseFloat($(this).val());
                    });


                    input = input.replace(/[^\d\,]/g, "");
                    input = input.replace(".", ",");
                    input = input.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");

                    if (input === "") {
                        $(this).val("");
                    } else {
                        $(this).val("Rp " + input);
                        input = input.replaceAll(".", "");
                        input2 = input.replace(",", ".");
                        var totalRupiah = input2.toLocaleString("id-ID", {
                            style: "currency",
                            currency: "IDR",
                        });
                        $('.total').text($(this).val());

                        $("." + classHide + count).val(input);
                    }
                    $(".debit").each(function() {
                        total_pbyrDebit += parseFloat($(this).val());
                    });
                    $(".kredit").each(function() {
                        total_pbyrKredit += parseFloat($(this).val());
                    });
                    var selisih = total_debit - (total_pbyrDebit - total_pbyrKredit);

                    var selisih_total = selisih.toLocaleString("id-ID", {
                        style: "currency",
                        currency: "IDR",
                    });
                    $('.total').text(selisih_total);

                    if (selisih === 0) {
                        $(".cselisih").css("color", "green");
                        $(".button-save").removeAttr("hidden");
                    } else {
                        $(".cselisih").css("color", "red");
                        $(".button-save").attr("hidden", true);
                    }
                    $(".selisih").text(selisih_total);
                })
            }

            pbyr('pembayaranDebit-nohide', 'pembayaranDebit-hide', 'pembayaranKredit-hide')
            pbyr('pembayaranKredit-nohide', 'pembayaranKredit-hide', 'pembayaranDebit-hide')
            // convertRpKoma('setor-nohide', 'setor-hide', '', 'total_kredit')
        </script>
    @endsection
</x-theme.app>
