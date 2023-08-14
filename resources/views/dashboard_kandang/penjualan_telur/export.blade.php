<table class="table table-hover" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Nota</th>
            <th>Customer</th>
            <th>Total Rp </th>
            <th>Diterima</th>
            @php
                $detail = ['pcs_pcs', 'pcs_kg', 'rp_pcs', 'ikat_ikat', 'ikat_kg', 'rp_ikat', 'rak_pcs', 'rak_kg_kotor', 'rak_kg_bersih', 'rp_rak'];
            @endphp
            @foreach ($detail as $d)
                <th>{{ ucwords(str_replace('_', ' ', $d)) }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($penjualan as $no => $s)
            <tr class="induk_detail{{ $s->no_nota }}">
                <td>{{ $no + 1 }}</td>
                <td>{{ tanggal($s->tgl) }}</td>
                <td>{{ $s->no_nota }}</td>
                <td>{{ $s->customer }}</td>
                <td align="right">{{ $s->ttl_rp }} </td>
                <td><span class="btn btn-sm btn-success">{{ ucwords($s->admin_cek) ?? '' }}</span></td>
                @foreach ($detail as $d)
                    <th>{{ $s->$d }}</th>
                @endforeach
            </tr>
        @endforeach

    </tbody>
</table>
