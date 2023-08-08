<div class="row baris{{$count}}">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Type</label>
            <select name="id_obat_air[]" id="" persen="1"
                count="{{ $count }}" class="form-control select2-pakan persen_pakan obat_air_input">
                <option value="">- Pilih Obat -</option>
                @foreach ($pakan as $p)
                    <option value="{{ $p->id_produk }}">{{ $p->nm_produk }}</option>
                @endforeach
                <option value="tambah">+ Obat Baru</option>
            </select>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Dosis</label>
            <input type="text" name="dosis_obat_air[]" class="form-control">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Satuan</label>
            <input type="text" readonly name="satuan_obat_air[]" id="stn1" class="form-control get_dosis_satuan_air{{$count}}">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Campuran</label>
            <input type="text" class="form-control" id="cmpr1" name="campuran_obat_air[]">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Satuan</label>
            <input type="text" id="stnc1" readonly name="satuan[]" class="form-control get_campuran_satuan_air{{$count}}">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Waktu</label><br>
            <input type="time" name="waktu_obat_air[]" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Cara Pemakaian</label>
            <input type="text" name="cara_pemakaian_obat_air[]" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Keterangan</label>
            <input type="text" name="ket_obat_air[]" class="form-control">
        </div>
    </div>
    <div class="col-lg-1">  
        <div class="form-group">
            <label for="">Aksi</label><br>
            <button count="{{$count}}" class="remove_baris btn btn-danger btn-md" type="button"><i
                    class="fa fa-minus"></i></button>
        </div>
    </div>
</div>