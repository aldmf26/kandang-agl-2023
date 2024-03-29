<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>

<body>
    @php
        $font = DB::table('font_size')->first()->font;
        $tglHariIniNih = request()->get('tglKandang') ?? date('Y-m-d');
    @endphp
    <div class="row">
        <div class="col-lg-8">
            <table class="table table-bordered text-center " id=""style="font-size: {{ $font }}px; "
                width="80%">
                <thead class="thead-light">
                    <tr>
                        @php
                            $bgZona = '#f6f0f0';
                        @endphp
                        <th rowspan="2" width="1%" class="text-center dhead">
                            Kdg <br> chick in <br>
                            Afkir <br>
                            chick in2
                        </th>
                        <th style="background-color: {{ $bgZona }} !important" colspan="3"
                            class="text-center  putih">
                            Populasi</th>
                        <th colspan="9" class="text-center abu"> Telur </th>
                        <th style="background-color: {{ $bgZona }} !important" colspan="3"
                            class="text-center putih">
                            pakan</th>

                    </tr>

                    <tr>
                        <th width="3%" class="dhead text-center">Mgg (85)<br>
                        </th>
                        <th width="3%" class="dhead text-center">D <br>C <br> Afkir <br> Week<br>
                        </th>
                        <th width="1%" class="dhead text-center">pop <br>awal <br> akhir</th>
                        <th width="4%" class="dhead text-center">kg bersih <br> butir <br> kg kotor
                        </th>
                        <th width="4%" class="dhead text-center">gr / p <br> (butir) <br>
                        </th>
                        <th width="4%" class="dhead text-center">selisih <br> kg <br> butir<br></th>
                        @php
                            $telur = DB::table('telur_produk')->get();
                        @endphp
                        @foreach ($telur as $d)
                            <th width="1%" class="dhead text-center">
                                {{ ucwords(str_replace('telur', '', strtolower($d->nm_telur))) }}</th>
                        @endforeach

                        <th width="1%" class="dhead text-center">Kg</th>
                        <th width="3%" class="dhead text-center">Gr / Ekor <i
                                class="fas text-white fa-question-circle rumus" rumus="grEkor"
                                style="cursor: pointer"></i></th>
                        <th width="10" class="dhead text-center">Pakan Obat/vit</th>

                    </tr>
                </thead>
                <tbody class="text-end">
                    @php
                        $ayam_awal = 0;
                        $ayam_akhir = 0;
                        $kg = 0;
                        $kgTtl = 0;
                        $kg_kotor = 0;
                        $pcs = 0;
                        $pcsTtl = 0;
                        $kg_today = 0;
                        $butir = 0;
                        $gr_butir = 0;

                        $total_populasi = 0;
                        $total_mati = 0;
                        $total_jual = 0;
                        $total_ayamAfkir = 0;
                        $total_kilo = 0;
                        $total_kilo_kemaren = 0;
                        $total_kg_pakan = 0;
                        $dc_week = 0;
                        $totalGrPcs = 0;
                        $total_pcs_kemarin_dan_hariini = 0;
                    @endphp
                    @foreach ($kandang as $no => $d)
                        <tr>
                            <td align="center" class="detail_perencanaan" id_kandang="{{ $d->id_kandang }}"
                                data-bs-toggle="modal" data-bs-target="#detail_perencanaan">
                                {{ $d->nm_kandang }} <br>
                                {{ date('d/m/y', strtotime($d->chick_in)) }} <br>

                                @php
                                    $kgTtl += empty($d->pcs) ? '0' : $d->kg - $d->pcs / 180;
                                    $kg_kotor += empty($d->pcs) ? '0' : $d->kg;
                                    $pcsTtl += $d->pcs;
                                    $gr_butir += empty($d->pcs)
                                        ? '0'
                                        : number_format((($d->kg - $d->pcs / 180) * 1000) / $d->pcs, 0);
                                    $kg_today += $d->kg - $d->pcs / 180 - ($d->kg_past - $d->pcs_past / 180);
                                    $butir += $d->pcs - $d->pcs_past;
                                    $dc_week += $d->mati_week + $d->jual_week;
                                    $ayam_awal += $d->stok_awal;
                                    $ayam_akhir += $d->stok_awal - $d->pop_kurang;
                                    $chick_in_next = date('Y-m-d', strtotime($d->chick_out . ' +1 month'));
                                    $merah = date('Y-m-d', strtotime($chick_in_next . ' -15 weeks'));
                                    $tgl_hari_ini = date('Y-m-d');
                                    $afkir = date('Y-m-d', strtotime($d->chick_out . ' -4 weeks'));
                                    $ckin2 = date('Y-m-d', strtotime($d->tgl_masuk . ' -20 weeks'));

                                @endphp

                                <span class="{{ $tgl_hari_ini >= $afkir ? 'text-danger fw-bold' : '' }}">
                                    {{ date('d/m/y', strtotime($d->chick_out)) }} </span> <br>

                                <span class="{{ $tgl_hari_ini >= $ckin2 ? 'text-danger fw-bold' : '' }}">
                                    {{ date('d/m/y', strtotime($d->tgl_masuk)) }}
                                </span>

                            </td>
                            @php
                                $populasi = DB::table('populasi')
                                    ->where([['id_kandang', $d->id_kandang], ['tgl', $tglHariIniNih]])
                                    ->first();

                                $mati = $populasi->mati ?? 0;
                                $jual = $populasi->jual ?? 0;
                                $ayamAfkir = $populasi->afkir ?? 0;
                                $kelas = $mati > 3 ? 'merah' : 'putih';
                                $kelasMgg = $d->mgg >= 85 ? 'merah' : 'putih';

                            @endphp
                            <td align="center"
                                class="freeze-cell_td td_layer mgg {{ $d->mgg >= '85' ? 'text-danger fw-bold' : '' }}">
                                {{ $d->mgg }} <br> {{ $d->mgg_afkir }} <br>
                                ({{ number_format(($d->mgg / $d->mgg_afkir) * 100, 0) }}%) <br>
                            </td>

                            @php
                                $popu = DB::selectOne("SELECT sum(a.mati + a.jual + a.afkir) as pop,b.stok_awal FROM populasi as a
                    LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                    WHERE a.id_kandang = '$d->id_kandang';");

                                $pop = $popu->stok_awal - $popu->pop;
                                $kelasPop = ($pop / $popu->stok_awal) * 100 <= 85 ? 'merah' : 'putih';
                            @endphp

                            {{-- mati dan jual --}}
                            <td style="background-color: {{ $bgZona }} !important" align="center"
                                data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}"
                                nm_kandang="{{ $d->nm_kandang }}" class="tambah_populasi {{ $kelas }}"
                                data-bs-target="#tambah_populasi">
                                <a href="javascript:void(0);" style="font-weight: bold">
                                    &nbsp; <br>
                                    {{ empty($mati) ? '0' : $mati }} <br> {{ empty($jual) ? '0' : $jual }} <br>
                                    {{ empty($ayamAfkir) ? '0' : $ayamAfkir }}
                                    <br>
                                    {{ $d->mati_week + $d->jual_week }}
                                </a>
                            </td>
                            {{-- end mati dan jual --}}

                            <td style="background-color: {{ $bgZona }} !important"
                                class="tambah_populasi putih text-center" data-bs-target="#tambah_populasi">
                                &nbsp; <br>{{ $d->stok_awal }} <br> {{ $d->stok_awal - $d->pop_kurang }} <br>
                                {{ number_format((($d->stok_awal - $d->pop_kurang) / $d->stok_awal) * 100, 1) }}%
                            </td>
                            @php
                                $telur = DB::table('telur_produk')->get();
                                $ttlKg = 0;
                                $ttlPcs = 0;
                                $ttlPcsKemarin = 0;
                                $ttlKgKemarin = 0;
                                foreach ($telur as $tel) {
                                    $tgl = $tglHariIniNih;
                                    $tglKemarin = Carbon\Carbon::yesterday()->format('Y-m-d');

                                    $stok = DB::selectOne("SELECT * FROM stok_telur as a WHERE a.id_kandang = '$d->id_kandang'
                        AND a.tgl = '$tgl' AND a.id_telur = '$tel->id_produk_telur'");

                                    $stokKemarin = DB::selectOne("SELECT * FROM stok_telur as a WHERE a.id_kandang =
                        '$d->id_kandang'
                        AND a.tgl = '$tglKemarin' AND a.id_telur = '$tel->id_produk_telur'");

                                    $pcs = $stok->pcs ?? 0;
                                    $pcsKemarin = $stokKemarin->pcs ?? 0;

                                    $ttlKg += $stok->kg ?? 0;
                                    $ttlPcs += $stok->pcs ?? 0;

                                    $ttlPcsKemarin += $pcsKemarin;
                                    $ttlKgKemarin += $stokKemarin->kg ?? 0;
                                }

                                $kelasTtlPcsTelur = $ttlPcs - $ttlPcsKemarin < -5 ? 'merah' : 'abu';
                                $kelasTtKgTelur = $ttlKg - $ttlKgKemarin < -2.5 ? 'merah' : 'abu';
                            @endphp
                            {{-- telur --}}

                            <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}"
                                nm_kandang="{{ $d->nm_kandang }}" class="tambah_telur text-center "
                                data-bs-target="#tambah_telur">
                                &nbsp; <br>
                                {{ number_format($d->kg - $d->pcs / 180, 1) }}<br>
                                {{ number_format($d->pcs, 0) }} <br>
                                {{ number_format($d->kg, 1) }}
                            </td>
                            @php
                                $gr_butir = empty($d->pcs)
                                    ? '0'
                                    : number_format((($d->kg - $d->pcs / 180) * 1000) / $d->pcs, 0);
                            @endphp
                            <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}"
                                nm_kandang="{{ $d->nm_kandang }}" class="tambah_telur text-center "
                                data-bs-target="#tambah_telur">
                                <p style="margin: 0; padding: 0;">&nbsp;</p>
                                <p style="margin: 0; padding: 0;">&nbsp;</p>
                                <p style="margin: 0; padding: 0;"
                                    class="{{ $gr_butir < 58 ? 'text-danger fw-bold' : '' }}">
                                    {{ $gr_butir }}</p>
                                <p style="margin: 0; padding: 0;">{{ empty($k->t_peforma) ? 'NA' : $k->t_peforma }}
                                </p>
                            </td>
                            <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}"
                                nm_kandang="{{ $d->nm_kandang }}" class="tambah_telur " data-bs-target="#tambah_telur">
                                @php
                                    $kg = $d->kg - $d->pcs / 180 - ($d->kg_past - $d->pcs_past / 180);
                                @endphp
                                <p style="margin: 0; padding: 0;">&nbsp;</p>
                                <p style="margin: 0; padding: 0;" class="{{ $kg < 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ number_format($kg, 1) }}
                                </p>
                                <p style="margin: 0; padding: 0;"
                                    class="{{ $d->pcs - $d->pcs_past < 0 ? 'text-danger fw-bold' : '' }}">
                                    {{ number_format($d->pcs - $d->pcs_past, 0) }}</p>
                                <p style="margin: 0; padding: 0;">&nbsp;</p>
                            </td>

                            @foreach ($telur as $t)
                                @php
                                    $tgl = $tglHariIniNih;
                                    $tglKemarin = Carbon\Carbon::yesterday()->format('Y-m-d');

                                    $stok = DB::selectOne("SELECT * FROM stok_telur as a WHERE a.id_kandang = '$d->id_kandang'
                        AND a.tgl = '$tgl' AND a.id_telur = '$t->id_produk_telur'");

                                    $stokKemarin = DB::selectOne("SELECT * FROM stok_telur as a WHERE a.id_kandang =
                        '$d->id_kandang'
                        AND a.tgl = '$tglKemarin' AND a.id_telur = '$t->id_produk_telur'");

                                    // dd($pcsKemarin - $pcs);

                                @endphp <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}"
                                    nm_kandang="{{ $d->nm_kandang }}" class="tambah_telur abu"
                                    data-bs-target="#tambah_telur">
                                    <span>{{ $stok->pcs ?? 0 }}</span>
                                </td>
                            @endforeach
                            {{-- <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                            class="tambah_telur {{ $kelasTtlPcsTelur }}" data-bs-target="#tambah_telur">
                            {{ $ttlPcs }} ({{ $ttlPcs - $ttlPcsKemarin }})
                        </td>
                        <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                            class="tambah_telur {{ $kelasTtKgTelur }}" data-bs-target="#tambah_telur">
                            {{ number_format($ttlKg, 1) }} ({{ number_format($ttlKg - $ttlKgKemarin, 1) }})
                        </td> --}}
                            {{-- end telur --}}

                            {{-- pakan --}}
                            @php
                                $pakan = DB::selectOne("SELECT *,sum(gr) as total FROM tb_pakan_perencanaan as a
                            WHERE a.tgl = '$tgl' AND a.id_kandang = '$d->id_kandang'
                            GROUP BY a.id_kandang");
                                $gr_pakan = DB::selectOne("SELECT sum(a.gr) as ttl, a.no_nota FROM tb_pakan_perencanaan as a
                            where a.id_kandang = '$d->id_kandang' and a.tgl = '$tgl' group by a.id_kandang");
                                $gr_perekor = empty($pakan) ? 0 : $pakan->total / $pop;
                                $kelas = $gr_perekor < 100 ? 'merah' : 'putih';
                            @endphp
                            <td style="background-color: {{ $bgZona }} !important" data-bs-toggle="modal"
                                id_kandang="{{ $d->id_kandang }}" class="tambah_perencanaan merah"
                                data-bs-target="#tambah_perencanaan">
                                {{ empty($gr_pakan) ? 0 : number_format($gr_pakan->ttl / 1000, 1) }}</td>
                            <td style="background-color: {{ $bgZona }} !important" data-bs-toggle="modal"
                                id_kandang="{{ $d->id_kandang }}" class="{{ $kelas }} tambah_perencanaan"
                                data-bs-target="#tambah_perencanaan">
                                {{ number_format($gr_perekor, 0) }}</td>
                            <td class="td_layer" align="center">
                                @php
                                    $vitamin = DB::select("SELECT a.id_pakan, b.nm_produk, c.nm_satuan, a.id_kandang, a.pcs_kredit, b.kategori
                                        FROM stok_produk_perencanaan as a
                                        left JOIN tb_produk_perencanaan as b on b.id_produk = a.id_pakan
                                        left join tb_satuan as c on c.id_satuan = b.dosis_satuan
                                        WHERE a.tgl = '$tgl' and a.id_kandang = '$d->id_kandang' and b.kategori in('pakan');");
                                @endphp

                                @foreach ($vitamin as $key => $v)
                                    {{ $v->nm_produk }} :
                                    {{ number_format($v->pcs_kredit / 1000, 1) }}
                                    Kg,
                                    @if (($key + 1) % 2 == 0)
                                        <br>
                                    @endif
                                @endforeach

                                @php
                                    $vitamin = DB::select("SELECT a.id_pakan, b.nm_produk, c.nm_satuan, a.id_kandang, a.pcs_kredit, b.kategori, d.campuran, e.nm_satuan as satuan_campuran
                                FROM stok_produk_perencanaan as a
                                left JOIN tb_produk_perencanaan as b on b.id_produk = a.id_pakan
                                left join tb_satuan as c on c.id_satuan = b.dosis_satuan
                                left join (
                                    SELECT d.id_produk , d.tgl, d.campuran  
                                    FROM tb_obat_perencanaan as d
                                    where d.tgl = '$tgl' and d.id_kandang = '$d->id_kandang'
                                ) as d on d.id_produk = a.id_pakan
                                left join tb_satuan as e on e.id_satuan = b.campuran_satuan
                                WHERE a.tgl = '$tgl' and a.id_kandang = '$d->id_kandang' and b.kategori in('obat_pakan', 'obat_air','obat_ayam');");
                                @endphp

                                @foreach ($vitamin as $key => $v)
                                    {{ $v->nm_produk }} :
                                    {{ number_format($v->pcs_kredit, 1) }}
                                    {{ $v->nm_satuan }} |
                                    @if ($v->kategori == 'obat_ayam')
                                        {{ number_format($v->pcs_kredit / ($d->stok_awal - $d->pop_kurang), 1) }}
                                        PerAyam ,
                                    @else
                                        {{ $v->campuran }} {{ $v->satuan_campuran }},
                                    @endif
                                    @if (($key + 1) % 2 == 0)
                                        <br>
                                    @endif
                                @endforeach

                            </td>

                            {{-- end pakan --}}


                        </tr>
                        @php
                            $total_populasi += $pop;
                            $total_mati += $mati;
                            $total_jual += $jual;
                            $total_ayamAfkir += $ayamAfkir;
                            $total_kilo += $ttlKg;
                            $total_kilo_kemaren += $ttlKgKemarin;
                            $total_kg_pakan += empty($gr_pakan) ? 0 : $gr_pakan->ttl / 1000;
                            $totalGrPcs += empty($ttlKg) ? 0 : ($ttlKg * 1000) / $ttlPcs;

                            $total_pcs_kemarin_dan_hariini += $ttlPcs - $ttlPcsKemarin;
                        @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    @php
                        $total_pcs = 0;
                        $total_kemarin_pcs = 0;
                        foreach ($telur as $t) {
                            $totalstok = DB::selectOne("SELECT sum(a.pcs) as total_pcs FROM stok_telur as a WHERE a.tgl = '$tgl' AND
                        a.id_telur = '$t->id_produk_telur' and a.id_kandang != '0'");
                            $total_pcs += $totalstok->total_pcs;

                            $stokKemarin = DB::selectOne(
                                "SELECT sum(a.pcs) as ttl_pcs FROM stok_telur as a WHERE  a.tgl = '$tglKemarin' AND a.id_telur = '$t->id_produk_telur'",
                            );
                            $total_kemarin_pcs += $stokKemarin->ttl_pcs;
                        }

                    @endphp
                    <th style="background-color: {{ $bgZona }} !important" colspan="2">Total</th>
                    <th style="background-color: {{ $bgZona }} !important" class="text-center">
                        {{ number_format($total_mati, 0) }} <br> {{ number_format($total_jual, 0) }} <br>
                        {{ number_format($total_ayamAfkir, 0) }} <br>
                        {{ $dc_week }}</th>
                    <th style="background-color: {{ $bgZona }} !important" class="text-end">
                        {{ number_format($ayam_awal, 0) }}
                        <br>{{ number_format($ayam_akhir, 0) }} <br>
                        {{ number_format(($ayam_akhir / $ayam_awal) * 100, 0) }} %
                    </th>
                    <th class="text-end">{{ number_format($kgTtl, 2) }}
                        <br>
                        {{ number_format($pcsTtl, 0) }}
                        <br>
                        {{ number_format($kg_kotor, 2) }}
                    </th>
                    <th class="text-end">{{ $gr_butir / 4 }}</th>
                    <th class="text-end">{{ number_format($kg_today, 1) }}
                        <br>
                        {{ number_format($butir, 0) }}
                    </th>

                    @foreach ($telur as $t)
                        @php
                            $totalstok = DB::selectOne("SELECT sum(a.pcs) as total_pcs FROM stok_telur as a WHERE a.tgl = '$tgl' AND
                a.id_telur = '$t->id_produk_telur' and a.id_kandang != '0'");
                            $total_pcs += $totalstok->total_pcs;
                        @endphp
                        <th class="text-end">{{ number_format($totalstok->total_pcs, 0) }}</th>
                    @endforeach

                    <th style="background-color: {{ $bgZona }} !important" class="text-end">
                        {{ number_format($total_kg_pakan, 1) }}</th>
                    <th style="background-color: {{ $bgZona }} !important"></th>

                </tfoot>
            </table>
        </div>
    </div>



</body>

</html>
