<div class="row">
    <input type="hidden" name="id_kandang" value="{{ $d->id_kandang }}">
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Tanggal Chick in</label>
            <input required value="{{ $d->chick_in }}" type="date" name="tgl_lahir" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Tanggal Chick in2</label>
            <input required value="{{ $d->tgl_masuk }}" type="date" name="tgl_masuk" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Tanggal Afkir</label>
            <input required type="date" name="chick_out" class="form-control" value="{{ $d->chick_out }}">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Nama Kandang</label>
            <input required type="text" value="{{ $d->nm_kandang }}" name="nm_kandang" class="form-control">
        </div>
    </div>
    <div class="col-lg-6">
        <label for="">Strain</label>
        <select name="strain" class="form-control select2-edit" id="">
            <option value="">- Pilih Strain -</option>
            @foreach ($strain as $s)
            <option value="{{ $s->id_strain }}" {{$s->id_strain == $d->id_strain ? 'Selected' : ''}}>{{ $s->nm_strain }}
            </option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-6">
        <div class="form-group">
            <label for="">Ayam Awal</label>
            <input required type="text" value="{{ $d->stok_awal }}" name="ayam_awal" class="form-control">
        </div>
    </div>

</div>