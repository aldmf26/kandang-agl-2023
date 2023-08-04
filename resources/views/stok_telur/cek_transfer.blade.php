<x-theme.app title="{{ $title }}" table="T" sizeCard="12" cont="container-fluid">

    <section class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-lg-12">
                            <x-theme.button modal="T"
                                href="{{ route('dashboard_kandang.add_transfer_stok', ['id_gudang' => 1]) }}"
                                icon="fa-plus" addClass="float-end" teks="Buat Nota" />
                            <x-theme.button modal="T"
                                href="{{ route('dashboard_kandang.edit_transfer_stok', ['nota' => $nota]) }}"
                                icon="fa-pen" addClass="float-end" teks="Edit Nota" />
                            <x-theme.btn_dashboard route="dashboard_kandang.index" />
                            <br>
                            <br>
                            <hr>
                        </div>
                        <div class="col-lg-6">
                            <img src="https://agrilaras.putrirembulan.com/assets/img/logo.png" alt="Logo"
                                width="150px">
                        </div>
                        <div class="col-lg-6">
                            <table>
                                <tr>
                                    <td style="padding: 5px">Tanggal</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">{{ Tanggal($tgl) }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px">No. Nota </td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">{{ $nota }} <span class="text-warning">(copy di nota manual)</span></td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px">Admin</td>
                                    <td style="padding: 5px">:</td>
                                    <td style="padding: 5px">{{ auth()->user()->name }}</td>
                                </tr>


                            </table>
                        </div>
                    </div>
                </div>
                <h6 class="text-center">Transfer Telur Martadah</h6>
                <div class="card-body">
                    <table class="table  table-striped" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="dhead" width="10%" rowspan="2">Produk </th>
                                <th style="text-align: center" class="dhead abu" colspan="2">Per pcs</th>
                                <th style="text-align: center" class="dhead putih" colspan="2">Per ikat
                                </th>
                                <th style="text-align: center" class="dhead abuGelap" colspan="3">Rak
                                </th>

                            </tr>
                            <tr>


                                <th class="dhead text-end abu" width="7%" style="text-align: center">Pcs</th>
                                <th class="dhead text-end abu" width="7%" style="text-align: center">Kg</th>

                                <th class="dhead text-end putih" width="7%" style="text-align: center;">Ikat</th>
                                <th class="dhead text-end putih" width="7%" style="text-align: center;">Kg</th>

                                <th class="dhead text-end abuGelap" width="7%" style="text-align: center;">Pcs</th>
                                <th class="dhead text-end abuGelap" width="7%" style="text-align: center;">Kg</th>
                                <th class="dhead text-end abuGelap" width="7%" style="text-align: center;">Rak</th>


                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $ttl_pcs = 0;
                                $ttl_kg = 0;
                                $ttl_rak = 0;
                            @endphp
                            @foreach ($datas as $d)
                                @php
                                    $ttl_pcs += $d->pcs_pcs + $d->ikat * 180 + $d->pcs_kg;
                                    $ttl_kg += $d->pcs_kg + $d->kg_ikat + (($d->pcs_kg / 15) * 0.12 + $d->kg_kg);
                                    $ttl_rak += $d->rak_kg;
                                @endphp
                                <tr class="baris1">
                                    <input type="hidden" name="id_invoice_mtd[]" value="{{ $d->id_invoice_mtd }}">
                                    <td>
                                        @foreach ($produk as $p)
                                            {{ $p->id_produk_telur == $d->id_produk ? $p->nm_telur : '' }}
                                        @endforeach

                                    </td>
                                    <td align="right">
                                        {{ $d->pcs_pcs }}

                                    </td>
                                    <td align="right">
                                        {{ $d->kg_pcs }}
                                    </td>

                                    <!-- Jual Ikat -->
                                    <td align="right">
                                        {{ $d->ikat }}

                                    </td>
                                    <td align="right">
                                        {{ $d->kg_ikat }}

                                    </td>
                                    <!-- Jual Kg -->
                                    <td align="right">
                                        {{ $d->pcs_kg }}

                                    </td>
                                    <td align="right">
                                        {{ $d->kg_kg }}

                                    </td>
                                    <td align="right">
                                        {{ $d->rak_kg }}

                                    </td>
                                    <!-- Jual Kg -->

                                </tr>
                            @endforeach


                        </tbody>

                    </table>
                    <table width="50%" class="" style="white-space: nowrap;">
                        {{-- <thead>
                            <tr>
                                <th class="dhead text-center" colspan="4">Total </th>
                            </tr>
                            <tr>
                                <th class="dhead abu" width="2%" style="text-align: center">Pcs</th>
                                <th class="dhead putih" width="2%" style="text-align: center;">Kg</th>
                                <th class="dhead putih" width="2%" style="text-align: center;">Ikat</th>
                                <th class="dhead putih" width="2%" style="text-align: center;">Rak</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="right">{{ number_format($ttl_pcs, 0) }}</td>
                                <td align="right">{{ number_format($ttl_kg, 0) }}</td>
                                <td align="right">{{ number_format($ttl_pcs / 180, 2) }}</td>
                                <td align="right">{{ number_format($ttl_rak, 0) }}</td>



                            </tr>
                        </tbody> --}}
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>Total Pcs</td>
                            <td>:</td>
                            <td>{{number_format($ttl_pcs,0)}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>Total Kg</td>
                            <td>:</td>
                            <td>{{number_format($ttl_kg,0)}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>Total Ikat</td>
                            <td>:</td>
                            <td>{{number_format($ttl_pcs / 180,2)}}</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                            <td>Total Rak</td>
                            <td>:</td>
                            <td>{{number_format($ttl_rak,0)}}</td>
                            <td></td>
                        </tr>

                    </table>
                </div>
            </div>
        </div>

    </section>
    @section('scripts')
    @endsection
</x-theme.app>
