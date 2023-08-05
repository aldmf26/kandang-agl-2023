<div class="collapse multi-collapse show" id="perencanaan">
    <div class="row">
        <div class="col-lg-3">
            <label for="">Tanggal</label>
            <input value="{{ date('Y-m-d', strtotime(date('Y-m-d') . ' +1 day')) }}" type="date" name="tglPerencanaan"
                id="tglHistoryPerencanaan" class="form-control">
        </div>
        <div class="col-lg-3">
            <label for="">Kandang</label>
            <input type="text" readonly value="{{ $kandang->nm_kandang }}" class="form-control">
            <input type="hidden" id="id_kandangPerencanaan" readonly value="{{ $kandang->id_kandang }}"
                class="form-control" name="id_kandangPerencanaan">
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

<div class="collapse multi-collapse" id="pullet">
    <div class="row">
        <div class="col-lg-3">
            <label for="">Kandang</label>
            <input type="text" readonly value="{{ $kandang->nm_kandang }}" class="form-control"
                name="id_kandangPullet">
        </div>
        <div class="col-lg-3">
            <label for="">Dari</label>
            <input type="date" name="tglDariPullet" id="tglDariPullet" class="form-control">
        </div>
        <div class="col-lg-3">
            <label for="">Sampai</label>
            <input type="date" name="tglSampaiPullet" id="tglSampaiPullet" class="form-control">
        </div>
        <div class="col-lg-2">
            <label for="">Aksi</label><br>
            <button type="button" class="btn btn-md btn-primary" id="btnPullet">View</button>
        </div>
    </div>
    <div id="hasilPullet" class="mt-3"></div>
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
