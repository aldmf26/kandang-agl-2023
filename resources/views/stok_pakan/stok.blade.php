<div class="row">
    <div class="col-lg-4">
        <div class="row mb-2">
            <div class="col-lg-3">
                @php
                    $ttlPakan = 0;
                    foreach ($pakan as $d) {
                        $pakanD = $d->pcs_debit - $d->pcs_kredit;
                        $ttlPakan += $pakanD;
                    }
                    $ttlPakan = $ttlPakan / ($total_populasi * 100);
                @endphp
                <h6>Stok Pakan ({{ number_format($ttlPakan, 0) }} Hari)</h6>
            </div>
            <div class="col-lg-3">
                <input id="pencarianPakan" placeholder="Pencarian" type="text" class="form-control">
            </div>
            <div class="col-lg-6">
                <button type="button" data-bs-toggle="tooltip" data-bs-placement="top" title="Opname"
                    class="btn btn-primary btn-sm float-end opnme_pakan me-2">Opname</i>
                </button>
                <a href="#" onclick="event.preventDefault();" data-bs-toggle="modal" data-bs-target="#tbh_pakan"
                    class="btn btn-sm btn-primary float-end me-2 tbh_pakan"><i class="fas fa-plus"></i></a>
                <a href="#" onclick="event.preventDefault();" data-bs-toggle="modal"
                    data-bs-target="#history_pakvit" class="btn btn-sm btn-primary float-end me-2 history_pakvit"
                    jenis="pakan">History</a>
            </div>
        </div>
        <table class="table table-bordered table-hover" id="tablePakan">
            <thead>
                <tr>
                    <th>#</th>
                    <th class="dhead">Nama Pakan</th>
                    <th class="dhead" style="text-align: right">Stok</th>
                    <th class="dhead" style="text-align: center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pakan as $no => $p)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td><a href="#" onclick="event.preventDefault();" class="history_stok"
                                id_pakan="{{ $p->id_pakan }}">{{ ucwords(strtolower($p->nm_produk)) }}
                            </a>
                        </td>
                        <td style=" text-align: right">
                            {{ number_format($p->pcs_debit - $p->pcs_kredit, 0) }} <br>
                            {{ number_format(($p->pcs_debit - $p->pcs_kredit) / 50000, 0) }}
                        </td>
                        <td style="text-align: center">{{ $p->nm_satuan }} <br> Sak</td>
                    </tr>
                @endforeach
            </tbody>
        </table>


    </div>
    <div class="col-lg-4">
        <div class="row mb-2">

            <div class="col-lg-3">
                <h6>Stok Vitamin</h6>
            </div>
            <div class="col-lg-3">
                <input id="pencarianVitamin" placeholder="Pencarian" type="text" class="form-control">
            </div>
            <div class="col-lg-6">
                <button data-bs-toggle="tooltip" data-bs-placement="top" title="Opname" type="button"
                    class="btn btn-primary btn-sm float-end opnme_vitamin">Opname
                </button>
                <a href="#" onclick="event.preventDefault();" data-bs-toggle="modal" data-bs-target="#tbh_pakan"
                    class="btn btn-sm btn-primary float-end me-2 tbh_vitamin"><i class="fas fa-plus"></i></a>
                <a href="#" onclick="event.preventDefault();" data-bs-toggle="modal"
                    data-bs-target="#history_pakvit" class="btn btn-sm btn-primary float-end me-2 history_pakvit"
                    jenis="vitamin">History</a>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="tableVitamin">
            <thead>
                <tr>
                    <th class="dhead">#</th>
                    <th class="dhead">Nama Vitamin</th>
                    <th class="dhead" style="text-align: right">Stok</th>
                    <th class="dhead" style="text-align: center">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vitamin as $no => $p)
                    <tr>
                        <td>{{ $no + 1 }}</td>
                        <td><a href="#" onclick="event.preventDefault();" class="history_stok"
                                id_pakan="{{ $p->id_pakan }}">{{ $p->nm_produk }}</a></td>
                        <td style="text-align: right">{{ number_format($p->pcs_debit - $p->pcs_kredit, 0) }}</td>
                        <td style="text-align: center">{{ $p->nm_satuan }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{-- <div class="col-lg-4">
        <div class="row mb-2">

            <div class="col-lg-3">
                <h6>Stok Vaksin</h6>
            </div>
            <div class="col-lg-5 float-end">
                <input id="pencarianVaksin" placeholder="Pencarian" type="text" class="form-control">
            </div>
            <div class="col-lg-4">
                <a href="#" onclick="event.preventDefault();" data-bs-toggle="modal" data-bs-target="#tbh_vaksin"
                    class="btn btn-sm btn-primary float-end me-2"><i class="fas fa-plus"></i></a>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="tableVaksin">
            <thead>
                <tr>
                    <th class="dhead">Tgl</th>
                    <th class="dhead">Kdg</th>
                    <th class="dhead">Nama Vaksin</th>
                    <th class="dhead" style="text-align: right">Stok</th>
                    <th class="dhead" style="text-align: center">Total Rp</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($vaksin as $d)
                <tr>
                    <td>{{ tanggal($d->tgl) }}</td>
                    <td>{{ $d->nm_kandang }}</td>
                    <td>{{ $d->nm_vaksin }}</td>
                    <td>{{ $d->qty }}</td>
                    <td>{{ number_format($d->ttl_rp, 0) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div> --}}
</div>
