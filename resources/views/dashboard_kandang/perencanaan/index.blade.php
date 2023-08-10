<div class="row">
    <div class="col-lg-3">
        <label for="">Tanggal</label>
        <input value="{{ date('Y-m-d') }}" required type="date" name="tgl" id="tglPerencanaan"
            class="form-control">
    </div>
    <div class="col-lg-2">
        <label for="">Kandang</label>
        <input id="id_kandang" type="hidden" name="id_kandang" value="{{ $kandang->id_kandang }}">
        <input readonly type="text" class="form-control" value="{{ $kandang->nm_kandang }}">

    </div>
    <div class="col-lg-3">
        <label for="">Kg pakan/box</label>
        <input type="text" id="krng" name="kg_pakan_box" class="form-control kg_pakan_box">
    </div>
</div>

<hr style="border: 1px solid #6777EF;">
<h5 style="text-decoration: underline">Pakan</h5>

<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Populasi</label>
            <input type="text" id="getPopulasi" readonly name="populasi" class="form-control"
                value="{{ $pop->stok_awal - $pop->pop }}">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Gr Pakan / Ekor</label>
            <input type="text" id="gr" name="gr_pakan_ekor" class="form-control gr_pakan_error">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Kg/karung</label>
            <input type="text" id="krng_f" readonly name="kg_karung" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Kg/karung sisa</label>
            <input type="text" readonly id="krng_s" name="kg_karung_sisa" class="form-control">
        </div>
    </div>
</div>
<div id="load_pakan_perencanaan"></div>

<h5 style="text-decoration: underline">Obat/vit dengan campuran pakan</h5>
<div id="load_obat_pakan"></div>

<h5 style="text-decoration: underline">Obat/vit dengan campuran air</h5>
<div id="load_obat_air"></div>

<h5 style="text-decoration: underline">Obat/ekor ayam</h5>
<div id="load_obat_ayam"></div>