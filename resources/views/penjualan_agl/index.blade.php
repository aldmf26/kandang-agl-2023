<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}: {{ tanggal($tgl1) }} ~ {{ tanggal($tgl2) }}</h6>
            </div>
            <div class="col-lg-6">
                <x-theme.button modal="T" href="{{ route('dashboard_kandang.add_penjualan_telur') }}" icon="fa-plus"
                    addClass="float-end" teks="Buat Invoice" />
                <x-theme.btn_filter />
                <x-theme.button modal="T" href="/produk_telur" icon="fa-home" addClass="float-end"
                    teks="" />
                <x-theme.button modal="Y" idModal="customer" icon="fa-plus" addClass="float-end" teks="Customer" />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-hover" id="table">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>No Nota</th>
                        <th>Customer</th>
                        <th style="text-align: right">Total Rp</th>
                        <th style="text-align: right">Total Rak</th>
                        <th>Admin</th>
                        <th>Import</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice as $no => $i)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ tanggal($i->tgl) }}</td>
                            <td>{{ $i->no_nota }}</td>
                            <td>{{ $i->nm_customer }}{{ $i->urutan_customer }}</td>
                            <td align="right">Rp {{ number_format($i->ttl_rp, 0) }}</td>
                            <td align="right"> {{ number_format($i->rak_tf, 0) }}</td>
                            {{-- <td>{{ $i->tipe }}</td> --}}
                            <td>{{ ucwords($i->admin) }}</td>

                            <td>
                                <span class="badge {{ $i->import == 'T' ? 'bg-warning' : 'bg-success' }}">
                                    {{ $i->import == 'T' ? 'Belum Import' : 'Sudah Import' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <span class="btn btn-sm" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v text-primary"></i>
                                    </span>
                                    <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                        @if ($i->import == 'Y')
                                            <li>
                                                <a class="dropdown-item text-primary edit_akun"
                                                    href="{{ route('edit_invoice_telur', ['no_nota' => $i->no_nota]) }}"><i
                                                        class="me-2 fas fa-pen"></i>Edit
                                                </a>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item text-primary edit_akun"
                                                    href="{{ route('edit_invoice_telur', ['no_nota' => $i->no_nota]) }}"><i
                                                        class="me-2 fas fa-pen"></i>Edit
                                                </a>
                                            </li>

                                            <li>
                                                <a class="dropdown-item text-danger delete_nota"
                                                    no_nota="{{ $i->no_nota }}" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#delete"><i class="me-2 fas fa-trash"></i>Delete
                                                </a>
                                            </li>
                                        @endif

                                        <li><a class="dropdown-item  text-info detail_nota" href="#"
                                                href="#" data-bs-toggle="modal" no_nota="{{ $i->no_nota }}"
                                                data-bs-target="#detail"><i class="me-2 fas fa-search"></i>Detail</a>
                                        </li>

                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </section>

        {{-- sub akun --}}
        <form action="{{ route('plus_customer') }}" method="post">
            @csrf
            <x-theme.modal title="Tambah customer" idModal="customer">

                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Nama Customer</label>
                        <input type="text" name="nm_customer" class="form-control">
                    </div>
                    <div class="col-lg-6">
                        <label for="">Kode Accurate</label>
                        <input type="text" name="kode_customer" class="form-control">
                    </div>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.modal title="Detail Invoice" btnSave='T' size="modal-lg-max" idModal="detail">
            <div class="row">
                <div class="col-lg-12">
                    <div id="detail_invoice"></div>
                </div>
            </div>

        </x-theme.modal>

        <form action="{{ route('delete_invoice_telur') }}" method="get">
            <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body">
                            <div class="row">
                                <h5 class="text-danger ms-4 mt-4"><i class="fas fa-trash"></i> Hapus Data</h5>
                                <p class=" ms-4 mt-4">Apa anda yakin ingin menghapus ?</p>
                                <input type="hidden" class="no_nota" name="no_nota">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-danger"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        {{-- end sub akun --}}
    </x-slot>
    @section('scripts')
        <script>
            $(document).ready(function() {
                $(document).on("click", ".detail_nota", function() {
                    var no_nota = $(this).attr('no_nota');
                    $.ajax({
                        type: "get",
                        url: "/detail_invoice_telur?no_nota=" + no_nota,
                        success: function(data) {
                            $("#detail_invoice").html(data);
                        }
                    });

                });
                $(document).on('click', '.delete_nota', function() {
                    var no_nota = $(this).attr('no_nota');
                    $('.no_nota').val(no_nota);
                });
            });
        </script>
    @endsection
</x-theme.app>
