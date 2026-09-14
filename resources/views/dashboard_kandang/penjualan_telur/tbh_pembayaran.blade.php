<div class="row baris_bayar{{$count}}">
    <div class="col-lg-5 mt-2">
        <select name="id_akun_perkiraan[]" id="" class="select">
            <option value="">-Pilih Akun-</option>
            @foreach ($akun_perkiraan as $a)
                <option value="{{ $a->id_akun_perkiraan }}">{{ $a->nama }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-3 mt-2">
        <input type="text" class="form-control debit debit{{$count}}" count="{{$count}}" style="text-align: right">
        <input type="hidden" name="debit[]" class="form-control debit_biasa debit_biasa{{$count}}" value="0">
    </div>
    <div class="col-lg-3 mt-2">
        <input type="text" class="form-control kredit kredit{{$count}}" count="{{$count}}" style="text-align: right">
        <input type="hidden" name="kredit[]" class="form-control kredit_biasa kredit_biasa{{$count}}" value="0">
    </div>
    <div class="col-lg-1 mt-2">
        <button type="button" class="btn rounded-pill delete_pembayaran" count="{{$count}}">
            <i class="fas fa-trash text-danger"></i>
        </button>
    </div>
</div>