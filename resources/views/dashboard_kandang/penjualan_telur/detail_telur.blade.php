<tr class="show_detail{{$no_nota}}">
    <td></td>
    <td colspan="7">
        <table class="table table-striped table-bordered" style="white-space: nowrap;">
            <thead>
                <tr>
                    <th class="dhead" width="10%">Produk </th>
                    <th class="dhead" width="7%" style="text-align: center">Pcs</th>
                    <th class="dhead" width="7%" style="text-align: center">Kg</th>
                    <th class="dhead" width="10%" style="text-align: center;">Rp Pcs</th>

                    <th class="dhead" width="7%" style="text-align: center;">Ikat</th>
                    <th class="dhead" width="7%" style="text-align: center;">Kg</th>
                    <th class="dhead" width="10%" style="text-align: center;">Rp Ikat</th>

                    <th class="dhead" width="7%" style="text-align: center;">Pcs</th>
                    <th class="dhead" width="7%" style="text-align: center;">Kg</th>
                    <th class="dhead" width="7%" style="text-align: center;">Rak</th>
                    <th class="dhead" width="10%" style="text-align: center;">Rp Rak</th>

                    <th class="dhead" width="10%" style="text-align: center; white-space: nowrap;">Total Rp
                    </th>
                </tr>
            </thead>
            <tbody>
                @php
                $total_semua = 0;
                @endphp
                @foreach ($telur as $i)

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
                    <td align="right">{{number_format($i->rak_kg,1)}}</td>
                    <td align="right">Rp. {{number_format($i->rp_kg,0)}}</td>
                    <!-- Jual Kg -->
                    <td align="right">
                        @php
                        $rp_pcs = $i->pcs_pcs * $i->rp_pcs;
                        $rp_ikat = ($i->kg_ikat - $i->ikat) * $i->rp_ikat;
                        $rak_kali = round($i->rak_kg * 0.12,1);
                        $rp_kg = $i->kg_kg * $i->rp_kg;
                        $total_rp = $rp_pcs + $rp_ikat + $rp_kg;

                        @endphp
                        Rp. {{number_format($total_rp,0)}}
                    </td>
                </tr>
                @php
                $total_semua += $total_rp;
                @endphp
                @endforeach


            </tbody>
            <tfoot>
                <tr>
                    <td colspan="10"></td>
                    <th>Total</th>
                    <th style="text-align: right">Rp. {{number_format($total_semua,0)}}</th>
                </tr>
            </tfoot>
        </table>
    </td>
</tr>