<div id="load_stok_pakan"></div>

<form action="{{ route('dashboard_kandang.save_opname_pakan') }}" method="post">
    @csrf
    <x-theme.modal title="Opname Pakan" size="modal-lg" idModal="opname_pakan">
        <div class="row">
            <div class="col-lg-12">
                <div id="opname_stk_pkn"></div>
            </div>
        </div>
    </x-theme.modal>
</form>
<form action="{{ route('dashboard_kandang.save_opname_pakan') }}" method="post">
    @csrf
    <x-theme.modal title="Opname Vitamin" size="modal-lg" idModal="opname_vitamin">
        <div class="row">
            <div class="col-lg-12">
                <div id="opname_stk_vtmn"></div>
            </div>
        </div>
    </x-theme.modal>
</form>
<form action="{{ route('dashboard_kandang.save_vaksin') }}" method="post">
    @csrf
    <x-theme.modal title="Pemakaian Vaksin" size="modal-lg" idModal="tbh_vaksin">
        <div class="row">
            <div class="col-lg-3">
                <div class="form-group">
                    <label for="">Tanggal</label>
                    <input value="{{ date('Y-m-d') }}" type="date" name="tgl" class="form-control">
                </div>
            </div>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="dhead" width="20%">Kandang</th>
                    <th class="dhead" width="15%">Nama Vaksin</th>
                    <th class="dhead" width="10%">Qty</th>
                    <th class="dhead" width="30%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select required name="id_kandang" class="form-control select2-vaksin">
                            <option value="">- Pilih Kandang -</option>
                            @foreach ($kandang as $d)
                                <option value="{{ $d->id_kandang }}">{{ $d->nm_kandang }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <select required name="id_pakan" class="form-control select2-vaksin">
                            <option value="">- Pilih Vaksin -</option>
                            @forelse ($produkVaksin as $d)
                                <option value="{{ $d->id_produk }}">{{ $d->nm_produk }}</option>
                            @empty
                                <option value="" disabled>Belum ada produk kategori vaksin</option>
                            @endforelse
                        </select>
                    </td>
                    <td>
                        <input required type="number" min="0.01" step="0.01" name="stok" class="form-control">

                    </td>
                    <td>
                        <input type="text" name="keterangan" class="form-control"
                            placeholder="Keterangan (opsional)">
                    </td>
                </tr>
            </tbody>
        </table>

    </x-theme.modal>
</form>
<x-theme.modal scroll="Y" title="History Stok" btnSave='T' size="modal-lg" idModal="history_stok">
    <div class="row">
        <div class="col-lg-12">
            <div id="history_stk"></div>
        </div>
    </div>
</x-theme.modal>
<form action="{{ route('dashboard_kandang.save_tambah_pakan_stok') }}" method="post">
    @csrf
    <x-theme.modal title="Tambah Stok Pakan" size="modal-lg" idModal="tbh_pakan">
        <div class="row">
            <div id="load_tambah_pakan"></div>
        </div>
    </x-theme.modal>
</form>
