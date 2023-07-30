<tr class="baris{{$count}}">
    <td>
        <select name="id_produk[]" class="select" required>
            <option value="">-Pilih Produk-</option>
            @foreach ($produk as $p)
            <option value="{{$p->id_produk_telur}}">{{$p->nm_telur}}</option>
            @endforeach
        </select>
    </td>
    <td align="right">
        <input type="text" class="form-control pcs pcs{{$count}}" count="{{$count}}"
            style="text-align: right; font-size: 12px;">
        <input type="hidden" class="form-control  pcs_biasa{{$count}}" name="pcs_pcs[]" value="0">
    </td>
    <td align="right">
        <input type="text" class="form-control kg_pcs kg_pcs{{$count}}" count="{{$count}}"
            style="text-align: right; font-size: 12px;">
        <input type="hidden" class="form-control  kg_pcs_biasa{{$count}}" name="kg_pcs[]" value="0">
    </td>
    {{-- <td align="right">
        <input type="text" class="form-control rp_pcs pcs{{$count}}" count="{{$count}}"
            style="text-align: right;font-size: 12px;">
        <input type="hidden" class="form-control rp_pcs_biasa rp_pcs_biasa{{$count}}" name="rp_pcs[]" value="0">
        <input type="hidden" class="ttl_rp_pcs{{$count}}" value="0">
    </td> --}}
    <!-- Jual Ikat -->
    <td align="right">
        <input type="text" class="form-control ikat ikat{{$count}}" count="{{$count}}"
            style="text-align: right;font-size: 12px;" value="0" name="ikat[]">
    </td>
    <td align="right">
        <input type="text" class="form-control kg_ikat kg_ikat{{$count}}" count="{{$count}}"
            style="text-align: right;font-size: 12px;" value="0" name="kg_ikat[]">
    </td>
    {{-- <td align="right">
        <input type="text" class="form-control rp_ikat rp_ikat{{$count}}" count="{{$count}}"
            style="text-align: right;font-size: 12px;">
        <input type="hidden" class="form-control  rp_ikat_biasa{{$count}}" name="rp_ikat[]" value="0">
        <input type="hidden" class="ttl_rp_ikat{{$count}}" value="0">
    </td> --}}
    <!-- Jual Ikat -->
    <!-- Jual Kg -->
    <td align="right">
        <input type="text" class="form-control" name="pcs_kg[]" count="{{$count}}"
            style="text-align: right;font-size: 12px;" value="0">
    </td>
    <td align="right">
        <input type="text" class="form-control kg_kg kg_kg{{$count}}" count="{{$count}}"
            style="text-align: right;font-size: 12px;" value="0" name="kg_kg[]">
    </td>
    <td align="right">
        <input type="text" class="form-control rak_kg rak_kg{{$count}}" count="{{$count}}"
            style="text-align: right;font-size: 12px;" value="0" name="rak_kg[]">
    </td>
    {{-- <td align="right">
        <input type="text" class="form-control rp_kg rp_kg{{$count}}" count="{{$count}}"
            style="text-align: right;font-size: 12px;">
        <input type="hidden" class="form-control  rp_kg_biasa rp_kg_biasa{{$count}}" name="rp_kg[]" value="0">
        <input type="hidden" class="ttl_rp_kg{{$count}}" value="0">
    </td> --}}
    <!-- Jual Kg -->
    <td style="vertical-align: top;">
        <button type="button" class="btn rounded-pill remove_baris_tf" count="{{$count}}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>