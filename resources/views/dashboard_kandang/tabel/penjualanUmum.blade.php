<div class="col-lg-3" id="penjualan-umum">
    <div class="card shadow-sm mb-4">
        <div class="card-header py-2 bg-primary text-white">
            <h6 class="mb-0" style="color: white">Penjualan Umum</h6>
        </div>
        <div class="card-body p-2">
            <div class="d-flex mb-2">
                <a data-bs-toggle="tooltip" title="Tambah Penjualan Umum"
                    href="{{ route('dashboard_kandang.add_penjualan_umum') }}"
                    class="btn btn-primary btn-sm me-1"><i class="fas fa-plus"></i></a>
                <a data-bs-toggle="tooltip" title="History"
                    href="{{ route('dashboard_kandang.penjualan_umum') }}"
                    class="btn btn-primary btn-sm me-1">History</a>
                <a data-bs-toggle="tooltip" title="Produk" href="{{ route('barang_dagangan.index') }}"
                    class="btn btn-primary btn-sm"><i class="fas fa-list"></i> Produk</a>
            </div>
            
            <div style="max-height: 350px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                <table class="table table-hover table-sm mb-0">
                    <thead class="bg-light sticky-top" style="z-index: 1;">
                        <tr>
                            <th class="ps-2">Produk</th>
                            <th class="text-end pe-2">Total Rp</th>
                            <th class="text-center" width="50">Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($produk as $d)
                            @php
                                $datas = DB::selectOne("SELECT GROUP_CONCAT(CONCAT(urutan)) as urutan, count(*) as ttl,
                                sum(total_rp) as ttl_rp FROM penjualan_agl
                                WHERE id_produk = '$d->id_produk' AND cek = 'T' AND lokasi = 'mtd' GROUP BY id_produk");

                                if (!empty($datas)) {
                                    $urutan = implode(', ', explode(',', $datas->urutan));
                                } else {
                                    continue;
                                }
                            @endphp
                            <tr>
                                <td class="ps-2">
                                    <a href="#" class="detail_nota text-decoration-none" 
                                       data-bs-toggle="modal" data-bs-target="#detail_nota"
                                       urutan="{{ $urutan }}, {{ $d->id_produk }}">
                                       {{ $d->nm_produk }}
                                    </a>
                                </td>
                                <td align="right" class="pe-2">{{ number_format($datas->ttl_rp, 0) }}</td>
                                <td align="center">{{ $datas->ttl }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<x-theme.modal title="Detail Nota Penjualan Umum" btnSave="" size="modal-lg" idModal="detail_nota">
    <div id="load_detail_nota"></div>
</x-theme.modal>
{{-- end tambah detail nota --}}
