<x-theme.app title="{{ $title }}" table="Y" sizeCard="10" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <h5 class="mb-0"><i class="fas fa-clipboard-list me-2"></i>{{ $title }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard_kandang.harian') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Harian
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambah_perencanaan_harian">
                    <i class="fas fa-plus me-1"></i> Tambah Perencanaan
                </button>
            </div>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        @if (session('sukses'))
            <div class="alert alert-success">{{ session('sukses') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard_kandang.input_harian') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dari</label>
                        <input type="date" name="tgl1" value="{{ $tgl1 }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai</label>
                        <input type="date" name="tgl2" value="{{ $tgl2 }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Kandang</label>
                        <select name="id_kandang" class="form-control select2">
                            <option value="">Semua kandang</option>
                            @foreach ($kandang as $k)
                                <option value="{{ $k->id_kandang }}" @selected((string) $id_kandang === (string) $k->id_kandang)>{{ $k->nm_kandang }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th class="dhead text-center" style="width:40px">#</th>
                        <th class="dhead text-center">Tanggal</th>
                        <th class="dhead text-center">Kandang</th>
                        <th class="dhead text-center">No Nota</th>
                        <th class="dhead text-end">Pakan (Kg)</th>
                        <th class="dhead text-center">Jurnal</th>
                        <th class="dhead text-center" style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td class="text-center">{{ $rows->firstItem() + $i }}</td>
                            <td class="text-center text-nowrap">{{ tanggal($row->tgl) }}</td>
                            <td class="fw-semibold">{{ $row->nm_kandang }}</td>
                            <td class="text-center">{{ $row->no_nota }}</td>
                            <td class="text-end">{{ number_format($row->total_gr / 1000, 1, ',', '.') }}</td>
                            <td class="text-center text-nowrap">{{ $row->ada_jurnal ? 'Masuk' : 'Belum' }}{{ $row->sudah_cek ? ' / Dicek' : '' }}</td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="btn btn-sm btn-info btn-detail-harian" data-tgl="{{ $row->tgl }}" data-kandang="{{ $row->id_kandang }}" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if (!$row->sudah_cek)
                                    <button type="button" class="btn btn-sm btn-warning btn-edit-harian" data-tgl="{{ $row->tgl }}" data-kandang="{{ $row->id_kandang }}" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('dashboard_kandang.hapus_perencanaan') }}" class="d-inline" onsubmit="return confirm('Hapus data perencanaan ini beserta stok dan jurnalnya?')">
                                        @csrf
                                        <input type="hidden" name="tgl" value="{{ $row->tgl }}">
                                        <input type="hidden" name="id_kandang" value="{{ $row->id_kandang }}">
                                        <button class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada hasil Tambah Perencanaan pada periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rows->hasPages())
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 mt-3">
                <small class="text-muted">Menampilkan {{ $rows->firstItem() }}–{{ $rows->lastItem() }} dari {{ $rows->total() }} data</small>
                {{ $rows->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif

        <form action="{{ route('dashboard_kandang.tambah_perencanaan') }}" method="post">
            @csrf
            <x-theme.modal title="Tambah Perencanaan" size="modal-lg" idModal="tambah_perencanaan_harian">
                <div class="mb-3">
                    <label class="form-label">Pilih Kandang</label>
                    <select id="pilih_kandang_tambah" class="form-control select2-tambah-harian">
                        <option value="">- Pilih Kandang -</option>
                        @foreach ($kandang as $k)
                            <option value="{{ $k->id_kandang }}">{{ $k->nm_kandang }}</option>
                        @endforeach
                    </select>
                </div>
                <div id="load_perencanaan_harian">
                    <div class="alert alert-info mb-0">Pilih kandang untuk memuat form perencanaan.</div>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.modal title="Detail Perencanaan" btnSave="T" size="modal-lg-max" idModal="detail_harian">
            <div id="load_detail_harian"></div>
        </x-theme.modal>

        <form method="POST" action="{{ route('dashboard_kandang.edit_perencanaan') }}">
            @csrf
            <x-theme.modal title="Edit Perencanaan" size="modal-lg" idModal="edit_harian">
                <div id="load_edit_harian"></div>
            </x-theme.modal>
        </form>
    </x-slot>

    @section('js')
        <script>
            $(function() {
                $('.select2').select2();
            });

            $(document).on('shown.bs.modal', '#tambah_perencanaan_harian', function() {
                $('.select2-tambah-harian').select2({
                    dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                });
            });

            $(document).on('change', '#pilih_kandang_tambah', function() {
                var id = $(this).val();
                if (!id) {
                    $('#load_perencanaan_harian').html('<div class="alert alert-info mb-0">Pilih kandang untuk memuat form perencanaan.</div>');
                    return;
                }
                $('#load_perencanaan_harian').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Memuat form...</div>');
                $.get("{{ url('dashboard_kandang/load_perencanaan') }}/" + id, function(r) {
                    $('#load_perencanaan_harian').html(r);
                    $.get("{{ route('dashboard_kandang.load_pakan_perencanaan') }}", function(x) {
                        $('#load_pakan_perencanaan').html(x);
                        $('.select2-edit').select2({
                            dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                        });
                    });
                    $.get("{{ route('dashboard_kandang.load_obat_pakan') }}", function(x) {
                        $('#load_obat_pakan').html(x);
                        $('.select2-edit').select2({
                            dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                        });
                    });
                    $.get("{{ route('dashboard_kandang.load_obat_air') }}", function(x) {
                        $('#load_obat_air').html(x);
                        $('.select2-edit').select2({
                            dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                        });
                    });
                    $.get("{{ route('dashboard_kandang.load_obat_ayam') }}", function(x) {
                        $('#load_obat_ayam').html(x);
                        $('.select2-edit').select2({
                            dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                        });
                    });
                }).fail(function() {
                    $('#load_perencanaan_harian').html('<div class="alert alert-danger mb-0">Form gagal dimuat.</div>');
                });
            });

            $(document).on('change', '#tglPerencanaan', function() {
                var tgl = $(this).val(),
                    id = $('#id_kandang').val();
                if (!tgl || !id) return;
                $.getJSON("{{ route('dashboard_kandang.get_populasi') }}", {
                    tgl: tgl,
                    id_kandang: id
                }, function(r) {
                    $('#getPopulasi').val((parseFloat(r.stok_awal) || 0) - (parseFloat(r.pop) || 0));
                    hitungPakanHarian();
                });
            });

            function hitungPakanHarian() {
                var pop = parseFloat($('#getPopulasi').val()) || 0,
                    gr = parseFloat($('#gr').val()) || 0;
                $('.persen').each(function(i) {
                    var pct = parseFloat($(this).val()) || 0;
                    $('.hasil').eq(i).val((pct * gr * pop / 100).toFixed(2));
                });
            }
            $(document).on('keyup change', '#gr,.persen', hitungPakanHarian);

            $(document).on('click', '.tbhPakan', function() {
                var n = $('.persen').length + 1;
                $.get("{{ route('dashboard_kandang.tbh_pakan') }}?count=" + n, function(x) {
                    $('#tbhPakan').append(x);
                    $('.select2-pakan').select2({
                        dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                    });
                });
            });
            $(document).on('click', '.tbhObatPakan', function() {
                var n = $('#tbhObatPakan .row').length + 2;
                $.get("{{ route('dashboard_kandang.tbh_obatPakan') }}?count=" + n, function(x) {
                    $('#tbhObatPakan').append(x);
                    $('.select2-edit').select2({
                        dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                    });
                });
            });
            $(document).on('click', '.tbhObatAir', function() {
                var n = $('#tbhObatAir .row').length + 2;
                $.get("{{ route('dashboard_kandang.tbh_obatAir') }}?count=" + n, function(x) {
                    $('#tbhObatAir').append(x);
                    $('.select2-edit').select2({
                        dropdownParent: $('#tambah_perencanaan_harian .modal-content')
                    });
                });
            });

            $(document).on('change', '.obat_pakan_input', function() {
                var id = $(this).val(),
                    row = $(this).closest('.row');
                if (id && id !== 'tambah') $.get("{{ route('dashboard_kandang.get_stok_obat_pakan') }}?id_produk=" + id, function(x) {
                    try {
                        x = JSON.parse(x);
                        row.find('.get_dosis_satuan1').val(x.dosis_satuan);
                        row.find('.get_campuran_satuan1').val(x.campuran_satuan);
                    } catch (e) {}
                });
            });
            $(document).on('change', '.obat_air_input', function() {
                var id = $(this).val(),
                    row = $(this).closest('.row');
                if (id && id !== 'tambah') $.get("{{ route('dashboard_kandang.get_stok_obat_air') }}?id_produk=" + id, function(x) {
                    try {
                        x = JSON.parse(x);
                        row.find('.get_dosis_satuan_air1').val(x.dosis_satuan);
                        row.find('.get_campuran_satuan_air1').val(x.campuran_satuan);
                    } catch (e) {}
                });
            });
            $(document).on('change', '.obat_ayam_input', function() {
                var id = $(this).val(),
                    row = $(this).closest('.row');
                if (id && id !== 'tambah') $.get("{{ route('dashboard_kandang.get_stok_obat_ayam') }}?id_produk=" + id, function(x) {
                    row.find('.get_dosis_satuan_ayam').val(x);
                });
            });

            $(document).on('keyup', '#krng,#gr,.persen', function() {
                var total = 0;
                $('.hasil').each(function() {
                    total += parseFloat($(this).val()) || 0;
                });
                $('#total').val(total.toFixed(2));
                var box = parseFloat($('#krng').val()) || 0;
                $('#krng_f').val(box ? Math.floor(total / (box * 1000)) : 0);
                $('#krng_s').val(box ? ((total / (box * 1000) - Math.floor(total / (box * 1000))) * 10).toFixed(2) : 0);
            });

            function bukaModal(id) {
                var el = document.getElementById(id);
                if (window.bootstrap && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(el).show();
                else $('#' + id).show();
            }

            $(document).on('click', '.btn-detail-harian', function() {
                var b = $(this);
                b.prop('disabled', true);
                $.ajax({
                    url: "{{ route('dashboard_kandang.viewHistoryPerencanaan') }}",
                    data: {
                        tgl: b.data('tgl'),
                        id_kandang: b.data('kandang'),
                        history_page: 1
                    },
                    success: function(r) {
                        $('#load_detail_harian').html(r);
                        bukaModal('detail_harian');
                    },
                    error: function(x) {
                        alert('Detail gagal dimuat: ' + (x.responseJSON?.message || x.statusText));
                    },
                    complete: function() {
                        b.prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.btn-edit-harian', function() {
                var b = $(this);
                b.prop('disabled', true);
                $.ajax({
                    url: "{{ route('dashboard_kandang.viewHistoryEditPerencanaan') }}",
                    data: {
                        tgl: b.data('tgl'),
                        id_kandang: b.data('kandang')
                    },
                    success: function(r) {
                        $('#load_edit_harian').html(r);
                        bukaModal('edit_harian');
                        $('.select2-edit-perencanaan').select2({
                            dropdownParent: $('#edit_harian .modal-content')
                        });
                    },
                    error: function(x) {
                        alert('Form edit gagal dimuat: ' + (x.responseJSON?.message || x.statusText));
                    },
                    complete: function() {
                        b.prop('disabled', false);
                    }
                });
            });
        </script>
    @endsection
</x-theme.app>
