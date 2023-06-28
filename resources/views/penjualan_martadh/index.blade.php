<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="row">
            <div class="col-lg-6">
                <h6 class="float-start mt-1">{{ $title }}</h6> <br><br>
                <p>Piutang Diceklis : Rp. <span class="piutangBayar">0</span></p>
            </div>
            <div class="col-lg-6">
                <x-theme.button modal="T" icon="fa-plus" addClass="float-end btn_bayar" teks="Setor" />
                <x-theme.button modal="T" href="/produk_telur" icon="fa-home" addClass="float-end" teks="" />
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-8"></div>
            <div class="col-lg-4 mb-2">
                <table class="float-end">
                    <td>Pencarian :</td>
                    <td><input type="text" id="pencarian" class="form-control float-end"></td>
                </table>
            </div>
            <table class="table table-hover" id="nanda">
                <thead>
                    <tr>
                        <th width="5">#</th>
                        <th>Tanggal</th>
                        <th>No Nota</th>
                        <th>Customer</th>
                        <th style="text-align: right">Total Rp</th>
                        <th style="text-align: center">Cek</th>
                        <th>Admin</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice as $no => $i)
                    <tr>
                        <td>{{$no+1}}</td>
                        <td>{{tanggal($i->tgl)}}</td>
                        <td>{{$i->no_nota}}</td>
                        <td>{{$i->nm_customer}}{{$i->urutan_customer}}</td>
                        <td align="right">Rp {{number_format($i->ttl_rp,0)}}</td>
                        <td align="center">
                            @if ($i->cek == 'Y')
                            <i class="fas fa-check text-success"></i>
                            @else
                            <input type="checkbox" name="" no_nota="{{$i->no_nota}}" piutang="{{ $i->ttl_rp }}" id=""
                                class="cek_bayar">
                            @endif
                        </td>
                        <td>{{$i->admin}}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <span class="btn btn-sm" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v text-primary"></i>
                                </span>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    @if ($i->cek == 'Y')

                                    @else
                                    <li>
                                        <a class="dropdown-item text-primary edit_akun"
                                            href="{{route('edit_invoice_telur',['no_nota' => $i->no_nota])}}"><i
                                                class="me-2 fas fa-pen"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger delete_nota" no_nota="{{$i->no_nota}}"
                                            href="#" data-bs-toggle="modal" data-bs-target="#delete"><i
                                                class="me-2 fas fa-trash"></i>Delete
                                        </a>
                                    </li>
                                    @endif

                                    <li><a class="dropdown-item  text-info detail_nota" href="#" href="#"
                                            data-bs-toggle="modal" no_nota="{{ $i->no_nota }}"
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
        <x-theme.modal title="Edit Akun" idModal="sub-akun" size="modal-lg">
            <div id="load-sub-akun">
            </div>
        </x-theme.modal>

        <x-theme.modal title="Detail Invoice" btnSave='T' size="modal-lg-max" idModal="detail">
            <div class="row">
                <div class="col-lg-12">
                    <div id="detail_invoice"></div>
                </div>
            </div>

        </x-theme.modal>

        <form action="{{ route('delete_invoice_telur') }}" method="get">
            <div class="modal fade" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                            <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Batal</button>
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
            pencarian('pencarian', 'nanda')
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

            $(".btn_bayar").hide();
            $(".piutang_cek").hide();
            $(document).on('change', '.cek_bayar', function() {
                var totalPiutang = 0
                $('.cek_bayar:checked').each(function() {
                    var piutang = $(this).attr('piutang');
                    totalPiutang += parseInt(piutang);
                });
                var anyChecked = $('.cek_bayar:checked').length > 0;
                $('.btn_bayar').toggle(anyChecked);
                $(".piutang_cek").toggle(anyChecked);
                $('.piutangBayar').text(totalPiutang.toLocaleString('en-US'));
            });

                $('.hide_bayar').hide();
                $(document).on("click", ".detail_bayar", function() {
                    var no_nota = $(this).attr('no_nota');
                    var clickedElement = $(this); // Simpan elemen yang diklik dalam variabel

                    clickedElement.prop('disabled', true); // Menonaktifkan elemen yang diklik

                    $.ajax({
                        type: "get",
                        url: "/get_pembayaranpiutang_telur?no_nota=" + no_nota,
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
                $(document).on('click', '.btn_bayar', function() {
                var dipilih = [];
                $('.cek_bayar:checked').each(function() {
                    var no_nota = $(this).attr('no_nota');
                    dipilih.push(no_nota);

                });
                var params = new URLSearchParams();

                dipilih.forEach(function(orderNumber) {
                    params.append('no_nota', orderNumber);
                });
                var queryString = 'no_nota[]=' + dipilih.join('&no_nota[]=');
                window.location.href = "/terima_invoice_mtd?" + queryString;

            });
        });
    </script>
    @endsection
</x-theme.app>