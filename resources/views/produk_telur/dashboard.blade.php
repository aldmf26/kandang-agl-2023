<x-theme.app cont="container-fluid" title="{{ $title }}" cont="container-fluid" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row ">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} </h6>
            </div>
            <div class="col-lg-6">
                <a href="#" data-bs-toggle="modal" data-bs-target="#view" class="btn btn-sm btn-primary float-end"><i
                        class="fas fa-calendar-week"></i></a>
            </div>
        </div>
    </x-slot>
    <style>
        .dhead {
            background-color: #435EBE !important;
            color: white;
            vertical-align: middle;
        }
    </style>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-6">
                <h6>Stok masuk martadah {{ tanggal($tanggal) }}</h6>
                <table class="table table-bordered">
                    <thead style="font-size: 10px; border-top-left-radius: 50px">
                        <tr>
                            <th class="dhead" rowspan="2" style="text-align: center">Kandang</th>
                            @foreach ($produk as $p)
                                <th colspan="2" style="text-align: center" class="dhead">{{ $p->nm_telur }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($produk as $p)
                                <th style="text-align: center" class="dhead">Pcs</th>
                                <th style="text-align: center" class="dhead">Kg</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody style="border-color: #435EBE; font-size: 10px">

                        @foreach ($kandang as $k)
                            <tr>
                                <td>{{ $k->nm_kandang }}</td>
                                @foreach ($produk as $p)
                                    @php
                                        $stok = DB::selectOne("SELECT a.pcs , a.kg
                            FROM stok_telur as a
                            where a.tgl = '$tanggal' and a.id_telur = '$p->id_produk_telur' and a.id_gudang = '1'
                            and
                            a.id_kandang = '$k->id_kandang'
                            ");
                                    @endphp
                                    <td>{{ empty($stok->pcs) ? '0' : number_format($stok->pcs, 0) }}</td>
                                    <td>{{ empty($stok->kg) ? '0' : number_format($stok->kg, 2) }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="border-color: #435EBE; font-size: 10px">
                        <tr>
                            <th>Total</th>
                            @foreach ($produk as $p)
                                @php
                                    $total_mtd = DB::selectOne("SELECT sum(a.pcs) as pcs , sum(a.kg) as kg
                            FROM stok_telur as a
                            where a.tgl = '$tanggal' and a.id_telur = '$p->id_produk_telur' and a.id_gudang = '1' and
                            a.id_kandang != '0'
                            ");
                                @endphp
                                <th>{{ number_format($total_mtd->pcs, 0) }}</th>
                                <th>{{ number_format($total_mtd->kg, 2) }}</th>
                            @endforeach
                        </tr>

                    </tfoot>

                </table>
                @php
                    $cek = DB::selectOne("SELECT a.check FROM stok_telur as a
                WHERE a.tgl = '$tanggal' and a.id_gudang = '1' and
                a.id_kandang != '0'
                group by a.tgl;");
                @endphp


                @if (empty($cek->check))
                @else
                    @if ($cek->check == 'T')
                        <a href="{{ route('CheckMartadah', ['cek' => $cek->check, 'tgl' => $tanggal]) }}"
                            class="float-end btn btn-sm  btn-primary">Save</a>
                    @else
                        <a href="{{ route('CheckMartadah', ['cek' => $cek->check, 'tgl' => $tanggal]) }}"
                            class="float-end btn btn-sm  btn-primary">Unsave</a>
                    @endif
                @endif


                {{-- dasda --}}
                <button class="float-end btn btn-sm btn-primary me-2 history-mtd"><i class="fas fa-history"></i>
                    History
                </button>

            </div>
            <div class="col-lg-6">
                <h6>Stok Transfer Alpa {{ tanggal($tanggal) }}</h6>
                <table class="table table-bordered table-dashboard ">
                    <thead style="font-size: 10px">
                        <tr>
                            @foreach ($produk as $p)
                                <th colspan="2" style="text-align: center" class="dhead">{{ $p->nm_telur }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($produk as $p)
                                <th style="text-align: center" class="dhead">Pcs</th>
                                <th style="text-align: center" class="dhead">Kg</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody style="border-color: #435EBE; font-size: 10px">
                        @foreach ($produk as $p)
                            @php
                                $stok_transfer = DB::selectOne("SELECT sum(a.pcs) as pcs , sum(a.kg) as kg
                        FROM stok_telur as a
                        where a.tgl = '$tanggal' and a.pcs != '0' and a.id_telur = '$p->id_produk_telur' and a.id_gudang
                        = '2'
                        ");
                            @endphp
                            <td>{{ empty($stok_transfer->pcs) ? '0' : number_format($stok_transfer->pcs, 0) }}</td>
                            <td>{{ empty($stok_transfer->kg) ? '0' : number_format($stok_transfer->kg, 2) }}</td>
                        @endforeach
                    </tbody>

                </table>
                @php
                    $cek2 = DB::selectOne("SELECT a.check FROM stok_telur as a
                WHERE a.tgl = '$tanggal' and a.id_gudang = '2' and a.pcs != '0'
                group by a.tgl;");
                @endphp

                @if (empty($cek2))
                @else
                    @if ($cek2->check == 'T')
                        <a href="{{ route('CheckAlpa', ['cek' => $cek2->check, 'tgl' => $tanggal]) }}"
                            class="float-end btn btn-sm  btn-primary">Save</a>
                    @else
                        <a href="{{ route('CheckAlpa', ['cek' => $cek2->check, 'tgl' => $tanggal]) }}"
                            class="float-end btn btn-sm  btn-primary">Unsave</a>
                    @endif
                @endif

                <button class="float-end btn btn-sm btn-primary me-2 history-tf-alpa"><i class="fas fa-history"></i>
                    History</button>
            </div>


            <div class="col-lg-12 mt-4">
                <h6>Stok Telur</h6>
                <table class="table table-bordered ">
                    <thead>
                        <tr>
                            <th rowspan="2" class="dhead" style="vertical-align: middle">Gudang</th>
                            @foreach ($produk as $p)
                                <th colspan="3" style="text-align: center" class="dhead">{{ $p->nm_telur }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($produk as $p)
                                <th style="text-align: center" class="dhead">pcs</th>
                                <th style="text-align: center" class="dhead">kg</th>
                                <th style="text-align: center" class="dhead">ikat</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gudang as $g)
                            <tr>
                                <td>
                                    {{ $g->nm_gudang }}
                                    <a href="#" onclick="event.preventDefault();"
                                        class="badge bg-primary float-end ms-2  text-sm {{ $g->id_gudang_telur == '2' ? 'history-tf-alpa' : 'history-mtd' }} "><i
                                            class="fas fa-history"></i></i>
                                    </a>
                                    <a href="{{ route('penyetoran_telur') }}"
                                        {{ $g->id_gudang_telur == '2' ? '' : 'hidden' }}
                                        class="badge bg-success text-sm float-end" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="penyetoran telur">
                                        <i class="fas fa-money-bill-wave-alt"></i>
                                    </a>
                                    <a href="{{ route('piutang_telur') }}"
                                        {{ $g->id_gudang_telur == '2' ? '' : 'hidden' }}
                                        class="badge bg-primary text-sm me-2 float-end" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Piutang telur"><i
                                            class="far fa-credit-card"></i></i>
                                    </a>
                                    <a href="{{ route('tbh_invoice_telur') }}"
                                        {{ $g->id_gudang_telur == '2' ? '' : 'hidden' }}
                                        class="badge bg-primary me-2 text-sm float-end" data-bs-toggle="tooltip"
                                        data-bs-placement="top" title="Penjualan telur"><i class="fas fa-plus"></i>
                                    </a>
                                </td>
                                @foreach ($produk as $p)
                                    @php
                                        $stok = DB::selectOne("SELECT sum(a.pcs) as pcs , sum(a.kg) as kg, sum(a.pcs_kredit) as
                            pcs_kredit, sum(a.kg_kredit) as kg_kredit
                            FROM stok_telur as a
                            where a.id_gudang ='$g->id_gudang_telur' and a.id_telur = '$p->id_produk_telur' and a.check
                            ='Y' and a.opname = 'T' group by a.id_telur");
                                    @endphp
                                    <td>{{ empty($stok->pcs) ? '0' : number_format($stok->pcs - $stok->pcs_kredit, 0) }}
                                    </td>
                                    <td>{{ empty($stok->kg) ? '0' : number_format($stok->kg - $stok->kg_kredit, 2) }}
                                    </td>
                                    <td>{{ empty($stok->pcs) ? '0' : number_format(($stok->pcs - $stok->pcs_kredit) / 180, 1) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12">
                <hr style="border: 1px solid #435EBE">
            </div>
            <div class="col-lg-10">


                <div class="row">
                    <div class="col-lg-7">
                        <h6>Penjualan martadah</h6> <br>
                    </div>
                </div>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="dhead" width="5">#</th>
                            <th class="dhead">Tipe Penjualan</th>
                            <th class="dhead" style="text-align: right">Total Rp</th>
                            <th class="dhead" style="text-align: right">Yang Sudah Diterima</th>
                            <th class="dhead" style="text-align: right">Sisa</th>
                            <th class="dhead" style="text-align: center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Penjualan Telur</td>
                            <td align="right">Rp
                                {{ number_format($penjualan_cek_mtd->ttl_rp + $penjualan_blmcek_mtd->ttl_rp, 0) }}
                            </td>
                            <td align="right">Rp {{ number_format($penjualan_cek_mtd->ttl_rp, 0) }}</td>
                            <td align="right">Rp {{ number_format($penjualan_blmcek_mtd->ttl_rp, 0) }}</td>
                            <td align="center">
                                <a href="{{ route('penjualan_martadah_cek', ['lokasi' => 'mtd']) }}"
                                    class="btn btn-primary btn-sm"><i class="fas fa-history"></i>
                                    History
                                    <span
                                        class="badge bg-danger">{{ empty($penjualan_blmcek_mtd->jumlah) ? '0' : $penjualan_blmcek_mtd->jumlah }}</span>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Penjualan Umum</td>
                            <td align="right">Rp
                                {{ number_format($penjualan_umum_mtd->ttl_rp + $penjualan_umum_blmcek_mtd->ttl_rp, 0) }}
                            </td>
                            <td align="right">Rp {{ number_format($penjualan_umum_mtd->ttl_rp, 0) }}</td>
                            <td align="right">Rp {{ number_format($penjualan_umum_blmcek_mtd->ttl_rp, 0) }}</td>
                            <td align="center">
                                <a href="{{ route('penjualan_umum_cek') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-history"></i> History
                                    <span
                                        class="badge bg-danger">{{ empty($penjualan_umum_blmcek_mtd->jumlah) ? '0' : $penjualan_umum_blmcek_mtd->jumlah }}</span>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Opname Telur Martadah</td>
                            <td align="right">Rp
                                {{ number_format($opname_cek_mtd->ttl_rp + $opname_blmcek_mtd->ttl_rp, 0) }}
                            </td>
                            <td align="right">Rp {{ number_format($opname_cek_mtd->ttl_rp, 0) }}</td>
                            <td align="right">Rp {{ number_format($opname_blmcek_mtd->ttl_rp, 0) }}</td>
                            <td align="center">
                                <a href="{{ route('bukukan_opname_martadah') }}" class="btn btn-primary btn-sm"><i
                                        class="fas fa-history"></i>
                                    History
                                    <span
                                        class="badge bg-danger">{{ empty($opname_blmcek_mtd->jumlah) ? '0' : $opname_blmcek_mtd->jumlah }}</span>
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>

            </div>



            </div>
        </section>
        <x-theme.modal btnSave='T' title="History Telur Martdah" size="modal-lg-max" idModal="history_mtd">
            <div class="row">
                <div class="col-lg-12">
                    <div id="h_martadah"></div>
                </div>
            </div>

        </x-theme.modal>
        <x-theme.modal btnSave='T' title="History Transfer Alpa" size="modal-lg-max" idModal="history_alpa">
            <div class="row">
                <div class="col-lg-12">
                    <div id="h_alpa"></div>
                </div>
            </div>

        </x-theme.modal>

        <form action="" method="get">
            <x-theme.modal title="View Tanggal" idModal="view">
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Tanggal</label>
                        <input type="date" name="tgl" class="form-control" value="{{ $tanggal }}">
                    </div>
                </div>
            </x-theme.modal>
        </form>
    </x-slot>
    @section('js')
        <script>
            $(document).ready(function() {

                $(document).on('click', '.history-mtd', function() {
                    $.ajax({
                        type: "get",
                        url: "/HistoryMtd",
                        success: function(data) {
                            $('#h_martadah').html(data);
                            $('#history_mtd').modal('show');
                        }
                    });
                });
                $(document).on('click', '.history-tf-alpa', function() {
                    $.ajax({
                        type: "get",
                        url: "/HistoryAlpa",
                        success: function(data) {
                            $('#h_alpa').html(data);
                            $('#history_alpa').modal('show');
                        }
                    });
                });

                $(document).on('submit', '#search_history_mtd', function(e) {
                    e.preventDefault();
                    var tgl1 = $('#tgl1').val();
                    var tgl2 = $('#tgl2').val();
                    $.ajax({
                        type: "get",
                        url: "/HistoryMtd?tgl1=" + tgl1 + "&tgl2=" + tgl2,
                        success: function(data) {
                            $('#h_martadah').html(data);
                        }
                    });
                });
                $(document).on('submit', '#search_history_alpa', function(e) {
                    e.preventDefault();
                    var tgl1 = $('#tgl1').val();
                    var tgl2 = $('#tgl2').val();
                    $.ajax({
                        type: "get",
                        url: "/edit_telur_dashboard?id_kandang=" + id_kandang + "&tgl=" + tgl,
                        success: function(data) {
                            $('#edit_martadah').html(data);
                            $('#edit_mtd').modal('show');
                        }
                    });
                });

            });
        </script>
    @endsection
</x-theme.app>
