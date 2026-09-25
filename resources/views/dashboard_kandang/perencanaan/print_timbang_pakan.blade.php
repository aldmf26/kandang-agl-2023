<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Timbang Pakan</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            line-height: 1.2;
            color: #111;
            background: #fff;
            padding: 6px;
        }

        .header-bar {
            display: flex;
            align-items: center;
            border: 1px solid #333;
            background: #f8f9fa;
            margin-bottom: 6px;
            padding: 4px 8px;
        }

        .header-bar .title {
            font-size: 12px;
            font-weight: bold;
            flex: 1;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-bar .date-info {
            font-size: 11px;
            font-weight: bold;
            color: #2c3e50;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }

        .kandang-card {
            border: 1px solid #444;
            border-radius: 3px;
            overflow: hidden;
            background: #fff;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .kandang-header {
            background: #2c3e50;
            color: #ffffff;
            font-weight: bold;
            font-size: 11px;
            padding: 3px 6px;
            text-align: center;
            letter-spacing: 0.5px;
        }

        table.tbl-data {
            width: 100%;
            border-collapse: collapse;
        }

        table.tbl-data th,
        table.tbl-data td {
            border: 1px solid #ccc;
            padding: 2px 4px;
            font-size: 10px;
        }

        table.tbl-data th {
            background: #eaeaea;
            font-weight: bold;
            text-align: left;
        }

        table.tbl-data td.num {
            text-align: right;
        }

        .row-total td {
            font-weight: bold;
            background: #f2f2f2;
        }

        .row-karung td {
            background: #fffbe6;
            font-weight: 600;
            color: #333;
            font-size: 9.5px;
        }

        .section-obat-title {
            background: #eef6fb;
            color: #1a5276;
            font-weight: bold;
            font-size: 10px;
            padding: 2px 5px;
            border-top: 1px solid #444;
            border-bottom: 1px solid #ccc;
        }

        .no-data {
            text-align: center;
            color: #777;
            padding: 20px;
            font-style: italic;
        }

        @media print {
            body {
                padding: 0;
                font-size: 9.5px;
                zoom: 96%;
            }

            .no-print {
                display: none !important;
            }

            .grid-container {
                gap: 5px;
            }

            @page {
                size: portrait;
                margin: 4mm 5mm;
            }
        }

        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 6px 14px;
            background: #1a5276;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .print-btn:hover {
            background: #154360;
        }
    </style>
</head>

<body>

    <button class="print-btn no-print" onclick="window.print()">Print Document</button>

    @php
        $hariArr = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $namaHari = $hariArr[date('l', strtotime($tgl))] ?? '';
    @endphp

    <div class="header-bar">
        <div class="title">Perencanaan Timbang Pakan</div>
        <div class="date-info">Tanggal: {{ $namaHari }}, {{ tanggal($tgl) }}</div>
    </div>

    @if (empty($datakandang))
        <div class="no-data">
            Tidak ada data perencanaan untuk tanggal {{ $namaHari }}, {{ tanggal($tgl) }}.
        </div>
    @else

        <div class="grid-container">
            @foreach ($datakandang as $dk)
                @php
                    $kd = $dk['kandang'];
                    $karung = $dk['karung'];
                    $pakanList = $dk['pakan_list'];
                    $obatPakan = $dk['obat_pakan'];
                    $totalGr = $dk['total_gr'];
                    $totalKg = $totalGr / 1000;
                @endphp

                <div class="kandang-card">
                    <div class="kandang-header">
                        KANDANG {{ $kd->nm_kandang }}
                    </div>

                    {{-- Tabel Pakan --}}
                    <table class="tbl-data">
                        <thead>
                            <tr>
                                <th>Nama Pakan</th>
                                <th style="width: 65px; text-align: right;">Qty</th>
                                <th style="width: 40px;">Satuan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pakanList as $p)
                                <tr>
                                    <td>{{ $p->nm_pakan }}</td>
                                    <td class="num">{{ number_format($p->gr_pakan / 1000, 1) }}</td>
                                    <td>KG</td>
                                </tr>
                            @endforeach

                            <tr class="row-total">
                                <td>Total Pakan</td>
                                <td class="num">{{ number_format($totalKg, 1) }}</td>
                                <td>KG</td>
                            </tr>

                            @if (!empty($karung) && (!empty($karung->karung) || !empty($karung->gr)))
                                <tr class="row-karung">
                                    <td colspan="3">
                                        @if (!empty($karung->gr) && !empty($karung->karung))
                                            {{ number_format($karung->gr, 0) }} Karung @ {{ number_format($karung->karung, 1) }} Kg
                                        @endif
                                        @if (!empty($karung->gr2) && $karung->gr2 > 0)
                                            | 1 Karung @ {{ number_format($karung->gr2, 1) }} Kg
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                    {{-- Tabel Obat / Vitamin --}}
                    @if (count($obatPakan) > 0)
                        <div class="section-obat-title">Obat / Vitamin</div>
                        <table class="tbl-data">
                            <thead>
                                <tr>
                                    <th>Nama Obat</th>
                                    <th style="width: 65px; text-align: right;">Qty</th>
                                    <th style="width: 40px;">Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($obatPakan as $o)
                                    @php
                                        $campuran = (float) ($o->campuran ?? 1);
                                        $dosis = (float) ($o->dosis ?? 0);
                                        $qtyObat = $campuran > 0 ? ($totalKg / $campuran) * $dosis : 0;
                                    @endphp
                                    <tr>
                                        <td style="color: #1a5276; font-weight: 600;">{{ $o->nm_produk }}</td>
                                        <td class="num">{{ number_format($qtyObat, 1) }}</td>
                                        <td>{{ $o->satuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            @endforeach
        </div>

    @endif

</body>

</html>