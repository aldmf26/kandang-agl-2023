<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">

        <h5 class="float-start mt-1">{{ $title }}</h5>
        <div class="row justify-content-end">

            <div class="col-lg-12">
                <x-theme.button modal="Y" idModal="tambah_kandang" icon="fa-plus" addClass="float-end"
                    teks="Buat Baru" />

            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-hover table-bordered" id="table">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th class="text-center">Kandang</th>
                        <th class="text-end">Chick In</th>
                        <th class="text-end">Chick Out</th>
                        <th class="text-end">Chick In2</th>
                        <th>Strain</th>
                        <th class="text-end">Pop Awal</th>
                        <th class="text-end">Rupiah</th>

                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($kandang as $no => $a)
                        @php
                            $tgl = date('Y-m-d', strtotime('-3 months', strtotime($a->tgl_masuk)));
                        @endphp
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td class="text-center">{{ ucwords($a->nm_kandang) }}</td>
                            <td align="right">{{ tanggal($a->chick_in) }}</td>
                            <td align="right">{{ tanggal($a->chick_out) }}</td>
                            <td align="right" class="{{ date('Y-m-d') >= $tgl ? 'text-danger' : '' }}">
                                {{ tanggal($a->tgl_masuk) }} </td>
                            <td>{{ ucwords($a->nm_strain) }}</td>
                            <td class="text-end">
                                {{ $a->stok_awal }}
                            </td>
                            <td class="text-end">
                                Rp {{ number_format($a->rupiah, 0) }}
                            </td>

                            <td align="center">
                                @if (auth()->user()->posisi_id == 1)
                                    @if ($a->selesai == 'T')
                                        <a onclick="return confirm('Yakin ingin di selesaikan ?')"
                                            href="{{ route('dashboard_kandang.kandang_selesai', $a->id_kandang) }}"
                                            class="badge bg-primary"><i class="fas fa-check"></i></a>
                                    @else
                                        <a onclick="return confirm('Yakin ingin di selesaikan ?')"
                                            href="{{ route('dashboard_kandang.kandang_belum_selesai', $a->id_kandang) }}"
                                            class="badge bg-info"><i class="fas fa-undo"></i></a>
                                    @endif
                                    <a href="#" class="badge bg-warning edit_kandang"
                                        id_kandang="{{ $a->id_kandang }}" data-bs-toggle="modal"
                                        data-bs-target="#edit_kandang"><i class="fas fa-edit"></i></a>
                                @else
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        <form action="{{ route('data_kandang.store') }}" method="post">
            @csrf
            <x-theme.modal title="Tambah Kandang" idModal="tambah">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">chick In</label>
                            <input required value="{{ date('Y-m-d') }}" type="date" name="tgl"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Nama Kandang</label>
                            <input required type="text" name="nm_kandang" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Strain</label>
                        <select name="strain" class="form-control select2" id="">
                            <option value="">- Pilih Strain -</option>
                            @php
                                $strain = DB::table('strain')->get();
                            @endphp
                            @foreach ($strain as $d)
                                <option value="{{ $d->id_strain }}">{{ ucwords($d->nm_strain) }} Brown</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Ayam Awal</label>
                            <input required type="text" name="ayam_awal" class="form-control">
                        </div>
                    </div>
                </div>
            </x-theme.modal>
        </form>

        @include('dashboard_kandang.modal.tambah_kandang')


        <x-theme.btn_alert_delete route="data_kandang.delete" name="id_kandang" />
        <form action="{{ route('data_kandang.update') }}" method="post">
            @csrf
            <input type="hidden" name="route" value="dashboard_kandang.index">
            <x-theme.modal title="Edit Kandang" idModal="edit_kandang">
                <div class="row">
                    <div id="load-edit"></div>
                </div>
            </x-theme.modal>
        </form>
    </x-slot>
    @section('js')
        <script>
            edit('edit_kandang', 'id_kandang', 'data_kandang/edit', 'load-edit')
        </script>
    @endsection
</x-theme.app>
