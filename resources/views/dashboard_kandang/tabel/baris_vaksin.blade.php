<tr class="baris-vaksin">
    <td>
        <select required name="baris[{{ $index }}][id_kandang]" class="form-control select2-vaksin">
            <option value="">- Pilih Kandang -</option>
            @foreach ($kandang as $d)
                <option value="{{ $d->id_kandang }}">{{ $d->nm_kandang }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select required name="baris[{{ $index }}][id_pakan]" class="form-control select2-vaksin">
            <option value="">- Pilih Vaksin -</option>
            @forelse ($produkVaksin as $d)
                <option value="{{ $d->id_produk }}">{{ $d->nm_produk }}</option>
            @empty
                <option value="" disabled>Belum ada produk kategori vaksin</option>
            @endforelse
        </select>
    </td>
    <td><input required type="number" min="0.01" step="0.01" name="baris[{{ $index }}][stok]" class="form-control"></td>
    <td><input type="text" maxlength="500" name="baris[{{ $index }}][keterangan]" class="form-control" placeholder="Keterangan (opsional)"></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger hapus-baris-vaksin" aria-label="Hapus baris"><i class="fas fa-trash"></i></button></td>
</tr>
