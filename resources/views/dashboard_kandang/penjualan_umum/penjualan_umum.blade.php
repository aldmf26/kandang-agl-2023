<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6 class="float-start">{{ $title }}</h6>

        <x-theme.btn_dashboard route="dashboard_kandang.index" />
        <x-theme.button href="{{ route('dashboard_kandang.penjualan_umum_export', [$tgl1, $tgl2]) }}" icon="fa-print"
        addClass="float-end" teks="Export" />
        <x-theme.btn_filter />
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <table class="table" id="table1">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Nota</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th width="20%" class="text-center">Total Produk</th>
                        <th>Di Terima</th>
                        <th class="text-end">Total Rp (Rp. {{ number_format($ttlRp,0) }})</th>
                        <th width="25%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    
                    @foreach ($penjualan as $no => $d)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td class="detail_nota text-primary" no_nota="{{ $d->urutan }}" href="#"
                                data-bs-toggle="modal" data-bs-target="#detail">{{$d->kode}}-{{ $d->urutan }}</td>
                            <td>{{ tanggal($d->tgl) }}</td>
                            <td>{{ $d->id_customer }}</td>
                            <td align="center">{{ $d->ttl_produk }}</td>
                            <td><span class="btn btn-sm btn-success">{{ ucwords($d->admin_cek) ?? '' }}</span></td>
                            <td align="right">Rp. {{ number_format($d->total, 2) }}</td>
                            <td align="center">
                                <a class="btn btn-primary btn-sm detail_nota" no_nota="{{ $d->urutan }}" href="#" data-bs-toggle="modal"
                                    data-bs-target="#detail" href="#"><i
                                        class="me-2 fas fa-eye"></i>Detail</a>
                                @php
                                    $no_nota = "$d->kode-$d->urutan";
                                    $void = DB::table('tb_void')
                                        ->where('no_nota', $no_nota)
                                        ->where('status', 'T')
                                        ->first();
                                @endphp
                                @if (auth()->user()->posisi_id == 1)
                                    @if (!empty($void))
                                        <button type="button" onclick="copyToClipboard('{{ $void->voucher }}')"
                                            class="btn btn-sm btn-danger">Salin Voucher : {{ $void->voucher }}</button>
                                    @else
                                        <a
                                            class="btn btn-danger btn-sm "href="{{ route('dashboard_kandang.void_penjualan_umum', ['no_nota' => $no_nota, 'tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"><i
                                                class="me-2 fas fa-newspaper"></i>Voucher Edit</a>
                                    @endif
                                @else
                                    @if ($d->void == 'Y' || $d->tgl == date('Y-m-d'))
                                    <div class="btn-group" role="group">
                                        <span class="btn btn-sm" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-primary"></i>
                                        </span>
                                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                          
    
                                            <li>
                                                <a class="dropdown-item text-info edit_akun"
                                                    href="{{ route('dashboard_kandang.edit_penjualan', ['urutan' => $d->urutan]) }}"><i
                                                        class="me-2 fas fa-pen"></i>Edit</a>
                                            </li>
    
                                            <li>
                                                <a class="dropdown-item text-danger delete_nota"
                                                    no_nota="{{ $d->urutan }}" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#delete"><i class="me-2 fas fa-trash"></i>Delete
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    @endif
                                @endif

                                
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>

            <x-theme.modal btnSave="" title="Detail Jurnal" size="modal-lg" idModal="detail">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="detail_jurnal"></div>
                    </div>
                </div>
            </x-theme.modal>

            <x-theme.btn_alert_delete route="penjualan2.delete" name="urutan" :tgl1="$tgl1" :tgl2="$tgl2" />
        </section>
        @section('js')
            <script>
                edit('detail_nota', 'no_nota', 'detail', 'detail_jurnal')
            </script>
        @endsection
    </x-slot>
</x-theme.app>
