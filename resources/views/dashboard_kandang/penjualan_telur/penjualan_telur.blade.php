<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }} : {{ tanggal($tgl1) }}~{{ tanggal($tgl2) }}</h6>
            </div>
            <div class="col-lg-6">
                <x-theme.button modal="T" href="{{ route('dashboard_kandang.add_penjualan_telur') }}" icon="fa-plus"
                    addClass="float-end" teks="Buat Nota" />
                <x-theme.button modal="T" href="{{ route('dashboard_kandang.add_penjualan_telur', ['tgl1' => $tgl1, 'tgl2' => $tgl2]) }}" icon="fa-print"
                    addClass="float-end" teks="Export" />
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
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>Nota</th>
                        <th>Customer</th>
                        <th style="text-align: right">Total Rp ({{number_format($ttl_rp,0)}})</th>
                        <th>Diterima</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penjualan as $no => $s)
                    <tr class="induk_detail{{ $s->no_nota }}">
                        <td>{{ $no + 1 }}</td>
                        <td>{{ tanggal($s->tgl) }}</td>
                        <td>{{ $s->no_nota }}</td>
                        <td>{{ $s->customer }}</td>
                        <td align="right">{{ number_format($s->ttl_rp, 0) }} </td>
                        <td><span class="btn btn-sm btn-success">{{ ucwords($s->admin_cek) ?? '' }}</span></td>
                        <td align="center">
                            <a class="btn btn-primary btn-sm detail" no_nota="{{ $s->no_nota }}" href="#"><i
                                    class="me-2 fas fa-eye"></i>Detail</a>
                            @php
                            $void = DB::table('tb_void')
                            ->where('no_nota', $s->no_nota)
                            ->where('status', 'T')
                            ->first();
                            @endphp
                            @if (auth()->user()->posisi_id == 1)
                            @if (!empty($void))
                            <button type="button" onclick="copyToClipboard('{{ $void->voucher }}')"
                                class="btn btn-sm btn-danger">Salin Voucher : {{ $void->voucher }}</button>
                            @else
                            <a class="btn btn-danger btn-sm "
                                href="{{ route('dashboard_kandang.void_penjualan_mtd', ['no_nota' => $s->no_nota, 'tgl1' => $tgl1, 'tgl2' => $tgl2]) }}"><i
                                    class="me-2 fas fa-newspaper"></i>Voucher Edit</a>
                            @endif
                            @else
                            @if ($s->void == 'Y' || $s->tgl == date('Y-m-d'))
                            <div class="btn-group" role="group">
                                <span class="btn btn-sm" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-primary"></i>
                                </span>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    <li><a class="dropdown-item text-primary "
                                            href="{{ route('dashboard_kandang.edit_telur', ['no_nota' => $s->no_nota]) }}"><i
                                                class="me-2 fas fa-pen"></i>Edit</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger delete_nota" no_nota="{{ $s->no_nota }}"
                                            href="#" data-bs-toggle="modal" data-bs-target="#delete"><i
                                                class="me-2 fas fa-trash"></i>Delete
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
            <x-theme.modal btnSave="" title="Detail Penjualan Telur" size="modal-lg-max" idModal="detail">
                <div class="row">
                    <div class="col-lg-12">
                        <div id="detail_penjualan"></div>
                    </div>
                </div>
            </x-theme.modal>
            <x-theme.btn_alert_delete route="dashboard_kandang.delete_penjualan_mtd" name="no_nota" :tgl1="$tgl1"
                :tgl2="$tgl2" />
        </section>
    </x-slot>
    @section('js')
    <script src="{{ asset('js/salin_voucher.js') }}"></script>

    <script>
        $(document).ready(function() {
                $(document).on('click', '.delete_nota', function() {
                    var no_nota = $(this).attr('no_nota');
                    $('.no_nota').val(no_nota);
                });
                $(document).on('click', '.detail', function() {
                    var no_nota = $(this).attr('no_nota');

                    $.ajax({
                        type: "get",
                        url: "/dashboard_kandang/detail_penjualan_mtd?no_nota=" + no_nota,
                        success: function(data) {
                            $("#detail").modal('show');
                            $("#detail_penjualan").html(data);
                        }
                    });
                });
                $('.hide_bayar').hide();
                $(document).on("click", ".detail_bayar", function() {
                    var no_nota = $(this).attr('no_nota');
                    var clickedElement = $(this); // Simpan elemen yang diklik dalam variabel

                    clickedElement.prop('disabled', true); // Menonaktifkan elemen yang diklik

                    $.ajax({
                        type: "get",
                        url: "/dashboard_kandang/get_detail_penjualan_mtd?no_nota=" + no_nota,
                        success: function(data) {
                            $('.induk_detail' + no_nota).after("<tr>" + data + "</tr>");
                            $(".show_detail" + no_nota).show();
                            $(".detail_bayar" + no_nota).hide();
                            $(".hide_bayar" + no_nota).show();

                            clickedElement.prop('disabled',
                                false
                            ); // Mengaktifkan kembali elemen yang diklik setelah tampilan ditambahkan
                        },
                        error: function() {
                            clickedElement.prop('disabled',
                                false
                            ); // Jika ada kesalahan dalam permintaan AJAX, pastikan elemen yang diklik diaktifkan kembali
                        }
                    });
                });

                $(document).on("click", ".hide_bayar", function() {
                    var no_nota = $(this).attr('no_nota');
                    $(".show_detail" + no_nota).remove();
                    $(".detail_bayar" + no_nota).show();
                    $(".hide_bayar" + no_nota).hide();

                });
            });
    </script>
    @endsection
</x-theme.app>