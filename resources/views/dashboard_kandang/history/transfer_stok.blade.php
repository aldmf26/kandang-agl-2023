<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} : {{ tanggal($tgl1) }}~{{ tanggal($tgl2) }}</h6>
            </div>
            <div class="col-lg-6">
                <x-theme.button modal="T"
                    href="{{ route('dashboard_kandang.add_transfer_stok', ['id_gudang' => 1]) }}" icon="fa-exchange-alt"
                    addClass="float-end" teks="Transfer Stok" />
                <x-theme.btn_dashboard route="dashboard_kandang.index" />

                <x-theme.btn_filter />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <table class="table table-hover" id="table">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="10%">Tanggal</th>
                        <th width="15%">Nota</th>
                        <th class="text-end" width="10%">Pcs</th>
                        <th class="text-end" width="10%">Kg</th>
                        <th class="text-end" width="10%">Ikat</th>
                        <th class="text-center" width="15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transfer as $no => $s)
                        <tr>
                            <td>{{ $no + 1 }}</td>
                            <td>{{ tanggal($s->tgl) }}</td>
                            <td>{{ $s->no_nota }}</td>

                            <td align="right">{{ $s->pcs }}</td>
                            <td align="right">{{ $s->kg }}</td>
                            <td align="right">{{ number_format($s->pcs / 180, 2) }} </td>
                            <td align="center">
                                <a class="btn btn-primary btn-sm detail_transfer" data-bs-target="#detail_transfer"
                                    data-bs-toggle="modal" no_nota="{{ $s->no_nota }}" href="#"><i
                                        class="me-2 fas fa-eye"></i>Detail</a>
                                @if ($s->void == 'Y' || $s->tgl == date('Y-m-d'))
                                    <div class="btn-group" role="group">
                                        <span class="btn btn-sm" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v text-primary"></i>
                                        </span>
                                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <li><a class="dropdown-item text-primary "
                                                    href="{{ route('dashboard_kandang.edit_transfer_stok', ['nota' => $s->no_nota]) }}"><i
                                                        class="me-2 fas fa-pen"></i>Edit</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger delete_nota"
                                                    no_nota="{{ $s->no_nota }}" href="#" data-bs-toggle="modal"
                                                    data-bs-target="#delete"><i class="me-2 fas fa-trash"></i>Delete
                                                </a>
                                            </li>
                                            @if (auth()->user()->posisi_id == 1)
                                                <a class="dropdown-item"
                                                    href="{{ route('dashboard_kandang.void_transfer', ['nota' => $s->no_nota, 'value' => 'T']) }}"><i
                                                        class="me-2 fas fa-window"></i>Tutup Akses
                                                </a>
                                            @endif
                                        </ul>
                                    </div>
                                @else
                                    @if (auth()->user()->posisi_id == 1)
                                        <x-theme.button
                                            href="{{ route('dashboard_kandang.void_transfer', ['nota' => $s->no_nota]) }}"
                                            icon="fa-times" variant="danger" teks="Void" />
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </tbody>
            </table>
        </section>
        <x-theme.modal btnSave="" title="Detail Jurnal" size="modal-lg" idModal="detail_transfer">
            <div class="row">
                <div class="col-lg-12">
                    <div id="detail_jurnal"></div>
                </div>
            </div>
        </x-theme.modal>

        <x-theme.btn_alert_delete route="dashboard_kandang.delete_transfer" name="no_nota" :tgl1="$tgl1"
            :tgl2="$tgl2" />
    </x-slot>
    @section('js')
        <script>
            edit('detail_transfer', 'no_nota', 'detail_transfer', 'detail_jurnal')
            $(document).ready(function() {
                $(document).on('click', '.delete_nota', function() {
                    var no_nota = $(this).attr('no_nota');
                    $('.no_nota').val(no_nota);
                })
            });
        </script>
    @endsection
</x-theme.app>
