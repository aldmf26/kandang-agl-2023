<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">

        <h5 class="float-start mt-1">{{ $title }}</h5>
        <div class="row justify-content-end">

            <div class="col-lg-12">
                <x-theme.button modal="Y" idModal="tambah" icon="fa-plus" addClass="float-end" teks="Buat Baru" />

            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-hover" id="table">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Check In</th>
                        <th>Nama Kandang</th>
                        <th>Strain</th>
                        <th>Ayam Awal</th>
                        <th>Ayam Akhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($kandang as $no => $a)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ tanggal($a->chick_in) }}</td>
                            <td>{{ ucwords($a->nm_kandang) }}</td>
                            <td>{{ ucwords($a->nm_strain) }}</td>
                            <td>
                                {{ $a->stok_awal }}
                            </td>
                            <td>
                                1
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <span class="btn btn-sm" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v text-primary"></i>
                                    </span>
                                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">

                                        <li>
                                            <a class="dropdown-item text-info edit" href="#"
                                                data-bs-toggle="modal" data-bs-target="#edit"
                                                id_akun="{{ $a->id_kandang }}"><i class="me-2 fas fa-pen"></i>Edit</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger delete_nota"
                                                no_nota="{{ $a->id_kandang }}" href="#" data-bs-toggle="modal"
                                                data-bs-target="#delete"><i class="me-2 fas fa-trash"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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

        <form action="{{ route('data_kandang.update') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Kandang" idModal="edit" size="modal-lg">
                <div id="load-edit">
                </div>
            </x-theme.modal>
        </form>

        <x-theme.btn_alert_delete route="data_kandang.delete" name="id_kandang" />
    </x-slot>
    @section('js')
        <script>
            edit('edit', 'id_akun', 'data_kandang/edit', 'load-edit')
        </script>
    @endsection
</x-theme.app>
