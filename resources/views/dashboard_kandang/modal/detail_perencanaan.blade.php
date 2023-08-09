@php
    $listKandang = DB::table('kandang')->get();
@endphp
<div class="collapse multi-collapse" id="perencanaan">
    <div class="row">
        <div class="col-lg-3">
            <label for="">Tanggal</label>
            <input value="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day')) }}" type="date" name="tglPerencanaan"
                id="tglHistoryPerencanaan" class="form-control">
        </div>
        <div class="col-lg-3">
            <label for="">Kandang</label>

            <select class="form-control" name="id_kandangPerencanaan" id="id_kandangPerencanaan">
                @foreach ($listKandang as $d)
                    <option value="{{ $d->id_kandang }}" {{ $kandang->nm_kandang == $d->nm_kandang ? 'selected' : '' }}>
                        {{ $d->nm_kandang }}
                    </option>
                @endforeach
            </select>

        </div>
        <div class="col-lg-2">
            <label for="">Aksi</label><br>
            <button type="button" class="btn btn-md btn-primary" id="btnPerencanaan">View</button>
        </div>
    </div>
    <div id="hasilPerencanaan" class="mt-3"></div>
    <br>
</div>

<div class="collapse multi-collapse" id="layer">
    <div class="row">
        <div class="col-lg-3">
            <label for="">Tanggal</label>
            <input type="date" id="tglLayer" class="form-control">
        </div>
        <div class="col-lg-2">
            <label for="">Aksi</label><br>
            <button type="button" class="btn btn-md btn-primary" id="btnLayer">View</button>
        </div>
    </div>
    <div id="hasilLayer" class="mt-3"></div>

    <br>
</div>

<div class="collapse multi-collapse" id="inputTelur">
    <div class="row">
        <div class="col-lg-3">
            <label for="">Dari</label>
            <input type="date" value="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' -1 day')) }}" name="tglDariInputTelur" id="tglDariInputTelur" class="form-control">
        </div>
        <div class="col-lg-3">
            <label for="">Kandang</label>

            <select class="form-control" name="id_kandangPerencanaan" id="id_kandangPerencanaan">
                @foreach ($listKandang as $d)
                    <option value="{{ $d->id_kandang }}" {{ $kandang->nm_kandang == $d->nm_kandang ? 'selected' : '' }}>
                        {{ $d->nm_kandang }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2">
            <label for="">Aksi</label><br>
            <button type="button" class="btn btn-md btn-primary" id="btnInputTelur">View</button>
        </div>
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
