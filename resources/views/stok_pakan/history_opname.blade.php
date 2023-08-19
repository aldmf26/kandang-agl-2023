<form id="search_history_pakvit">
    <div class="row">
        <div class="col-lg-4 mb-2">
            <label for="">Dari</label>
            <input type="date" class="form-control" id="tgl1" value="{{$tgl1}}">
            <input type="hidden" class="form-control" id="jenis" value="{{$jenis}}">
        </div>
        <div class="col-lg-4 mb-2">
            <label for="">Sampai</label>
            <input type="date" class="form-control" id="tgl2" value="{{$tgl2}}">
        </div>
        <div class="col-lg-2">
            <label for="">Aksi</label> <br>
            <button type="submit" class="btn btn-sm btn-primary">Filter</button>
        </div>
    </div>
</form>
<table class="table table-bordered" id="tablePakvit">
    <thead>
        <tr>
            <th class="dhead">Tanggal</th>
            <th class="dhead text-center">No Nota</th>
            <th class="dhead">Nama Produk</th>
            <th class="dhead" style="text-align: right">Stok Masuk</th>
            <th class="dhead" style="text-align: right">Stok Keluar</th>
            <th class="dhead">Opname</th>
            <th class="dhead" style="text-align: right">Admin</th>
        </tr>
    </thead>
    <tbody style="border-color: #435EBE;">
        @php
            $saldo = 0;
        @endphp
        @foreach ($history as $no => $d)
        @php
        $saldo += $d->pcs - $d->pcs_kredit
        @endphp
            <tr>
                <td>{{ tanggal($d->tgl) }}</td>
                <td align="center">{{ $d->no_nota }}</td>
                <td>{{ ucwords($d->nm_produk) }}</td>
                <td align="right">{{ number_format($d->pcs,1) }}</td>
                <td align="right">{{ number_format($d->pcs_kredit,1) }}</td>
                <td align="center">
                    <i
                        class="fas {{$d->h_opname == 'Y' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} "></i>
                </td>
                <td>{{$d->admin}}</td>
            </tr>
        @endforeach
    </tbody>
</table>