<form action="{{ route('data_kandang.store') }}" method="post">
    @csrf
    <input type="hidden" name="route" value="dashboard_kandang.index">
    <x-theme.modal title="Tambah Kandang" idModal="tambah_kandang">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Tanggal Chick in</label>
                    <input required value="{{ date('Y-m-d') }}" type="date" name="tgl_lahir" class="form-control">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Tanggal Chick in2</label>
                    <input required value="{{ date('Y-m-d') }}" type="date" name="tgl_masuk" class="form-control">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Tanggal Afkir</label>
                    <input required type="date" name="chick_out" class="form-control">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Nama Kandang</label>
                    <input required type="text" name="nm_kandang" class="form-control">
                </div>
            </div>
            <div class="col-lg-6">
                <label for="">Strain</label>
                <select name="strain" class="form-control select2-kandang" id="">
                    <option value="">- Pilih Strain -</option>
                    @php
                        $strain = DB::table('strain')->get();
                    @endphp
                    @foreach ($strain as $d)
                        <option value="{{ $d->id_strain }}">{{ $d->nm_strain }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Ayam Awal</label>
                    <input required type="text" name="ayam_awal" class="form-control">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Rupiah</label>
                    <input required type="text" name="rupiah" class="form-control">
                </div>
            </div>

        </div>
    </x-theme.modal>
</form>
