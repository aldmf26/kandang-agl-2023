<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Type</label>
            <select name="id_pakan[]" id="" persen="1" count="1"
                class="form-control select2-edit persen_pakan pakan_input">
                <option value="">- Pilih Pakan -</option>
                @foreach ($pakan as $p)
                <option value="{{ $p->id_produk }}">{{ $p->nm_produk }}</option>
                @endforeach
                <option value="tambah">+ Pakan Baru</option>
            </select>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Stok</label>
            <input type="text" name="stok[]" readonly class="form-control get_stok_pakan1">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">%</label>
            <input type="text" id="prsn1" name="persen_pakan[]" class="form-control pakan_input persen" kd="1">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Pakan (Gr)</label>
            <input type="text" readonly name="gr_pakan[]" id="hasil1" class="form-control hasil">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Aksi</label><br>
            <button class="btn btn-primary btn-md pakan_input tbhPakan" type="button"><i
                    class="fa fa-plus"></i></button>
        </div>
    </div>
</div>
<div id="tbhPakan"></div>
<div class="row">
    <div class="col-lg-3">
    </div>
    <div class="col-lg-2">
    </div>
    <div class="col-lg-3">
        <hr style="border: 1px solid #6777EF;">
        <input type="text" readonly name="pakan_gr_total" id="total" class="form-control">
    </div>
</div>