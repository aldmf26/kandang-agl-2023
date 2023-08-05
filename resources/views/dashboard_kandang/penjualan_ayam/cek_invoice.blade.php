<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">

    <section class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <x-theme.btn_dashboard route="dashboard_kandang.index" />
                            <br>
                            <br>
                            <hr>
                        </div>
                        <div class="col-lg-5">
                            <img src="https://agrilaras.putrirembulan.com/assets/img/logo.png" alt="Logo" width="150px">
                        </div>
                        <div class="col-lg-7">
                            <table>
                                <tr>
                                    <td style="padding: 5px">Tanggal</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">{{Tanggal($ayam->tgl)}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px">No. Nota</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">{{$ayam->no_nota}} <span class="text-danger">(mohon
                                            dicopy
                                            di nota manual)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px">Kpd Yth</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">Bpk/Ibu {{$ayam->customer}}</td>
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
                    Cek Nota Penjualan Ayam
                </h6>
                <div class="card-body">
                    <table class="table  table-bordered" style="white-space: nowrap;">
                        <thead>
                            <tr>


                                <th class="dhead abu" width="7%" style="text-align: center">Ekor</th>
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

                        {{-- <tfoot>
                            <tr>
                                <td colspan="9"></td>
                                <th>Total</th>
                                <th style="text-align: right">Rp. {{number_format($total_semua,0)}}</th>
                            </tr>
                        </tfoot> --}}
                    </table>
                    {{-- <table width="100%">
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
                    </table> --}}
                </div>
            </div>
        </div>

    </section>
    @section('scripts')
    @endsection
</x-theme.app>