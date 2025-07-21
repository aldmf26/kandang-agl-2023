<tr class="baris{{ $count }}">
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
        <input type="text" class="form-control pcs pcs{{ $count }}" count="{{ $count }}"
            style="text-align: right" required>
        <input type="hidden" class="form-control  pcs_biasa{{ $count }}" value="0" name="pcs[]">
    </td>
    <td align="right">
        <input type="text" class="form-control kg kg{{ $count }}" count="{{ $count }}"
            style="text-align: right" required>
        <input type="hidden" class="form-control kgbiasa kgbiasa{{ $count }}" count="{{ $count }}"
            value="0" name="kg[]">
    </td>
    <td align="right">
        <input type="text" class="form-control ikat{{ $count }}" value="0" name="ikat[]">
    </td>
    <td align="right">
        <input type="text" class="form-control kg_jual kgminrakbiasa{{ $count }}" value="0"
            name="kg_jual[]" count="{{ $count }}">
    </td>
    <td align="right">
        <input type="text" class="form-control rp_satuan rp_satuan{{ $count }}" count="{{ $count }}"
            style="text-align: right" required>
        <input type="hidden" class="form-control rp_satuanbiasa{{ $count }}" value="0" name="rp_satuan[]">
        <input type="hidden" class="form-control ttl_rpbiasa ttl_rpbiasa{{ $count }}" value="0"
            name="total_rp[]">
    </td>
    <td>
        <select name="tipe[]" class="form-control changetipe tipe{{ $count }}" required
            count="{{ $count }}">
            <option value="">Pilih</option>
            <option value="kg">Kg</option>
            <option value="pcs">Pcs</option>
        </select>
    </td>
    <td align="right" class="ttl_rp{{ $count }}"></td>

    <td style="vertical-align: top;">
        <button type="button" class="btn rounded-pill remove_baris_kg" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
