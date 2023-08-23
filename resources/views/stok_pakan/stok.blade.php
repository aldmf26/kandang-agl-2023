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
            <div class="col-lg-6">
                <input id="pencarianPakan" placeholder="Pencarian" type="text" class="form-control">
            </div>
            <div class="col-lg-3">
                <div class="btn-group dropup me-1 mb-2 float-end">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i> Aksi
                    </button>
                    <div class="dropdown-menu bg-primary" style="">
                        <h6 class="dropdown-header text-white">Data Aksi</h6>

                        <a data-bs-toggle="modal" data-bs-target="#tbh_pakan"
                            class="text-white tbh_pakan dropdown-item hoverbtn" href="#"> Tambah Pakan</a>

                        <a onclick="event.preventDefault();" class="text-white opnme_pakan dropdown-item hoverbtn"
                            href="#">Opname</a>
                        <a onclick="event.preventDefault();" data-bs-toggle="modal" data-bs-target="#history_pakvit"
                            class="text-white history_pakvit dropdown-item hoverbtn" href="#" jenis="pakan">History Masuk Keluar</a>
                        <a onclick="event.preventDefault();" data-bs-toggle="modal" data-bs-target="#history_pakvit"
                            class="text-white history_pakvit dropdown-item hoverbtn" href="#"
                            jenis="pakan_opname">History Opname</a>


                    </div>

                </div>
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
            <div class="col-lg-6">
                <input id="pencarianVitamin" placeholder="Pencarian" type="text" class="form-control">
            </div>
            <div class="col-lg-3">
                <div class="btn-group dropup me-1 mb-2 float-end">
                    <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v"></i> Aksi
                    </button>
                    <div class="dropdown-menu bg-primary" style="">
                        <h6 class="dropdown-header text-white">Data Aksi</h6>

                        <a data-bs-toggle="modal" data-bs-target="#tbh_pakan"
                            class="text-white tbh_vitamin dropdown-item hoverbtn" href="#"> Tambah Pakan</a>
                        <a onclick="event.preventDefault();" class="text-white opnme_vitamin dropdown-item hoverbtn"
                            href="#">Opname</a>
                        <a onclick="event.preventDefault();" data-bs-toggle="modal" data-bs-target="#history_pakvit"
                            class="text-white history_pakvit dropdown-item hoverbtn" href="#"
                            jenis="vitamin">History Masuk Keluar</a>
                            <a onclick="event.preventDefault();" data-bs-toggle="modal" data-bs-target="#history_pakvit"
                            class="text-white history_pakvit dropdown-item hoverbtn" href="#"
                            jenis="vitamin_opname">History Opname</a>
                    </div>
                </div>
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

</div>
