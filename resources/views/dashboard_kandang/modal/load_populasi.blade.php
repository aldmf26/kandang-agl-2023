<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <tr>
                <th class="dhead">Tanggal</th>
                <th class="dhead">Kandang</th>
                <th class="dhead text-end">Mati / Death</th>
                <th class="dhead text-end">Jual / Culling</th>
                <th class="dhead text-end">Afkir</th>
            </tr>
            @foreach ($kandang as $k)
            @php
            $populasi = DB::table('populasi')->where([['id_kandang', $k->id_kandang], ['tgl', date('Y-m-d')]])->first();
            @endphp
            <tr>
                <td>
                    <input type="date" readonly value="{{ date('Y-m-d') }}" name="tgl[]" class="form-control">
                </td>
                <td>
                    <input name="id_kandang[]" type="hidden" value="{{ $k->id_kandang }}">
                    <input readonly class="form-control" type="text" value="{{ $k->nm_kandang }}">
                </td>
                <td>
                    <input autofocus value="{{ $populasi->mati ?? 0 }}" type="text" class="form-control selectAll text-end" name="mati[]">
                </td>
                <td>
                    <input value="{{ $populasi->jual ?? 0 }}" type="text" class="form-control selectAll text-end" name="jual[]">
                </td>
                <td>
                    <input value="{{ $populasi->afkir ?? 0 }}" type="text" class="form-control selectAll text-end" name="afkir[]">
                </td>
            </tr>

            @endforeach
        </table>

    </div>


</div>