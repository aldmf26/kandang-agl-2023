<table class="table" id="table1">
    <thead>
        <tr>
            <th>#</th>
            <th>Nota</th>
            <th>Tanggal</th>
            <th>Pelanggan</th>
            <th>Nama Produk</th>
            <th>Harga Satuan</th>
            <th>Qty</th>
            <th>Di Terima</th>
            <th>Total Rp </th>
        </tr>
    </thead>
    <tbody>

        @foreach ($penjualan as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->kode }}-{{ $d->urutan }}</td>
                <td>{{ tanggal($d->tgl_jual) }}</td>
                <td>{{ $d->id_customer }}</td>
                <td>{{ $d->nm_produk }}</td>
                <td>{{ $d->rp_satuan }}</td>
                <td>{{ $d->qty }}</td>
                <td><span class="btn btn-sm btn-success">{{ ucwords($d->admin_cek) ?? '' }}</span></td>
                <td>{{ $d->total }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
