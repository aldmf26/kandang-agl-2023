<tr class="baris{{ $count }}">

    <td>
        <select name="id_pakan[]" id="" class="select2tbhPakan id_vitamin id_vitamin{{ $count }}"
            count='{{ $count }}'>
            <option value="">- Pilih Produk -</option>
            @foreach ($produk as $p)
                <option value="{{ $p->id_produk }}">{{ $p->nm_produk }}</option>
            @endforeach
            <option value="tambah">+ Produk Baru</option>
        </select>
    </td>
    <td><input type="text" name="pcs[]" class="form-control"></td>
    @if ($kategori == 'vitamin')
        <td class="satuan_vitamin{{ $count }}"></td>
    @endif
    <td><input type="text" value="0" name="ttl_rp[]" class="form-control"></td>
    <td><input type="text" value="0" name="biaya_dll[]" class="form-control"></td>
    <td>
        <button type="button" class="btn rounded-pill remove_baris" count="{{ $count }}"><i
                class="fas fa-trash text-danger"></i>
        </button>
    </td>
</tr>
