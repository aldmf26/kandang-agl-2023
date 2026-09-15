<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    @php
        $totalPakan = $rows->sum('total_gr') / 1000;
        $totalBiaya = $rows->sum(fn ($row) => $row->rp_pakan + $row->rp_vit);
        $jumlahJurnal = $rows->where('ada_jurnal', true)->count();
    @endphp

    <x-slot name="cardHeader">
        <h5 class="mb-0">{{ $title }}</h5>
    </x-slot>

    <x-slot name="cardBody">
        @if (session()->has('sukses'))
            <div class="alert alert-success">{{ session('sukses') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 bg-light">
                    <div class="card-body py-3">
                        <div class="text-muted small">Jumlah Input</div>
                        <div class="fs-4 fw-bold">{{ number_format($rows->count()) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 bg-light">
                    <div class="card-body py-3">
                        <div class="text-muted small">Total Pakan</div>
                        <div class="fs-4 fw-bold">{{ number_format($totalPakan, 1, ',', '.') }} Kg</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 bg-light">
                    <div class="card-body py-3">
                        <div class="text-muted small">Total Biaya</div>
                        <div class="fs-4 fw-bold">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 border-0 bg-light">
                    <div class="card-body py-3">
                        <div class="text-muted small">Sudah Masuk Jurnal</div>
                        <div class="fs-4 fw-bold">{{ $jumlahJurnal }} / {{ $rows->count() }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard_kandang.input_harian') }}" class="row align-items-end g-3">
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label">Dari</label>
                        <input type="date" name="tgl1" value="{{ $tgl1 }}" class="form-control">
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <label class="form-label">Sampai</label>
                        <input type="date" name="tgl2" value="{{ $tgl2 }}" class="form-control">
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <label class="form-label">Kandang</label>
                        <select name="id_kandang" class="form-control select2">
                            <option value="">Semua kandang</option>
                            @foreach ($kandang as $d)
                                <option value="{{ $d->id_kandang }}" {{ (string) $id_kandang === (string) $d->id_kandang ? 'selected' : '' }}>
                                    {{ $d->nm_kandang }}{{ $d->selesai == 'Y' ? ' (selesai)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-12">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle" id="tableInputHarian">
                <thead>
                    <tr>
                        <th class="dhead text-center">#</th>
                        <th class="dhead text-center">Tanggal</th>
                        <th class="dhead text-center">Kandang</th>
                        <th class="dhead text-center">No Nota</th>
                        <th class="dhead text-end">Pakan (Kg)</th>
                        <th class="dhead text-end">Rp Pakan</th>
                        <th class="dhead text-end">Rp Vitamin/Obat</th>
                        <th class="dhead text-end">Total Rp</th>
                        <th class="dhead text-center">Jurnal</th>
                        <th class="dhead text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($rows as $i => $row)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td class="text-center text-nowrap">{{ tanggal($row->tgl) }}</td>
                            <td><span class="fw-semibold">{{ $row->nm_kandang }}</span></td>
                            <td class="text-center"><span class="badge bg-light text-dark border">{{ $row->no_nota }}</span></td>
                            <td class="text-end">{{ number_format($row->total_gr / 1000, 1) }}</td>
                            <td class="text-end">{{ number_format($row->rp_pakan, 0) }}</td>
                            <td class="text-end">{{ number_format($row->rp_vit, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($row->rp_pakan + $row->rp_vit, 0) }}</td>
                            <td class="text-center">
                                @if ($row->ada_jurnal)
                                    <span class="badge bg-success">Masuk</span>
                                @else
                                    <span class="badge bg-secondary">Belum</span>
                                @endif
                                @if ($row->sudah_cek)
                                    <span class="badge bg-warning">Dicek</span>
                                @endif
                            </td>
                            <td class="text-center" style="white-space: nowrap">
                                <button type="button" class="btn btn-sm btn-outline-info btn-detail-harian"
                                    tgl="{{ $row->tgl }}" id_kandang="{{ $row->id_kandang }}">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                @if (!$row->sudah_cek)
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-edit-harian"
                                        tgl="{{ $row->tgl }}" id_kandang="{{ $row->id_kandang }}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <form method="POST" action="{{ route('dashboard_kandang.hapus_perencanaan') }}"
                                        class="d-inline form-hapus-harian" data-kandang="{{ $row->nm_kandang }}"
                                        data-tgl="{{ tanggal($row->tgl) }}">
                                        @csrf
                                        <input type="hidden" name="tgl" value="{{ $row->tgl }}">
                                        <input type="hidden" name="id_kandang" value="{{ $row->id_kandang }}">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                @else
                                    <span class="small text-muted d-block mt-1">Terkunci setelah dibukukan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada input harian pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <small class="text-muted">Hapus juga menghapus jurnal Pemakaian Pakan Harian (PPH-tgl-kandang) terkait. Data yang sudah dicek admin tidak bisa diedit/dihapus.</small>
    </x-slot>

    <x-theme.modal title="Detail Perencanaan" btnSave="T" size="modal-lg-max" idModal="detail_harian">
        <div id="load_detail_harian"></div>
    </x-theme.modal>

    <form action="{{ route('dashboard_kandang.edit_perencanaan') }}" method="post">
        @csrf
        <x-theme.modal title="Edit Perencanaan" size="modal-lg" idModal="edit_harian">
            <div id="load_edit_harian"></div>
        </x-theme.modal>
    </form>

    @section('js')
        <script>
            function showModalHarian(id) {
                console.log('showModalHarian called with id:', id);
                var el = document.getElementById(id);
                if (!el) {
                    console.error('Modal element not found:', id);
                    alert('Modal #' + id + ' tidak ditemukan di DOM');
                    return;
                }
                console.log('Modal element found:', el);
                try {
                    if (window.bootstrap && bootstrap.Modal) {
                        var m = bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el, { backdrop: 'static', keyboard: false });
                        m.show();
                        console.log('Bootstrap 5 modal shown');
                    } else if (typeof $ !== 'undefined' && $.fn.modal) {
                        $('#' + id).modal('show');
                        console.log('jQuery modal shown');
                    } else {
                        console.error('No modal library available');
                        alert('Library modal tidak tersedia');
                    }
                } catch (e) {
                    console.error('Modal error:', e);
                    alert('Gagal buka modal: ' + e.message);
                }
            }

            $(document).ready(function() {
                $('.select2').select2();
                $('#tableInputHarian').DataTable({
                    "paging": true,
                    "pageLength": 25,
                    "lengthChange": true,
                    "stateSave": true,
                    "searching": true,
                    "ordering": false,
                });
            });

            $(document).on('click', '.btn-detail-harian', function() {
                var tombol = $(this);
                var tgl = $(this).attr('tgl');
                var id_kandang = $(this).attr('id_kandang');
                tombol.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat');
                $.ajax({
                    type: "GET",
                    url: "{{ route('dashboard_kandang.viewHistoryPerencanaan') }}",
                    data: {
                        tgl: tgl,
                        id_kandang: id_kandang,
                        history_page: 1
                    },
                    success: function(r) {
                        $('#load_detail_harian').html(r);
                        showModalHarian('detail_harian');
                    },
                    error: function(xhr) {
                        console.error('Detail AJAX error:', xhr.status, xhr.responseText);
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
                        alert('Detail gagal dimuat: ' + msg);
                    },
                    complete: function() {
                        tombol.prop('disabled', false).html('<i class="fas fa-eye"></i> Detail');
                    }
                });
            });

            $(document).on('click', '.btn-edit-harian', function() {
                var tombol = $(this);
                var tgl = $(this).attr('tgl');
                var id_kandang = $(this).attr('id_kandang');
                tombol.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memuat');
                $.ajax({
                    type: "GET",
                    url: "{{ route('dashboard_kandang.viewHistoryEditPerencanaan') }}",
                    data: {
                        tgl: tgl,
                        id_kandang: id_kandang
                    },
                    success: function(r) {
                        $('#load_edit_harian').html(r);
                        showModalHarian('edit_harian');
                        $('.select2-edit-perencanaan').select2({
                            dropdownParent: $('#edit_harian .modal-content')
                        });
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
                        alert('Form edit gagal dimuat: ' + msg);
                    },
                    complete: function() {
                        tombol.prop('disabled', false).html('<i class="fas fa-edit"></i> Edit');
                    }
                });
            });

            $(document).on('submit', '.form-hapus-harian', function(e) {
                var kandang = $(this).data('kandang');
                var tgl = $(this).data('tgl');
                if (!confirm('Hapus input harian ' + kandang + ' tanggal ' + tgl + ' beserta stok dan jurnalnya?')) {
                    e.preventDefault();
                }
            });

            $(document).on('click', '.tbhObatPakanEdit', function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('dashboard_kandang.load_obat_pakan') }}",
                    success: function(r) {
                        $("#loadTbhObatPakanEdit").append(r);
                    }
                });
            });

            $(document).on('click', '.tbhObatAirEdit', function() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('dashboard_kandang.load_obat_air') }}",
                    success: function(r) {
                        $("#loadTbhObatAirEdit").append(r);
                    }
                });
            });
        </script>
    @endsection
</x-theme.app>
