<x-theme.app title="{{$title}}" table="Y" sizeCard="11">

    <x-slot name="cardHeader">
        <div class="row justify-content-end">
            <div class="col-lg-12">
                <h6 class="float-start">{{ $title }}</h6>
                <x-theme.button modal="T" href="/dashboard_kandang" icon="fa-home" addClass="float-end" teks="" />
            </div>
            
        </div>

    </x-slot>


    <x-slot name="cardBody">
        <form action="{{route('save_opname_telur_mtd')}}" method="post" class="save_jurnal">
            @csrf
            <section class="row">

                <div class="col-lg-2 col-6">
                    <label for="">Tanggal</label>
                    <input type="date" class="form-control tgl_nota" name="tgl" value="{{date('Y-m-d')}}">
                </div>
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
                            $telur = DB::selectOne("SELECT b.nm_telur, SUM(a.pcs - a.pcs_kredit) as pcs, SUM(a.kg -
                            a.kg_kredit) as kg
                            FROM stok_telur as a
                            left JOIN telur_produk as b on b.id_produk_telur = a.id_telur
                            WHERE a.id_gudang = 1 and a.id_telur = '$p->id_produk_telur' and a.opname = 'T'
                            GROUP by a.id_telur;")
                            @endphp
                            <tr>
                                <td>{{$no+1}}</td>
                                <td>{{$p->nm_telur}}</td>
                                <td align="right">{{empty($telur->pcs) ? '0' : number_format($telur->pcs,0)}}</td>
                                <td align="right">{{empty($telur->pcs) ? '0' :number_format($telur->kg,2)}}</td>
                                <td>
                                    <input style="text-align: right" type="text"
                                        class="form-control pcs_opname pcs_opname{{$p->id_produk_telur}}"
                                        id_produk="{{$p->id_produk_telur}}"
                                        value="{{empty($telur->pcs) ? '0' : number_format($telur->pcs,0,',','.')}}">

                                    <input type="hidden" class="pcs_program{{$p->id_produk_telur}}"
                                        value="{{empty($telur->pcs) ? '0' : $telur->pcs}}">

                                    <input type="hidden" name="pcs[]" class="pcs_opname_biasa{{$p->id_produk_telur}}"
                                        value="{{empty($telur->pcs) ? '0' : $telur->pcs}}">
                                    <input type="hidden" name="id_telur[]" value="{{$p->id_produk_telur}}">
                                </td>
                                <td>
                                    <input style="text-align: right" type="text"
                                        class="form-control kg_opname kg_opname{{$p->id_produk_telur}}"
                                        id_produk="{{$p->id_produk_telur}}"
                                        value="{{ empty($telur->kg) ? '0' : number_format($telur->kg,2,',','.')}}">

                                    <input type="hidden" class="kg_program{{$p->id_produk_telur}}"
                                        value="{{empty($telur->kg) ? '0' : $telur->kg}}">
                                    <input type="hidden" name="kg[]" class="kg_opname_biasa{{$p->id_produk_telur}}"
                                        value="{{empty($telur->kg) ? '0' : $telur->kg}}">
                                </td>
                                <td>
                                    <input style="text-align: right" type="text"
                                        class="form-control pcs_selisih{{$p->id_produk_telur}}" value="0" readonly>

                                    <input type="hidden" name="pcs_selisih[]"
                                        class="pcs_selisih_biasa{{$p->id_produk_telur}}" readonly value="0">
                                </td>
                                <td>
                                    <input style="text-align: right" type="text"
                                        class="form-control kg_selisih{{$p->id_produk_telur}}" value="0" readonly>

                                    <input type="hidden" name="kg_selisih[]"
                                        class="kg_selisih_biasa{{$p->id_produk_telur}}" readonly value="0">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
    </x-slot>
    <x-slot name="cardFooter">
        <button type="submit" class="float-end btn btn-primary button-save ">Simpan</button>
        <button class="float-end btn btn-primary btn_save_loading" type="button" disabled hidden>
            <span class="spinner-border spinner-border-sm " role="status" aria-hidden="true"></span>
            Loading...
        </button>
        <a href="{{route('jurnal')}}" class="float-end btn btn-outline-primary me-2">Batal</a>
        </form>
    </x-slot>



    @section('scripts')
    <script>
        $(document).ready(function () {
            $(document).on("keyup", ".pcs_opname", function () {
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

                var pcs_program = $('.pcs_program'+count).val();

                var selisih = parseFloat(pcs_program) - parseFloat(input2);

                var total_selisih = parseInt(selisih).toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                $('.pcs_selisih' + count).val(total_selisih);
                $('.pcs_selisih_biasa' + count).val(selisih);
                   
            });
            $(document).on("keyup", ".kg_opname", function () {
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

                var kg_program = $('.kg_program'+count).val();

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