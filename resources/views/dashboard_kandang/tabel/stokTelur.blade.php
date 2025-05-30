<div class="row">
    @if (session()->has('errorMin'))
        <x-theme.alert pesan="ada input yang salah !" />
    @endif
    <div class="col-lg-12">
        <h6>
            Stok Telur
        </h6>
        <table class="table table-bordered text-center">
            @php
                $ttlPcs = 0;
                $ttlKg = 0;
                $ttlIkat = 0;
            @endphp
            <tr>
                <th class="dhead" rowspan="2" style="vertical-align: middle">Gudang</th>
                @foreach ($telur as $d)
                    <th class="dhead" colspan="3">
                        {{ ucwords(str_replace('telur', '', strtolower($d->nm_telur))) }}</th>
                @endforeach
            </tr>

            <tr>
                @php
                    $telur = DB::table('telur_produk')->get();
                @endphp
                @foreach ($telur as $d)
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Kg</th>
                    <th class="dhead text-end">Ikat</th>
                @endforeach
            </tr>
            <tr>
                <td align="left">
                    Martadah
                    <a href="{{ route('opnamemtd', ['id_gudang' => '1']) }}"
                        class="badge float-end me-2 bg-primary text-sm">Opname</a>
                    <a data-bs-toggle="modal" data-bs-target="#history_opn" href="#"
                        class="badge float-end me-2 bg-primary text-sm history_opname">History Opname</a>
                </td>
                @foreach ($telur as $d)
                    @php
                        $stok = DB::selectOne("SELECT SUM(pcs - pcs_kredit) as pcs, SUM(kg - kg_kredit) as kg FROM
                `stok_telur`
                WHERE id_telur = '$d->id_produk_telur' AND id_gudang = 1 AND opname = 'T';");
                        request()->session()->forget('errorMin', '1');
                        if ($stok->pcs < 0) {
                            request()->session()->put('errorMin', '1');
                        }
                    @endphp
                    <td align="right">{{ number_format($stok->pcs, 0) }}</td>
                    <td align="right">{{ number_format($stok->kg, 0) }}</td>
                    <td align="right">{{ number_format($stok->pcs / 180, 0) }}</td>
                @endforeach
            </tr>
            <tr>
                <td align="left">
                    Penjualan Martadah
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Penjualan Martadah"
                        href="{{ route('dashboard_kandang.add_penjualan_telur') }}"
                        class="badge float-end me-2 bg-primary text-sm"><i class="fas fa-plus"></i></a>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="History Penjualan Martadah"
                        href="{{ route('penjualan_agrilaras', ['id_gudang' => 1]) }}"
                        class="badge float-end me-2 bg-primary text-sm">History</i>
                    </a>
                </td>
                @foreach ($telur as $d)
                    @php
                        $stok = DB::selectOne("SELECT SUM(pcs_kredit) as pcs, SUM(kg_kredit) as kg FROM `stok_telur`
                WHERE id_telur = '$d->id_produk_telur' AND jenis = 'penjualan' AND opname = 'T';");

                    @endphp
                    <td align="right">{{ number_format($stok->pcs, 0) ?? 0 }}</td>
                    <td align="right">{{ number_format($stok->kg, 0) ?? 0 }}</td>
                    <td align="right">{{ number_format($stok->pcs / 180, 0) }}</td>
                @endforeach
            </tr>
            <tr>
                <td align="left">
                    Transfer Alpa
                    <a href="{{ route('opnamemtd', ['id_gudang' => '2']) }}"
                        class="badge float-end me-2 bg-primary text-sm">Opname</a>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Transfer Alpa"
                        href="{{ route('dashboard_kandang.add_transfer_stok', ['id_gudang' => 1]) }}"
                        class="badge float-end me-2 bg-primary text-sm"><i class="fas fa-plus"></i></a>
                    <a data-bs-toggle="tooltip" data-bs-placement="top" title="History Transfer Alpa"
                        href="{{ route('dashboard_kandang.transfer_stok', ['id_gudang' => 1]) }}"
                        class="badge float-end me-2 bg-primary text-sm">History
                    </a>


                </td>
                @foreach ($telur as $d)
                    @php
                        $stok = DB::selectOne("SELECT SUM(pcs - pcs_kredit) as pcs, SUM(kg - kg_kredit) as kg FROM
                `stok_telur`
                WHERE id_telur = '$d->id_produk_telur' AND id_gudang = 2 AND opname = 'T'");

                    @endphp
                    <td align="right">{{ number_format($stok->pcs) ?? 0 }}</td>
                    <td align="right">{{ number_format($stok->kg, 1) ?? 0 }}</td>
                    <td align="right">{{ number_format($stok->pcs / 180, 0) }}</td>
                @endforeach
            </tr>
        </table>
    </div>

</div>

@include('dashboard_kandang.modal.history_opname')
