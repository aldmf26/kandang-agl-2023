<div class="col-lg-3">
    <h6>
        Penjualan Umum

    </h6>
    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Tambah Penjualan Umum"
        href="{{ route('dashboard_kandang.add_penjualan_umum') }}"
        class="badge mb-2 float-start me-2 bg-primary text-sm"><i class="fas fa-plus"></i></a>
    <a data-bs-toggle="tooltip" data-bs-placement="top" title="History Penjualan Umum"
        href="{{ route('dashboard_kandang.penjualan_umum') }}"
        class="badge mb-2 float-start me-2 bg-primary text-sm">History</a>
    <a data-bs-toggle="tooltip" data-bs-placement="top" title="Data Produk" href="{{ route('barang_dagangan.index') }}"
        class="badge mb-2 float-start me-2 bg-primary text-sm"><i class="fas fa-list"></i>
        Produk</a>
    <table class="table table-bordered table-hover" id="">
        <thead>
            <tr>
                <th class="text-center dhead">Produk</th>
                <th width="30%" class="text-end dhead">Ttl Rp</th>
                <th width="20%" class="text-end dhead">Nota PAGL</th>
                <th width="16%" class="text-end dhead">Ttl Nota</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @foreach ($produk as $d)
                @php
                    $datas = DB::selectOne("SELECT GROUP_CONCAT(CONCAT(urutan)) as urutan,count(*) as ttl,
                    sum(total_rp) as ttl_rp FROM penjualan_agl
                    WHERE id_produk = '$d->id_produk' AND cek = 'T' AND lokasi = 'mtd' GROUP BY id_produk");

                    if (!empty($datas)) {
                        $urutan = implode(', ', explode(',', $datas->urutan));
                    } else {
                        continue;
                    }
                @endphp

                <tr>
                    <td>{{ $d->nm_produk }}</td>
                    <td align="right">Rp. {{ !empty($datas) ? number_format($datas->ttl_rp, 0) ?? 0 : 0 }}</td>
                    @if (!empty($datas))
                        <td data-bs-toggle="modal" data-bs-target="#detail_nota"
                            class="detail_nota text-primary cursor-pointer"
                            urutan="{{ $urutan }}, {{ $d->id_produk }}">{{ $urutan }}</td>
                    @else
                        <td align="right">0</td>
                    @endif
                    <td align="right">{{ !empty($datas) ? $datas->ttl ?? 0 : 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>


{{-- tambah detail nota --}}
<x-theme.modal title="Detail Nota Penjualan Umum" btnSave="" size="modal-lg" idModal="detail_nota">
    <div id="load_detail_nota"></div>
</x-theme.modal>
{{-- end tambah detail nota --}}
