<table class="table table-striped ">
    <thead>
        <tr>
            <th class="dhead" width="2%">#</th>
            <th class="dhead" width="10%">Produk</th>
            <th class="dhead" width="10%" style="text-align: right">Pcs</th>
            <th class="dhead" width="10%" style="text-align: right">Kg</th>
            <th class="dhead" width="10%" style="text-align: right">Rp Satuan</th>
            <th class="dhead" width="10%" style="text-align: right">Total Rp</th>
            <th class="dhead" width="5%">Aksi</th>
        </tr>
    </thead>
    <tbody>
        <tr class="baris1">
            <td></td>
            <td>
                <select name="id_produk[]" class="select" required>
                    <option value="">-Pilih Produk-</option>
                    @foreach ($produk as $p)
                        <option value="{{ $p->id_produk_telur }}">{{ $p->nm_telur }}</option>
                    @endforeach
                </select>
            </td>
            <td align="right">
                <input type="text" class="form-control tipe_pcs tipe_pcs1" count="1" style="text-align: right"
                    required>
                <input type="hidden" class="form-control  tipe_pcs_biasa1" name="pcs[]" value="0">
            </td>
            <td align="right">
                <input type="text" class="form-control tipe_kg tipe_kg1" count="1" style="text-align: right"
                    required>
                <input type="hidden" class="form-control tipe_kgbiasa tipe_kgbiasa1" name="kg[]" count="1"
                    value="0">
            </td>
            <td align="right">
                <input type="text" class="form-control tipe_rp_satuan tipe_rp_satuan1" count="1"
                    style="text-align: right" required>
                <input type="hidden" class="form-control tipe_rp_satuanbiasa1" name="rp_satuan[]" value="0">
                <input type="hidden" class="form-control ttl_rpbiasa tipe_ttl_rpbiasa1" name="total_rp[]"
                    value="0">
            </td>
            <td align="right" class="tipe_ttl_rp1"></td>
            <td style="vertical-align: top;">
                <button type="button" class="btn rounded-pill remove_baris_pcs" count="1"><i
                        class="fas fa-trash text-danger"></i>
                </button>
            </td>
        </tr>


    </tbody>
    <tbody id="tb_baris_pcs">

    </tbody>
    <tbody>
        <tr>
            <td></td>
            <td>Rak</td>
            <td><input type="text" class="form-control pcs_rak text-end" name="pcs_rak" value="0"></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>


        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="9">
                <button type="button" class="btn btn-block btn-lg tbh_baris_pcs"
                    style="background-color: #F4F7F9; color: #8FA8BD; font-size: 14px; padding: 13px;">
                    <i class="fas fa-plus"></i> Tambah Baris Baru

                </button>
            </th>
        </tr>
    </tfoot>


</table>
