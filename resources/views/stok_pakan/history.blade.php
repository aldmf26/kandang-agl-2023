<div class="row">
    <div class="col-lg-12">
        <table class="table" id="tblPakanHistory">
            <thead>
                <tr>
                    <th class="dhead">Nota</th>
                    <th class="dhead">Tgl</th>
                    <th class="dhead">Nama Pakan</th>
                    <th class="dhead">Pcs</th>
                    <th class="dhead">Ttl Rp</th>
                    <th class="dhead">Admin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $i => $d)
                    <tr>
                        <td>{{ $d->no_nota }}</td>
                        <td>{{ tanggal($d->tgl) }}</td>
                        <td>{{ $d->nm_produk }}</td>
                        <td>{{ number_format($d->pcs,0) }}</td>
                        <td>{{ number_format($d->total_rp,0) }}</td>
                        <td>{{ ucwords($d->admin) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>