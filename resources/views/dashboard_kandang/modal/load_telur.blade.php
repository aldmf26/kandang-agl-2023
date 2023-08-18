<div class="row">
    <div class="col-lg-6">
        <table class="table">
            <tr>
                <th class="dhead">Tanggal</th>
                <th class="dhead">Kandang</th>
            </tr>
            <tr>
                <td>
                    <input type="date" value="{{ date('Y-m-d') }}" name="tgl" class="form-control">
                </td>
                <td>
                    <input type="hidden" value="{{ $kandang->id_kandang }}" name="id_kandang"
                        id="id_kandang_tambah_telur">
                    <input readonly type="text" class="form-control" value="{{ $kandang->nm_kandang }}"
                        id="nm_kandang_tambah_telur">
                </td>
            </tr>
        </table>

    </div>
    <div class="col-lg-12">
        <table class="table table-bordered text-center">
            <tr>
                <th class="dhead" width="50%" style="vertical-align: middle">Produk</th>
                <th class="dhead">Pcs</th>
                <th class="dhead">Kg</th>
            </tr>


            @foreach ($telur as $i => $d)
            @php
            $cek = DB::table('stok_telur')
            ->where([['id_telur', $d->id_produk_telur], ['id_kandang', $kandang->id_kandang], ['tgl', date('Y-m-d')]])
            ->first();
            @endphp

            <input type="hidden" name="id_telur[]" value="{{ $d->id_produk_telur }}">
            <tr>
                <td align="left">{{ ucwords($d->nm_telur) }}</td>
                <td>
                    <input value="{{ $cek->pcs ?? 0 }}" type="text" name="pcs[]" class="form-control text-end"
                        count="{{ $i + 1 }}">
                </td>
                <td>
                    <input value="{{ $cek->kg ?? 0 }}" type="text" name="kg[]" class="form-control text-end">
                </td>

            </tr>
            @endforeach
        </table>


    </div>
</div>