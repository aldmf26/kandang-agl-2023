<div class="row">
    <div class="col-lg-12">
        <table class="table table-bordered text-center">
            <tr>
                <th class="dhead" width="20%" style="vertical-align: middle" rowspan="2">Produk</th>
                <th class="dhead" colspan="2">Per Ikat</th>
                <th class="dhead" colspan="2">Full Rak</th>
                <th class="dhead" colspan="2">Per Pcs (Timbang dengan raknya)</th>
            </tr>
            <tr>
                <th class="dhead" width="9%">Ikat</th>
                <th class="dhead" width="9%">Kg</th>
                <th class="dhead" width="9%">Rak</th>
                <th class="dhead" width="9%">Kg</th>
                <th class="dhead" width="9%">Pcs</th>
                <th class="dhead" width="9%">Kg</th>
                {{-- <th class="dhead" width="9%">Potongan</th> --}}

                {{-- <th class="dhead" width="15%">Total Kg</th> --}}
            </tr>

            @foreach ($telur as $i => $d)
                @php
                    $cek = DB::table('stok_telur_new')
                        ->where([['id_telur', $d->id_produk_telur], ['id_kandang', $id_kandang], ['tgl', $tgl]])
                        ->first();
                @endphp

                <input type="hidden" name="id_telur[]" value="{{ $d->id_produk_telur }}">
                <tr>
                    <td align="left">{{ ucwords($d->nm_telur) }}</td>
                    <td>
                        <input readonly value="{{ $cek->ikat ?? 0 }}" type="text" name="ikat[]" class="form-control tdHistoryInputTelur text-end"
                            count="{{ $i + 1 }}">
                    </td>
                    <td>
                        <input readonly value="{{ $cek->ikat_kg ?? 0 }}" type="text" name="ikat_kg[]" class="form-control tdHistoryInputTelur text-end">
                    </td>
                    <td>
                        <input readonly type="text" value="{{ $cek->rak ?? 0 }}" class="form-control tdHistoryInputTelur text-end" name="rak[]">
                    </td>
                    <td>
                        <input readonly type="text" name="rak_kg[]" value="{{ $cek->rak_kg ?? 0 }}" class="form-control tdHistoryInputTelur text-end ">
                    </td>
                    <td>
                        <input readonly type="text" name="pcs[]" value="{{ $cek->pcs ?? 0 }}"
                            class="form-control tdHistoryInputTelur text-end pcs pcs{{ $i + 1 }}" count="{{ $i + 1 }}">
                    </td>
                    <td>
                        <input readonly type="text" name="pcs_kg[]" value="{{ $cek->pcs_kg ?? 0 }}"
                            class="form-control tdHistoryInputTelur text-end kgPcs kgPcs{{ $i + 1 }}" count="{{ $i + 1 }}">
                    </td>
                    {{-- <td>
                        <input type="text"
                            value="{{ !empty($cek) ? ($cek->pcs == 0 ? 0 : $cek->potongan_pcs ?? 0) : 0 }}" readonly
                            class="form-control potongan{{ $i + 1 }}" name="potongan_pcs[]">
                    </td>
                    <td>
                        <input type="text"
                            value="{{ !empty($cek) ? ($cek->pcs == 0 ? 0 : $cek->ttl_kg_pcs ?? 0) : 0 }}" readonly
                            class="form-control ttlKgPcs{{ $i + 1 }}" name="ttl_kg_pcs[]">
                    </td> --}}
                </tr>
            @endforeach
        </table>
    </div>
</div>