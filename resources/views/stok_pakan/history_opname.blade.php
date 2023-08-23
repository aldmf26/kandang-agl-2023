<form id="search_history_pakvit">
    <div class="row">
        <div class="col-lg-4 mb-2">
            <label for="">Dari</label>
            <input type="date" class="form-control" id="tgl1" value="{{ $tgl1 }}">
            <input type="hidden" class="form-control" id="jenis" value="{{ $jenis }}">
        </div>
        <div class="col-lg-4 mb-2">
            <label for="">Sampai</label>
            <input type="date" class="form-control" id="tgl2" value="{{ $tgl2 }}">
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
            <th style="text-align: right" class="dhead">Stok Program</th>
            <th style="text-align: right" class="dhead">Stok Aktual</th>
            <th style="text-align: right" class="dhead">Stok Selisih</th>
            <th style="text-align: right" class="dhead">Harga Satuan</th>
            <th style="text-align: right" class="dhead">Rupiah</th>
            <th class="dhead">Opname</th>
            <th class="dhead">Admin</th>
        </tr>
    </thead>
    <tbody style="border-color: #435EBE;">
        @php
            $saldo = 0;
        @endphp
        @foreach ($history as $no => $d)
            @php
                $stokProgram = $d->stok - $d->pcs + $d->pcs_kredit;
                $selisih = $d->stok - $stokProgram;
                if ($d->sum_ttl_rp != 0) {
                    $hargaSatuan = $d->sum_ttl_rp / $d->pcs_sum_ttl_rp;
                } else {
                    $hargaSatuan = 0;
                }
                
                $selisihRupiah = $hargaSatuan * $selisih;
                // $ttlRp += $selisih < 0 ? $selisihRupiah * -1 : $selisihRupiah;
                $saldo += $d->pcs - $d->pcs_kredit;
            @endphp
            <tr>
                <td>{{ tanggal($d->tgl) }}</td>
                @if ($d->h_opname == 'Y')
                    <td align="center"><a href="dashboard_kandang/print_opname/{{ $d->no_nota }}/print"
                            target="_blank">{{ $d->no_nota }}</a></td>
                @else
                    <td align="center">{{ $d->no_nota }}</td>
                @endif
                <td>{{ ucwords($d->nm_produk) }}</td>
                <td align="right">{{ number_format($stokProgram, 1) }}</td>
                <td align="right">{{ number_format($d->stok, 1) }}</td>
                <td align="right">{{ number_format($d->stok - $stokProgram, 1) }}</td>
                <td align="right">{{ number_format($hargaSatuan, 1) }}</td>
                <td align="right">
                    {{ number_format($selisih < 0 ? $selisihRupiah * -1 : $selisihRupiah, 0) }}
                </td>
                
                <td align="center">
                    <i
                        class="fas {{ $d->h_opname == 'Y' ? 'fa-check-circle text-success' : 'fa-times-circle text-danger' }} "></i>
                </td>
                <td>{{ $d->admin }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
