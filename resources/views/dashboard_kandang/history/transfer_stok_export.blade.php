<table class="table table-hover" id="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Tanggal</th>
            <th>Nota</th>
            <th>Pcs</th>
            <th>Kg</th>
            <th>Ikat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($penjualan as $no => $s)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ tanggal($s->tgl) }}</td>
                <td>{{ $s->no_nota }}</td>
                <td align="right">{{ $s->pcs }}</td>
                <td align="right">{{ $s->kg }}</td>
                <td align="right">{{ $s->pcs / 180 }} </td>
            </tr>
        @endforeach

    </tbody>
</table>