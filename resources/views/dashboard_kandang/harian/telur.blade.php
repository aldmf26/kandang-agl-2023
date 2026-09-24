<x-theme.app title="{{ $title }}" table="Y" sizeCard="8" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <h5 class="mb-0"><i class="fas fa-egg me-2"></i>{{ $title }}</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard_kandang.harian') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Harian
                </a>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambah_telur_harian">
                    <i class="fas fa-plus me-1"></i> Input Telur
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
                <form method="GET" action="{{ route('dashboard_kandang.telur') }}" class="row g-3 align-items-end">
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
                        <th class="dhead text-end">Pcs</th>
                        <th class="dhead text-end">Kg</th>
                        <th class="dhead text-center" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $i => $row)
                        <tr>
                            <td class="text-center">{{ $rows->firstItem() + $i }}</td>
                            <td class="text-center text-nowrap">{{ tanggal($row->tgl) }}</td>
                            <td class="fw-semibold">{{ $row->nm_kandang }}</td>
                            <td class="text-end">{{ number_format($row->ttl_pcs, 0) }}</td>
                            <td class="text-end">{{ number_format($row->ttl_kg, 1, ',', '.') }}</td>
                            <td class="text-center text-nowrap">
                                <button type="button" class="btn btn-sm btn-info btn-detail-telur" data-tgl="{{ $row->tgl }}" data-kandang="{{ $row->id_kandang }}" title="Detail per produk">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if ($row->cek === 'Y')
                                    Dicek
                                @elseif($row->opname === 'Y')
                                    Opname
                                @else
                                    <button type="button" class="btn btn-sm btn-warning btn-edit-telur" data-tgl="{{ $row->tgl }}" data-kandang="{{ $row->id_kandang }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data telur pada periode ini.</td>
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
        <small class="text-muted">Satu baris = satu tanggal satu kandang. Data yang sudah dicek tidak bisa diedit.</small>

        <form action="{{ route('dashboard_kandang.tambah_telur') }}" method="post">
            @csrf
            <x-theme.modal title="Input Telur" size="modal-lg" idModal="tambah_telur_harian">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal</label>
                        <input type="date" id="tgl_tambah_telur" value="{{ date('Y-m-d') }}" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kandang</label>
                        <select id="pilih_kandang_telur" class="form-control">
                            <option value="">- Pilih Kandang -</option>
                            @foreach ($kandang as $k)
                                <option value="{{ $k->id_kandang }}">{{ $k->nm_kandang }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="load_tambah_telur">
                    <div class="alert alert-info mb-0">Pilih kandang untuk memuat form input telur.</div>
                </div>
            </x-theme.modal>
        </form>

        <x-theme.modal title="Detail Telur" btnSave="T" size="modal-lg" idModal="detail_telur_harian">
            <div id="load_detail_telur_harian"></div>
        </x-theme.modal>

        <form action="{{ route('dashboard_kandang.tambah_telur') }}" method="post">
            @csrf
            <x-theme.modal title="Edit Telur" size="modal-lg" idModal="edit_telur_harian">
                <div id="load_edit_telur_harian"></div>
            </x-theme.modal>
        </form>
    </x-slot>

    @section('js')
        <script>
            $(function() {
                $('.select2').select2();
            });

            function muatFormTelur(target, idKandang, tgl) {
                $(target).html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Memuat form...</div>');
                $.get("{{ url('dashboard_kandang/load_telur') }}/" + idKandang, {
                    tgl: tgl
                }, function(r) {
                    $(target).html(r);
                }).fail(function() {
                    $(target).html('<div class="alert alert-danger mb-0">Form gagal dimuat.</div>');
                });
            }

            function bukaModalTelur(id) {
                var el = document.getElementById(id);
                if (window.bootstrap && bootstrap.Modal) bootstrap.Modal.getOrCreateInstance(el).show();
                else $('#' + id).show();
            }

            $(document).on('change', '#pilih_kandang_telur, #tgl_tambah_telur', function() {
                var id = $('#pilih_kandang_telur').val();
                if (!id) {
                    $('#load_tambah_telur').html('<div class="alert alert-info mb-0">Pilih kandang untuk memuat form input telur.</div>');
                    return;
                }
                muatFormTelur('#load_tambah_telur', id, $('#tgl_tambah_telur').val());
            });

            $(document).on('click', '.btn-detail-telur', function() {
                var b = $(this);
                b.prop('disabled', true);
                $.ajax({
                    url: "{{ route('dashboard_kandang.detail_telur') }}",
                    data: {
                        tgl: b.data('tgl'),
                        id_kandang: b.data('kandang')
                    },
                    success: function(r) {
                        $('#load_detail_telur_harian').html(r);
                        bukaModalTelur('detail_telur_harian');
                    },
                    error: function(x) {
                        alert('Detail gagal dimuat: ' + (x.responseJSON?.message || x.statusText));
                    },
                    complete: function() {
                        b.prop('disabled', false);
                    }
                });
            });

            $(document).on('click', '.btn-edit-telur', function() {
                var b = $(this);
                muatFormTelur('#load_edit_telur_harian', b.data('kandang'), b.data('tgl'));
                bukaModalTelur('edit_telur_harian');
            });
        </script>
    @endsection
</x-theme.app>
