<div class="row baris{{$count}} barisAll">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Type</label>
            <select name="id_pakan[]" id="" persen="{{$count}}"
                count="{{ $count }}" class="form-control select2-pakan persen_pakan pakan_input">
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
            <input type="text" name="stok[]" readonly class="form-control get_stok_pakan{{$count}}">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">%</label>
            <input type="text" id="prsn{{$count}}" name="persen_pakan[]"
                class="form-control pakan_input persen" kd="{{$count}}">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Pakan (Gr)</label>
            <input type="text" readonly name="gr_pakan[]" id="hasil{{$count}}"
                class="form-control hasil">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Aksi</label><br>
            <button count="{{$count}}" class="remove_baris btn btn-danger btn-md" type="button"><i
                    class="fa fa-minus"></i></button>
        </div>
    </div>
</div>