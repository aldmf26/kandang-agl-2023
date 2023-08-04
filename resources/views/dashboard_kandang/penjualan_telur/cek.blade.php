<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">

    <section class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            
                            <x-theme.button modal="T" href="{{ route('dashboard_kandang.add_penjualan_telur') }}"
                                icon="fa-plus" addClass="float-end" teks="Buat Nota" />
                            <x-theme.button modal="T" href="{{ route('dashboard_kandang.penjualan_telur') }}"
                                icon="fa-history" addClass="float-end" teks="History" />
                            <x-theme.btn_dashboard route="dashboard_kandang.index" />
                            <br>
                            <br>
                            <hr>
                        </div>
                        <div class="col-lg-6">
                            <img src="https://agrilaras.putrirembulan.com/assets/img/logo.png" alt="Logo" width="150px">
                        </div>
                        <div class="col-lg-6">
                            <table>
                                <tr>
                                    <td style="padding: 5px">Tanggal</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">{{Tanggal($invoice2->tgl)}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px">No. Nota</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">{{$invoice2->no_nota}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px">Kpd Yth</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">Bpk/Ibu {{$invoice2->customer}}</td>
                                </tr>
                                {{-- <tr>
                                    <td style="padding: 5px">Pengirim</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px"></td>
                                </tr> --}}
                            </table>
                        </div>
                    </div>
                </div>
                <h6 class="text-center">
                    Cek Nota Penjualan Telur
                </h6>
                <div class="card-body">
                    <table class="table  table-bordered" style="white-space: nowrap;">
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
                            </tr>
                            <tr>


                                <th class="dhead abu" width="7%" style="text-align: center">Pcs</th>
                                <th class="dhead abu" width="7%" style="text-align: center">Kg</th>
                                <th class="dhead abu" width="10%" style="text-align: center;">Rp Pcs</th>

                                <th class="dhead putih" width="7%" style="text-align: center;">Ikat</th>
                                <th class="dhead putih" width="7%" style="text-align: center;">Kg</th>
                                <th class="dhead putih" width="10%" style="text-align: center;">Rp Ikat</th>

                                <th class="dhead abuGelap" width="7%" style="text-align: center;">Pcs</th>
                                <th class="dhead abuGelap" width="7%" style="text-align: center;">Kg</th>
                                {{-- <th class="dhead" width="7%" style="text-align: center;">Rak</th> --}}
                                <th class="dhead abuGelap" width="10%" style="text-align: center;">Rp Rak</th>


                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $total_semua = 0;
                            $ttl_pcs = 0;
                            $ttl_kg_kotor = 0;
                            $ttl_kg_bersih = 0;
                            @endphp
                            @foreach ($invoice as $i)
                            <tr>

                                <td>{{$i->nm_telur}}</td>
                                <td align="right">{{$i->pcs_pcs}}</td>
                                <td align="right">{{$i->kg_pcs}}</td>
                                <td align="right">Rp. {{number_format($i->rp_pcs,0)}}</td>
                                <!-- Jual Ikat -->
                                <td align="right">{{$i->ikat}}</td>
                                <td align="right">{{$i->kg_ikat}}</td>
                                <td align="right">Rp. {{number_format($i->rp_ikat,0)}}</td>
                                <!-- Jual Ikat -->
                                <!-- Jual Kg -->
                                <td align="right">{{$i->pcs_kg}}</td>
                                <td align="right">{{$i->kg_kg}}</td>
                                {{-- <td align="right">{{$i->rak_kg}}</td> --}}
                                <td align="right">Rp. {{number_format($i->rp_kg,0)}}</td>
                                <!-- Jual Kg -->
                                <td align="right">
                                    @php
                                    $rp_pcs = $i->pcs_pcs * $i->rp_pcs;
                                    $rp_ikat = ($i->kg_ikat - $i->ikat) * $i->rp_ikat;
                                    // $rak_kali = round($i->rak_kg * 0.12,1);
                                    $rak_kotor = round(($i->pcs_kg/15) * 0.12,1);
                                    $kg_rak_kotor = $i->kg_kg + $rak_kotor;
                                    $rp_kg = $i->kg_kg * $i->rp_kg;
                                    $total_rp = $rp_pcs + $rp_ikat + $rp_kg;

                                    $ikat_kg_bersih = $i->kg_ikat - $i->ikat;

                                    @endphp
                                    Rp. {{number_format($total_rp,0)}}
                                </td>
                            </tr>
                            @php
                            $total_semua += $total_rp;
                            $ttl_pcs += $i->pcs_pcs + ($i->ikat * 180) + $i->pcs_kg;
                            $ttl_kg_kotor += $i->kg_pcs + $i->kg_ikat + $kg_rak_kotor;
                            $ttl_kg_bersih += $ikat_kg_bersih + $i->kg_kg;
                            @endphp
                            @endforeach


                        </tbody>
                        {{-- <tfoot>
                            <tr>
                                <td colspan="9"></td>
                                <th>Total</th>
                                <th style="text-align: right">Rp. {{number_format($total_semua,0)}}</th>
                            </tr>
                        </tfoot> --}}
                    </table>
                    <table width="100%">
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>Total Pcs</td>
                            <td>:</td>
                            <td>{{number_format($ttl_pcs,0)}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>Total (Bruto)</td>
                            <td>:</td>
                            <td>{{number_format($ttl_kg_kotor,0)}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>Berat Bersih (Netto)</td>
                            <td>:</td>
                            <td>{{number_format($ttl_kg_bersih,0)}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="3"></td>
                            <td><b>JUMLAH TOTAL </b></td>
                            <td><b>Rp.{{ number_format($total_semua,0)}}</b></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </section>
    @section('scripts')
    @endsection
</x-theme.app>