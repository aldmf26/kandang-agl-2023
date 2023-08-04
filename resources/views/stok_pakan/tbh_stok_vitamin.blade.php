<div class="row">
    <div class="col-lg-4 mb-2">
        <label for="">Tanggal</label>
        <input type="date" value="{{ date('Y-m-d') }}" name="tgl" class="form-control" id="">
        <input type="hidden" name="kategori" value="{{$kategori}}">
    </div>
    <div class="col-lg-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead" width="25%">Produk</th>
                    <th class="dhead" width="15%">Qty</th>
                    <th class="dhead" width="15%">Satuan</th>
                    <th class="dhead" width="25%">Total Rp</th>
                    <th class="dhead" width="25%">Biaya lain-lain & ongkir</th>
                    <th class="dhead" width="5%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr class="baris1">
                    <td>
                        <select name="id_pakan[]" id="" class="select2tbhPakan form-control id_vitamin id_vitamin1"
                            count='1'>
                            <option value="">- Pilih Produk -</option>
                            @foreach ($produk as $p)
                            <option value="{{$p->id_produk}}">{{$p->nm_produk}}</option>
                            @endforeach
                            <option value="tambah">+ Produk Baru</option>
                        </select>
                    </td>
                    <td><input type="text" name="pcs[]" class="form-control"></td>
                    <td class="satuan_vitamin1"></td>
                    <td><input type="text" name="ttl_rp[]" class="form-control"></td>
                    <td><input type="text" name="biaya_dll[]" class="form-control"></td>
                    <td>
                        <button type="button" class="btn rounded-pill remove_baris" count="1"><i
                                class="fas fa-trash text-danger"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
            <tbody id="tb_baris_produk">

            </tbody>
        </table>
    </div>

    <div class="col-lg-12">
        <button type="button" class="btn btn-block btn-lg {{$kategori == 'pakan' ? 'tbh_baris' : 'tbh_baris_vitamin'}} "
            style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
            <i class="fas fa-plus"></i> Tambah Baris Baru
        </button>
    </div>


</div>