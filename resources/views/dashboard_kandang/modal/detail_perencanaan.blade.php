@php
    $listKandang = DB::table('kandang')->get();
@endphp
<div class="collapse multi-collapse" id="perencanaan">
    <h6>History Perencanaan</h6>
    <div class="col-lg-4">
        <table class="table">
            <tr>
                <th width="10%" class="dhead">Tanggal</th>
                <th class="dhead">Kandang</th>
                <th class="dhead">Aksi</th>
            </tr>
            <tr>
                <td>
                    <input value="{{ date('Y-m-d') }}" type="date"
                        name="tglPerencanaan" id="tglHistoryPerencanaan" class="form-control">
                </td>
                <td>
                    <select class="form-control" name="id_kandangPerencanaan" id="id_kandangPerencanaan">
                        @foreach ($listKandang as $d)
                            <option value="{{ $d->id_kandang }}"
                                {{ $kandang->nm_kandang == $d->nm_kandang ? 'selected' : '' }}>
                                {{ $d->nm_kandang }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-block btn-md btn-primary" id="btnPerencanaan">View</button>

                </td>
            </tr>
        </table>
    </div>

    <div id="hasilPerencanaan" class="mt-3"></div>
    <br>
</div>

<div class="collapse multi-collapse" id="layer">
    <h6>History Layer</h6>

    <div class="col-lg-3">
        <table class="table">
            <tr>
                <th width="10%" class="dhead">Tanggal</th>
                <th class="dhead">Aksi</th>
            </tr>
            <tr>
                <td>
                    <input type="date" value="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day')) }}" id="tglLayer" class="form-control">
                </td>

                <td>
                    <button type="button" class="btn btn-block btn-md btn-primary" id="btnLayer">View</button>

                </td>
            </tr>
        </table>
    </div>
    
    <div id="hasilLayer" class="mt-3"></div>

    <br>
</div>

<div class="collapse multi-collapse" id="inputTelur">
    <h6>History Input Telur</h6>
    <div class="col-lg-4">
        <table class="table">
            <tr>
                <th width="10%" class="dhead">Tanggal</th>
                <th class="dhead">Kandang</th>
                <th class="dhead">Aksi</th>
            </tr>
            <tr>
                <td>
                    <input type="date" value="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day')) }}"
                name="tglDariInputTelur" id="tglDariInputTelur" class="form-control">
                </td>
                <td>
                    <select class="form-control" name="id_kandangInputTelur" id="id_kandangInputTelur">
                        @foreach ($listKandang as $d)
                            <option value="{{ $d->id_kandang }}" {{ $kandang->nm_kandang == $d->nm_kandang ? 'selected' : '' }}>
                                {{ $d->nm_kandang }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-block btn-md btn-primary" id="btnInputTelur">View</button>

                </td>
            </tr>
        </table>
    </div>

    
    <div id="hasilInputTelur" class="mt-3"></div>
    <br>
</div>

<div class="collapse multi-collapse" id="stok">
    <div class="row">
        <div class="col-lg-3">
            <label for="">Tanggal</label>
            <input type="date" id="tglStok" class="form-control">
        </div>
        <div class="col-lg-2">
            <label for="">Aksi</label><br>
            <button type="button" class="btn btn-md btn-primary" id="btnStok">View</button>
        </div>
    </div>
    <div id="hasilStok" class="mt-3"></div>
</div>
</div>
