<div class="row">
    <div class="col-lg-12">
        <table class="table">
            <tr>
                <th class="dhead">Tanggal</th>
                <th class="dhead">Kandang</th>
                <th class="dhead">Mati / Death</th>
                <th class="dhead">Jual / Culling</th>
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
                    <input autofocus value="{{ $populasi->mati ?? 0 }}" type="text" class="form-control" name="mati[]">
                </td>
                <td>
                    <input value="{{ $populasi->jual ?? 0 }}" type="text" class="form-control" name="jual[]">
                </td>
            </tr>

            @endforeach
        </table>

    </div>


</div>