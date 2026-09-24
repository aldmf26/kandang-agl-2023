<x-theme.app title="{{ $title }}" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <h5 class="mb-0">{{ $title }}</h5>
            <a href="{{ route('dashboard_kandang.laporan') }}" class="btn btn-outline-primary btn-sm no-print">
                <i class="fas fa-arrow-left me-1"></i> Laporan
            </a>
        </div>
    </x-slot>

    <x-slot name="cardBody">
        @php
            $tanggalLaporan = request()->get('tglKandang') ?? date('Y-m-d');
            $tanggalSebelumnya = date('Y-m-d', strtotime($tanggalLaporan . ' -1 day'));
            $tanggalBerikutnya = date('Y-m-d', strtotime($tanggalLaporan . ' +1 day'));
        @endphp
        <style>
            .report-toolbar { border: 1px solid #e3e9f3; border-radius: 14px; background: linear-gradient(135deg, #f8faff 0%, #fff 65%); box-shadow: 0 5px 18px rgba(31, 54, 105, .06); }
            .report-title { color: #18366f; font-size: 20px; font-weight: 750; }
            .report-subtitle { color: #7a879d; font-size: 13px; }
            .report-table-card { overflow: hidden; border: 1px solid #e2e7f0; border-radius: 14px; background: #fff; box-shadow: 0 8px 24px rgba(31, 54, 105, .07); }
            .report-table-scroll { overflow-x: auto; scrollbar-color: #aab8d6 #edf1f7; scrollbar-width: thin; }
            .report-table-scroll::-webkit-scrollbar { height: 10px; }
            .report-table-scroll::-webkit-scrollbar-track { background: #edf1f7; }
            .report-table-scroll::-webkit-scrollbar-thumb { border: 2px solid #edf1f7; border-radius: 10px; background: #aab8d6; }
            .laporan-biasa { min-width: 100%; }
            .laporan-biasa > div { width: 100%; padding: 0; }
            .laporan-biasa h6 { display: none; }
            .laporan-biasa table { min-width: 1450px; margin: 0; border-collapse: separate; border-spacing: 0; }
            .laporan-biasa thead, .laporan-biasa thead tr { background-color: #3457b2 !important; }
            .laporan-biasa thead th { position: sticky; z-index: 3; top: 0; height: 39px; padding: 5px 7px !important; border-color: #7189c7 !important; background-color: #3457b2 !important; color: #fff !important; font-size: 11px; line-height: 1.2; vertical-align: middle; }
            .laporan-biasa thead tr:first-child th { border-right: 2px solid rgba(255,255,255,.45) !important; }
            .laporan-biasa thead tr:first-child th { height: 39px; padding-top: 5px !important; padding-bottom: 5px !important; }
            .laporan-biasa thead tr:nth-child(2) th { top: 39px; }
            .laporan-biasa tbody td, .laporan-biasa tbody th, .laporan-biasa tfoot th { padding: 6px 7px !important; border-color: #e6eaf1 !important; background-color: #fff !important; color: #263044 !important; font-size: 11px !important; font-variant-numeric: tabular-nums; vertical-align: middle; }
            .laporan-biasa tbody tr:nth-child(even) td { background-color: #f8faff !important; }
            .laporan-biasa tbody tr:not(:last-child) td { border-bottom: 2px solid #d7deeb !important; }
            .laporan-biasa tbody td:nth-child(4), .laporan-biasa tbody td:nth-child(7) { border-right: 2px solid #aebbd4 !important; }
            .laporan-biasa .group-divider-right { border-right: 2px solid #aebbd4 !important; }
            .laporan-biasa tbody tr:hover td { background-color: #eef3ff !important; }
            .laporan-biasa tbody td:first-child, .laporan-biasa thead th:first-child, .laporan-biasa tfoot th:first-child { position: sticky; left: 0; z-index: 2; box-shadow: 4px 0 8px rgba(39, 57, 99, .08); }
            .laporan-biasa thead th:first-child { z-index: 5; }
            .laporan-biasa tfoot th { position: sticky; bottom: 0; z-index: 2; border-top: 2px solid #3457b2 !important; background-color: #eef3ff !important; color: #18366f !important; }
            .laporan-biasa tfoot th, .laporan-biasa tfoot th:first-child { position: static; z-index: auto; box-shadow: none !important; border: 1px solid #aeb7c6 !important; border-top: 2px solid #3457b2 !important; }
            .laporan-biasa .putih, .laporan-biasa .abu, .laporan-biasa .abuGelap { background-color: inherit !important; color: #263044 !important; }
            .laporan-biasa tbody td a, .laporan-biasa tbody th a { color: inherit !important; text-decoration: none !important; }
            .laporan-biasa .merah, .laporan-biasa .text-danger { color: #d92d20 !important; font-weight: 700; }
            .laporan-biasa [data-bs-toggle="modal"], .laporan-biasa .tambah_telur, .laporan-biasa .tambah_populasi, .laporan-biasa .detail_perencanaan, .laporan-biasa .rumus { pointer-events: none !important; cursor: default !important; }
            .report-legend { color: #718096; font-size: 12px; }
            .report-legend__danger { display: inline-block; width: 9px; height: 9px; margin-right: 5px; border-radius: 50%; background: #d92d20; }
            .print-only { display: none; }
            @media (max-width: 767.98px) {
                .report-toolbar .form-control, .report-toolbar .btn { min-height: 42px; }
                .report-toolbar__actions { width: 100%; }
                .report-toolbar__actions .btn { flex: 1; }
            }
            @media print {
                @page { size: A3 landscape; margin: 8mm; }
                body { background: #fff !important; }
                body > header, header.mb-5, .header-top, .main-navbar, footer, .no-print, .modal, .modal-backdrop, .print-only { display: none !important; }
                .content-wrapper, .page-content { margin: 0 !important; padding: 0 !important; }
                .content-wrapper .card { border: 0 !important; box-shadow: none !important; }
                .content-wrapper .card > .card-header { display: none !important; }
                .content-wrapper .card > .card-body { padding: 0 !important; }
                #print-area { position: static !important; }
                .report-table-card { overflow: visible; border: 0; border-radius: 0; box-shadow: none; }
                .report-table-scroll { overflow: visible; }
                .laporan-biasa table { width: 100% !important; min-width: 0 !important; font-size: 10.5px !important; line-height: 1.25 !important; table-layout: auto; }
                .laporan-biasa th:nth-last-child(2), .laporan-biasa td:nth-last-child(2) { width: 20% !important; max-width: 20% !important; }
                .laporan-biasa th:last-child, .laporan-biasa td:last-child { width: 28% !important; max-width: 28% !important; }
                .laporan-biasa thead { display: table-header-group; }
                .laporan-biasa tfoot { display: table-row-group; }
                .laporan-biasa tr { break-inside: avoid; }
                .laporan-biasa thead th, .laporan-biasa tbody td, .laporan-biasa tfoot th { position: static !important; padding: 3px 4px !important; border: 1px solid #aeb7c6 !important; box-shadow: none !important; word-break: normal !important; overflow-wrap: normal !important; }
                .laporan-biasa thead th { background: #3457b2 !important; color: #fff !important; font-size: 10.5px !important; white-space: nowrap !important; hyphens: none !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                .laporan-biasa tbody td, .laporan-biasa tfoot th { font-size: 10.5px !important; }
                .laporan-biasa tbody tr:nth-child(even) td, .laporan-biasa tfoot th { background: #f1f4fa !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                .laporan-biasa tbody tr:not(:last-child) td { border-bottom: 1px solid #aeb7c6 !important; }
            }
        </style>
        <div id="print-area">
            <div class="print-only mb-3">
                <h3 class="mb-1">{{ $title }}</h3>
                <div>Tanggal: {{ tanggal($tanggalLaporan) }}</div>
            </div>

            <div class="report-toolbar mb-3 no-print">
                <div class="card-body p-3 p-lg-4">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                        <div>
                            <div class="report-title">Ringkasan Operasional Kandang</div>
                            <div class="report-subtitle">Populasi, produksi telur, pakan, dan obat dalam satu tampilan.</div>
                        </div>
                        <div class="d-flex gap-2 report-toolbar__actions">
                            <a href="{{ route('dashboard_kandang.laporan_kandang_harian', ['tglKandang' => $tanggalSebelumnya]) }}" class="btn btn-outline-secondary" title="Hari sebelumnya"><i class="fas fa-chevron-left"></i></a>
                            <a href="{{ route('dashboard_kandang.laporan_kandang_harian', ['tglKandang' => $tanggalBerikutnya]) }}" class="btn btn-outline-secondary" title="Hari berikutnya"><i class="fas fa-chevron-right"></i></a>
                            <button type="button" class="btn btn-success" onclick="window.print()"><i class="fas fa-print me-1"></i> Cetak</button>
                        </div>
                    </div>
                    <form method="GET" action="{{ route('dashboard_kandang.laporan_kandang_harian') }}" class="row g-2 align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <label class="form-label fw-semibold mb-1">Tanggal laporan</label>
                            <input type="date" name="tglKandang" value="{{ $tanggalLaporan }}" class="form-control">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan</button>
                        </div>
                        <div class="col-md-auto ms-md-auto report-legend pb-md-2">
                            <span class="report-legend__danger"></span> Angka merah perlu perhatian
                        </div>
                    </form>
                </div>
            </div>

            <div class="report-table-card">
                <div class="report-table-scroll">
                    <section class="row g-0 laporan-biasa">
                        @include('dashboard_kandang.tabel.inputKandangHarian', ['modeLaporan' => true])
                    </section>
                </div>
            </div>
        </div>
    </x-slot>
</x-theme.app>
