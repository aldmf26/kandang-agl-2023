<div class="row">
    <div class="col-lg-4">
        <label for="">Tanggal</label>
        <input required type="date" name="tgl" value="{{ $tgl }}" class="form-control tgl1">
    </div>
    <div class="col-lg-4">
        <label for="">Kandang</label>
        <input type="hidden" readonly name="id_kandang" value="{{ $id_kandang }}" class="form-control">
        <select disabled required id="kandangPerencanaan" class="form-control select2-edit-perencanaan id_populasi2">
            <option value="">- Pilih Kandang -</option>
            @foreach ($kandang as $k)
                <option {{$k->id_kandang == $id_kandang ? 'selected' : ''}} value="{{ $k->id_kandang }}">{{ $k->nm_kandang }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-lg-3">
        <label for="">Kg pakan/box</label>
        <input type="text" id="krngEdit" name="kg_pakan_box" class="form-control pakan_input" value="{{ $karung->karung }}">
        <input type="hidden" class="form-control pakan_input" name="id_krng" value="<?= $karung->id_karung ?>">
        <input type="hidden" name="id_no_nota" value="{{ $karung->no_nota }}">
    </div>
</div>
<input type="hidden" name="no_nota" value="{{$gr_pakan->no_nota}}">
<hr style="border: 1px solid #6777EF;">
<h5 style="text-decoration: underline">Pakan</h5>
{{-- pakan --}}
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Populasi </label>
            <input value="{{ $populasi }}" type="text" id="getPopulasiEdit" readonly name="populasi" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Gr Pakan / Ekor {{$gr_pakan->ttl}}</label>
            <input value="{{ number_format(($gr_pakan->ttl / $populasi), 0) }}" type="text" id="grEdit" name="gr_pakan_ekor" class="form-control pakan_input">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Kg/karung</label>
            <input value="{{ $karung->gr }}" type="text" id="krng_fEdit" readonly name="kg_karung" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Kg/karung sisa</label>
            <input value="{{ $karung->gr2 }}" type="text" readonly id="krng_sEdit" name="kg_karung_sisa" class="form-control">
        </div>
    </div>
</div>
@php
    $i = 1;
    $l = 1;
@endphp
@foreach ($pakan_id as $pk)
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <label for="">Type</label>
                <select name="id_pakan[]"  persen="1" class="form-control select2-edit-perencanaan persen_pakan pakan_input">
                    <option value="">- Pilih Pakan -</option>
                    @foreach ($pakan as $p)
                        <option {{ $p->id_produk == $pk->id_produk ? 'selected' : '' }} value="{{ $p->id_produk }}">{{ $p->nm_produk }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="">%</label>
                <input type="text" id="prsn1" name="persen_pakan[]" class="form-control pakan_input persenEdit" kd="{{ $i++ }}" value="{{ !$gr_pakan->ttl  ? 0 : ($pk->gr_pakan * 100) / $gr_pakan->ttl }}">
            </div>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <label for="">Pakan (Gr)</label>
                <input type="text" readonly name="gr_pakan[]" id="hasilEdit{{$l++}}" value="{{ $pk->gr_pakan }}" class="form-control hasilEdit">
            </div>
        </div>
    </div>
@endforeach
<div class="row">
    <div class="col-lg-3">
    </div>
    <div class="col-lg-2">
    </div>
    <div class="col-lg-3">
        <hr style="border: 1px solid #6777EF;">
        <input value="{{ $gr_pakan->ttl }}" type="text" readonly name="pakanGrTotal" id="totalEdit" class="form-control">
    </div>
</div>

<h5 style="text-decoration: underline">Obat/vit dengan campuran pakan</h5>
@foreach ($obat_pakan as $obt)
    <div class="row">
        <div class="col-lg-3">
            <div class="form-group">
                <label for="">Obat</label>
                <input type="hidden" name="id_obat_pkn[]" value="{{ $obt->id_produk }}">
                <select name="id_obat_pakan[]" id="" class="form-control select2-edit-perencanaan id_obat_pkn" detail="1">
                    <option value="">- Pilih Obat -</option>
                    @foreach ($obat as $o)
                        <option {{ $obt->id_produk == $o->id_produk ? 'selected' : '' }} value="{{ $o->id_produk }}">{{ $o->nm_produk }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="">Dosis</label>
                <input value="{{ $obt->dosis }}" type="text" class="form-control" id="ds1" name="dosis_obat_pakan[]">
            </div>
        </div>
        <div class="col-lg-1">
            <div class="form-group">
                <label for="">Satuan</label>
                <input value="{{ $obt->satuan }}" type="text" readonly name="satuan_obat_pakan[]" id="stn1" class="form-control">
            </div>
        </div>
        <div class="col-lg-2">
            <div class="form-group">
                <label for="">Campuran</label>
                <input value="{{ $obt->campuran }}" type="text" class="form-control" id="cmpr1" name="campuran_obat_pakan[]">
            </div>
        </div>
        <div class="col-lg-1">
            <div class="form-group">
                <label for="">Satuan</label>
                <input value="{{ $obt->satuan2 }}" type="text" id="stnc1" readonly name="satuanObat2[]" class="form-control">
            </div>
        </div>
    </div>
@endforeach
<div id="loadTbhObatPakanEdit"></div>
<button class="btn btn-primary btn-sm tbhObatPakanEdit mb-3" type="button"><i
    class="fa fa-plus"></i>Tambah Obat Pakan</button>
<h5 style="text-decoration: underline">Obat/vit dengan campuran air</h5>
@foreach ($obat_air as $oba)
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Obat</label>
            <input type="hidden" name="id_obat_air[]" value="{{ $oba->id_produk }}">
            <select name="id_obatAir[]" id="" detail="1" class="form-control select2-edit-perencanaan id_obat_air">
                <option value="">- Pilih Obat -</option>
                @foreach ($obat_air2 as $o)
                    <option {{ $o->id_produk == $oba->id_produk ? 'selected' : '' }} value="{{ $o->id_produk }}">{{ $o->nm_produk }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Dosis</label>
            <input type="text" value="{{ $oba->dosis }}" class="form-control" name="dosis_obat_air[]">
        </div>
    </div>
    <div class="col-lg-1">
        <div class="form-group">
            <label for="">Satuan</label>
            <input type="text" id="stnAir1" value="{{ $oba->satuan }}" readonly name="satuanObatAir[]" class="form-control">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Campuran</label>
            <input type="text" class="form-control" value="{{ $oba->campuran }}" name="campuran_obat_air[]">
        </div>
    </div>
    <div class="col-lg-1">
        <div class="form-group">
            <label for="">Satuan</label>
            <input type="text" id="stncAir1" value="{{ $oba->satuan2 }}" readonly name="satuanObatAir2[]" class="form-control">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Waktu</label><br>
            <input type="time" name="waktu_obat_air[]" value="{{ $oba->waktu }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Cara Pemakaian</label>
            <input type="text" name="cara_pemakaian_obat_air[]" value="{{ $oba->cara }}" class="form-control">
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Keterangan</label>
            <input type="text" name="ket_obat_air[]" value="{{ $oba->ket }}" class="form-control">
        </div>
    </div>
</div>
@endforeach
<div id="loadTbhObatAirEdit"></div>
<button class="btn btn-primary btn-sm tbhObatAirEdit mb-3" type="button"><i
    class="fa fa-plus"></i>Tambah Obat Air</button>
<h5 style="text-decoration: underline">Obat/ekor ayam</h5>
@foreach ($obat_aym as $tot)    
<div class="row">
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Obat</label>
            <input type="hidden" name="id_obat_ayam" value="{{ $tot->id_obat_ayam }}">
            <select name="id_obat_ayam" id="obatAyam" class="form-control select2-edit-perencanaan">
                <option value="">- Pilih Obat -</option>
                @foreach ($obat_ayam as $o)
                    <option {{ $o->id_barang == $tot->id_obat ? 'selected' : '' }} value="{{ $o->id_barang }}">{{ $o->nm_barang }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-lg-3">
        <div class="form-group">
            <label for="">Dosis</label>
            <input type="text" value="{{ $tot->dosis_awal }}" class="form-control" id="ds1" name="dosis_obat_ayam">
        </div>
    </div>
    <div class="col-lg-2">
        <div class="form-group">
            <label for="">Satuan</label>
            <input type="text" value="{{ $tot->satuan }}" id="satuanObatAyam" readonly class="form-control" id="stn1" name="obatAyamSatuan">
        </div>
    </div>
</div>
@endforeach