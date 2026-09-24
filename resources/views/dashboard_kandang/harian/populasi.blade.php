<x-theme.app title="{{ $title }}" table="Y" sizeCard="8" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <h5 class="mb-0"><i class="fas fa-kiwi-bird me-2"></i>{{ $title }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard_kandang.harian') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Harian
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambah_populasi_harian">
                    <i class="fas fa-plus me-1"></i> Input Populasi
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
                <form method="GET" action="{{ route('dashboard_kandang.populasi') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Dari</label>
                        <input type="date" name="tgl1" value="{{ $tgl1 }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Sampai</label>
                        <input type="date" name="tgl2" value="{{ $tgl2 }}" class="form-control">
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
                        <th class="dhead text-end">Mati</th>
                        <th class="dhead text-end">Jual</th>
                        <th class="dhead text-end">Afkir</th>
                        <th class="dhead text-end">Total Kurang</th>
                        <th class="dhead text-center" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td class="text-center">{{ $rows->firstItem() + $i }}</td>
                            <td class="text-center text-nowrap">{{ tanggal($row->tgl) }}</td>
                            <td class="text-end">{{ number_format($row->ttl_mati ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($row->ttl_jual ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($row->ttl_afkir ?? 0, 0) }}</td>
                            <td class="text-end">{{ number_format($row->ttl_kurang ?? 0, 0) }}</td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="btn btn-sm btn-info btn-detail-populasi" data-tgl="{{ $row->tgl }}" title="Detail per kandang">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-warning btn-edit-populasi" data-tgl="{{ $row->tgl }}" title="Edit semua kandang tanggal ini">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Belum ada data populasi pada periode ini.</td>
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
        <small class="text-muted">Satu baris = satu tanggal, mencakup semua kandang. Edit membuka form semua kandang untuk tanggal tersebut.</small>

        <form action="{{ route('dashboard_kandang.tambah_populasi') }}" method="post">
            @csrf
            <x-theme.modal title="Input Populasi" size="modal-lg" idModal="tambah_populasi_harian">
                <div id="load_populasi_harian">
                    <div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Memuat form...</div>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.modal title="Detail Populasi" btnSave="T" size="modal-lg" idModal="detail_populasi_harian">
            <div id="load_detail_populasi_harian"></div>
        </x-theme.modal>

        <form action="{{ route('dashboard_kandang.tambah_populasi') }}" method="post">
            @csrf
            <input type="hidden" name="tgl_lama" id="tgl_lama_edit" value="">
            <x-theme.modal title="Edit Populasi" size="modal-lg" idModal="edit_populasi_harian">
                <div id="load_edit_populasi_harian"></div>
            </x-theme.modal>
        </form>
    </x-slot>

    @section('js')
        <script>
            function muatFormPopulasi(target, tgl) {
                $(target).html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Memuat form...</div>');
                $.get("{{ url('dashboard_kandang/load_populasi') }}/0", {
                    tgl: tgl
                }, function(r) {
                    $(target).html(r);
                }).fail(function() {
                    $(target).html('<div class="alert alert-danger mb-0">Form gagal dimuat.</div>');
                });
            }

            $(document).on('shown.bs.modal', '#tambah_populasi_harian', function() {
                muatFormPopulasi('#load_populasi_harian', "{{ date('Y-m-d') }}");
            });

            $(document).on('click', '.btn-detail-populasi', function() {
                var b = $(this);
                b.prop('disabled', true);
                $.ajax({
                    url: "{{ route('dashboard_kandang.detail_populasi') }}",
                    data: {
                        tgl: b.data('tgl')
                    },
                    success: function(r) {
                        $('#load_detail_populasi_harian').html(r);
                        var el = document.getElementById('detail_populasi_harian');
                        if (window.bootstrap && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(el).show();
                        else $('#detail_populasi_harian').show();
                    },
                    error: function(x) {
                        alert('Detail gagal dimuat: ' + (x.responseJSON?.message || x.statusText));
                    },
                    complete: function() {
                        b.prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.btn-edit-populasi', function() {
                var tgl = $(this).data('tgl');
                $('#tgl_lama_edit').val(tgl);
                muatFormPopulasi('#load_edit_populasi_harian', tgl);
                var el = document.getElementById('edit_populasi_harian');
                if (window.bootstrap && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(el).show();
                else $('#edit_populasi_harian').show();
            });

            $(document).on('change', '#tglPopulasiSemua', function() {
                $('.tgl-populasi').val($(this).val());
            });
            $(document).on('focus', '.selectAll', function() {
                $(this).select();
            });
        </script>
    @endsection
</x-theme.app>
