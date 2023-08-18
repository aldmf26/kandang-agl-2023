<div class="row">
    <div class="col-lg-4">
        <table class="table table-bordered text-center">
            <tr>
                <th class="dhead" width="35%" style="vertical-align: middle">Produk</th>
                <th class="dhead">Pcs</th>
                <th class="dhead">Kg</th>
            </tr>
            @foreach ($telur as $i => $d)
                @php
                    $cek = DB::table('stok_telur')
                        ->where([['id_telur', $d->id_produk_telur], ['id_kandang', $id_kandang], ['tgl', $tgl]])
                        ->first();
                @endphp

                <input type="hidden" name="id_telur[]" value="{{ $d->id_produk_telur }}">
                <tr>
                    <td align="left">{{ ucwords($d->nm_telur) }}</td>
                    <td>
                        <input readonly value="{{ $cek->pcs ?? 0 }}" type="text" name="pcs[]" class="form-control tdHistoryInputTelur text-end"
                            count="{{ $i + 1 }}">
                    </td>
                    <td>
                        <input readonly value="{{ $cek->kg ?? 0 }}" type="text" name="kg[]" class="form-control tdHistoryInputTelur text-end">
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
</div>