<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <a href="#" data-bs-toggle="modal" data-bs-target="#view" class="btn icon icon-left btn-primary "
            style="float: right; margin-right: 2px"><i class="fas fa-calendar-week"></i> View</a>
    </x-slot>
    <x-slot name="cardBody">
        <div id="tableLoad"></div>

        {{-- view --}}
        <form id="formView">
            <x-theme.modal title="View Cashflow" idModal="view">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Dari</label>
                            <input type="date" id="tgl1" name="tgl1" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="">Sampai</label>
                            <input type="date" id="tgl2" name="tgl2" class="form-control">
                        </div>
                    </div>
                </div>

            </x-theme.modal>
        </form>
        {{-- end view --}}

        {{-- form sub kategori --}}
        <form id="formEditSubKategori">
            <x-theme.modal title="Kategori" size="modal-lg" idModal="modalSubKategori">
                <div id="loadSubKategori"></div>
            </x-theme.modal>
        </form>
        {{-- end form sub kategori --}}
    </x-slot>

    @section('scripts')
        <script>
            loadTabel()

            function toast(pesan) {
                Toastify({
                    text: pesan,
                    duration: 3000,
                    style: {
                        background: "#EAF7EE",
                        color: "#7F8B8B"
                    },
                    close: true,
                    avatar: "https://cdn-icons-png.flaticon.com/512/190/190411.png"
                }).showToast();
            }

            function loadTabel(tgl1 = "{{ date('Y-m-1') }}", tgl2 = "{{ date('Y-m-d') }}") {
                $.ajax({
                    type: "GET",
                    url: "{{ route('cashflow.load') }}",
                    data: {
                        tgl1: tgl1,
                        tgl2: tgl2,
                    },
                    success: function(r) {
                        $("#tableLoad").html(r);

                    }
                });
            }

            function loadSubKategori(jenis) {
                $.ajax({
                    type: "GET",
                    url: "{{ route('cashflow.loadSubKategori') }}?jenis=" + jenis,
                    success: function(r) {
                        $("#loadSubKategori").html(r);
                        $('.jenisSub').val(jenis)
                        $("#table2").DataTable({
                            "lengthChange": false,
                            "autoWidth": false,
                            "stateSave": true,
                        })
                    }
                });
            }

            $(document).on('submit', '#formView', function(e) {
                e.preventDefault()
                $('#view').modal('hide')
                var tgl1 = $("#tgl1").val()
                var tgl2 = $("#tgl2").val()

                loadTabel(tgl1, tgl2)
            })

            $(document).on('click', '.btnSubKategori', function() {
                $("#modalSubKategori").modal('show')
                var jenis = $(this).attr('jenis')
                loadSubKategori(jenis)

            })

            $(document).on('click', '#btnFormSubKategori', function() {
                var sub_kategori = $('#sub_kategori').val()
                var urutan = $('#urutan').val()
                var jenis = $('.jenisSub').val()
                $.ajax({
                    type: "GET",
                    url: "{{ route('cashflow.saveSubKategori') }}",
                    data: {
                        sub_kategori: sub_kategori,
                        urutan: urutan,
                        jenis: jenis
                    },
                    success: function(r) {
                        toast('Berhasil tambah sub kategori')
                        loadSubKategori(jenis)
                        loadTabel()
                    }
                });
            })

            $(document).on('submit', '#formEditSubKategori', function(e) {
                e.preventDefault()
                var data = $("#formEditSubKategori").serialize()
                var jenis = $('.jenisSub').val()
                $.ajax({
                    type: "GET",
                    url: "{{ route('cashflow.editSubKategori') }}?" + data,
                    success: function(response) {
                        toast('Berhasil edit sub kategori')
                        loadSubKategori(jenis)
                        loadTabel()
                        $("#modalSubKategori").modal('hide')
                    }
                });
            })
        </script>
    @endsection
</x-theme.app>