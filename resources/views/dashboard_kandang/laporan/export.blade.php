<x-theme.app title="{{ $title }}" table="Y" sizeCard="8" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <h5 class="mb-0"><i class="fas fa-file-export me-2"></i>{{ $title }}</h5>
            <a href="{{ route('dashboard_kandang.laporan') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Laporan
            </a>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        <div class="card mb-3 border-0 shadow-sm">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label" for="laporan_kandang">Kandang</label>
                        <select id="laporan_kandang" class="form-control select2">
                            @foreach ($kandang as $k)
                                <option value="{{ $k->id_kandang }}">{{ $k->nm_kandang }}{{ $k->selesai == 'Y' ? ' (selesai)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th class="dhead text-center" style="width:40px">#</th>
                        <th class="dhead text-center">Laporan</th>
                        <th class="dhead text-center" style="width:150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">1</td>
                        <td>
                            <span class="fw-semibold">Daily Layer</span>
                            <br><small class="text-muted">Produksi harian per kandang (Excel).</small>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('dashboard_kandang.daily_layer') }}" method="post" class="d-inline">
                                @csrf
                                <input type="hidden" name="id_kandang" class="laporan-id-kandang">
                                <button class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>Export</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">2</td>
                        <td>
                            <span class="fw-semibold">Weekly Layer</span>
                            <br><small class="text-muted">Produksi mingguan per kandang (Excel).</small>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('dashboard_kandang.week_layer') }}" method="post" class="d-inline">
                                @csrf
                                <input type="hidden" name="id_kandang" class="laporan-id-kandang">
                                <button class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>Export</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">3</td>
                        <td>
                            <span class="fw-semibold">Perencanaan</span>
                            <br><small class="text-muted">Seluruh data pakan dan obat perencanaan (Excel).</small>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('dashboard_kandang.export_perencanaan') }}" class="btn btn-sm btn-primary"><i class="fas fa-download me-1"></i>Export</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <small class="text-muted">Pilih kandang dahulu untuk Daily / Weekly Layer.</small>
    </x-slot>

    @section('js')
        <script>
            $(function() {
                $('.select2').select2();
                $('.laporan-id-kandang').val($('#laporan_kandang').val());
                $('#laporan_kandang').on('change', function() {
                    $('.laporan-id-kandang').val($(this).val());
                });
            });
        </script>
    @endsection
</x-theme.app>
