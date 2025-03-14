<style>
    .hoverbtn:hover {
        background: #263772;
    }
</style>
<div class="col-lg-8">
    @php
        $font = DB::table('font_size')->first()->font;

    @endphp
    <form action="{{ route('dashboard_kandang.set_font') }}" method="post">
        @csrf
        <table>
            <tr>
                <td><label for="" class="text-primary">Font Size</label></td>
                <td><input name="font" value="{{ $font }}" style="width: 65px" type="number" min="10"
                        class="form-control float-end"></td>
                <td><button type="submit" class="btn btn-sm btn-primary">Save</button></td>
            </tr>
        </table>
    </form>
    <h6>
        @php
            $tglHariIniNih = date('Y-m-d');
        @endphp
        Input Kandang Harian ~ {{ tanggal($tglHariIniNih) }}
        <div class="btn-group dropup me-1 mb-2 float-end">
            <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-print"></i> Export
            </button>
            <div class="dropdown-menu bg-primary" style="">
                <h6 class="dropdown-header text-white">Data Export</h6>
                {{-- <a data-bs-toggle="modal" data-bs-target="#export_telur" class="text-white dropdown-item hoverbtn"
                    href="#"> Telur</a> --}}
                <a data-bs-toggle="modal" data-bs-target="#daily_layer" class="text-white dropdown-item hoverbtn"
                    href="#"> Daily Layer</a>
                <a data-bs-toggle="modal" data-bs-target="#commercial_layer" class="text-white dropdown-item hoverbtn"
                    href="#"> Commercial Layer</a>
                <a href="{{ route('dashboard_kandang.export_perencanaan') }}" class="text-white dropdown-item hoverbtn"
                    href="#"> Perencanaan</a>
                {{-- <a data-bs-toggle="modal" data-bs-target="#week_layer" class="text-white dropdown-item hoverbtn"
                    href="#"> Week Layer</a> --}}
            </div>

        </div>

        {{-- <div class="float-end">
            <form action="">
                <div class="form-group">
                    <label for="">Font Size</label>
                    <input type="text" name="example" class="form-control">
                </div>
            </form>
        </div> --}}

    </h6>

    <table class="table table-bordered table-hover " id="" style="font-size: {{ $font }}px;">
        <thead>
            <tr>
                @php
                    $bgZona = '#f6f0f0';
                @endphp
                <th rowspan="2" width="1%" class="text-center dhead">
                    Kdg <br> chick in <br>
                    Afkir <br>
                    chick in2
                    <br>
                    {{ date('d/m/y') }}
                </th>
                <th style="background-color: {{ $bgZona }} !important" colspan="3" class="text-center  putih">
                    Populasi</th>
                <th colspan="9" class="text-center abu"> Telur </th>
                <th style="background-color: {{ $bgZona }} !important" colspan="2" class="text-center putih">
                    pakan</th>
                <th width="2%" class="text-center dhead" rowspan="2">Aksi</th>
            </tr>

            <tr>
                <th width="3%" class="dhead text-center">Mgg (85)<br> <i
                        class="fas text-white fa-question-circle rumus" rumus="minggu" style="cursor: pointer"></i></th>
                <th width="3%" class="dhead text-center">D <br> C <br><i
                        class="fas text-white fa-question-circle rumus" rumus="mati" style="cursor: pointer"></i></th>
                <th width="1%" class="dhead text-center">Pop </th>
                <th width="4%" class="dhead text-center">Ttl Pcs <i class="fas text-white fa-question-circle rumus"
                        rumus="ttlPcs" style="cursor: pointer"></i></th>
                <th width="4%" class="dhead text-center">Ttl Kg <i class="fas text-white fa-question-circle rumus"
                        rumus="ttlKg" style="cursor: pointer"></i></th>
                <th width="4%" class="dhead text-center">Gr pcs </th>
                @php
                    $telur = DB::table('telur_produk')->get();
                @endphp
                @foreach ($telur as $d)
                    <th width="1%" class="dhead text-center">
                        {{ ucwords(str_replace('telur', '', strtolower($d->nm_telur))) }}</th>
                @endforeach

                <th width="1%" class="dhead text-center">Kg</th>
                <th width="3%" class="dhead text-center">Gr / Ekor <i
                        class="fas text-white fa-question-circle rumus" rumus="grEkor" style="cursor: pointer"></i></th>

            </tr>
        </thead>
        <tbody class="text-end">
            @php
                $total_populasi = 0;
                $total_mati = 0;
                $total_jual = 0;
                $total_kilo = 0;
                $total_kilo_kemaren = 0;
                $total_kg_pakan = 0;
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
                        <br>
                        {{ date('d/m/y', strtotime($d->tgl_masuk_kandang)) }}

                    </td>
                    @php
                        $populasi = DB::table('populasi')
                            ->where([['id_kandang', $d->id_kandang], ['tgl', $tglHariIniNih]])
                            ->first();

                        $mati = $populasi->mati ?? 0;
                        $jual = $populasi->jual ?? 0;
                        $kelas = $mati > 3 ? 'merah' : 'putih';
                        $kelasMgg = $d->mgg >= 85 ? 'merah' : 'putih';

                    @endphp
                    <td style="background-color: {{ $bgZona }} !important" align="center" data-bs-toggle="modal"
                        id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_populasi {{ $kelasMgg }}" data-bs-target="#tambah_populasi">
                        {{ $d->mgg }} <br>
                        ({{ number_format(($d->mgg / 85) * 100, 0) }}%)</td>

                    @php
                        $popu = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                WHERE a.id_kandang = '$d->id_kandang';");

                        $pop = $popu->stok_awal - $popu->pop;
                        $kelasPop = ($pop / $popu->stok_awal) * 100 <= 85 ? 'merah' : 'putih';
                    @endphp

                    {{-- mati dan jual --}}
                    <td style="background-color: {{ $bgZona }} !important" align="center"
                        data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_populasi {{ $kelas }}" data-bs-target="#tambah_populasi">
                        {{ $mati ?? 0 }} <br> {{ $jual ?? 0 }}</td>
                    {{-- end mati dan jual --}}

                    <td style="background-color: {{ $bgZona }} !important" data-bs-toggle="modal"
                        id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_populasi putih" data-bs-target="#tambah_populasi">{{ $pop }} </td>
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

                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_telur {{ $kelasTtlPcsTelur }}" data-bs-target="#tambah_telur">
                        {{ $ttlPcs }} ({{ $ttlPcs - $ttlPcsKemarin }})
                    </td>
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_telur {{ $kelasTtKgTelur }}" data-bs-target="#tambah_telur">
                        {{ number_format($ttlKg, 1) }} ({{ number_format($ttlKg - $ttlKgKemarin, 1) }})
                    </td>
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_telur {{ $kelasTtKgTelur }}" data-bs-target="#tambah_telur">
                        {{ number_format(empty($ttlKg) ? 0 : ($ttlKg * 1000) / $ttlPcs, 1) }}
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

                    {{-- end pakan --}}
                    <td align="center">
                        @if (auth()->user()->posisi_id == 1)
                            <a onclick="return confirm('Yakin ingin di selesaikan ?')"
                                href="{{ route('dashboard_kandang.kandang_selesai', $d->id_kandang) }}"
                                class="badge bg-primary"><i class="fas fa-check"></i></a>
                            <a href="#" class="badge bg-warning edit_kandang"
                                id_kandang="{{ $d->id_kandang }}" data-bs-toggle="modal"
                                data-bs-target="#edit_kandang"><i class="fas fa-edit"></i></a>
                        @else
                        @endif
                    </td>
                </tr>
                @php
                    $total_populasi += $pop;
                    $total_mati += $mati;
                    $total_jual += $jual;
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

                    $stokKemarin = DB::selectOne("SELECT sum(a.pcs) as ttl_pcs FROM stok_telur as a WHERE  a.tgl = '$tglKemarin' AND a.id_telur = '$t->id_produk_telur'");
                    $total_kemarin_pcs += $stokKemarin->ttl_pcs;
                }

            @endphp
            <th style="background-color: {{ $bgZona }} !important" colspan="2">Total</th>
            <th style="background-color: {{ $bgZona }} !important" class="text-center">
                {{ number_format($total_mati, 0) }} <br> {{ number_format($total_jual, 0) }}</th>
            <th style="background-color: {{ $bgZona }} !important" class="text-end">
                {{ number_format($total_populasi, 0) }}</th>
            <th class="text-end">{{ number_format($total_pcs, 0) }}
                ({{ number_format($total_pcs_kemarin_dan_hariini, 0) }})</th>
            <th class="text-end">{{ number_format($total_kilo, 1) }}
                ({{ number_format($total_kilo - $total_kilo_kemaren, 0) }})</th>
            <th class="text-end">{{ number_format($totalGrPcs, 1) }}</th>

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
            <th></th>
        </tfoot>
    </table>
</div>
<x-theme.modal title="Rumus" btnSave='T' idModal="rumus">
    <div id="rumus_layer"></div>
</x-theme.modal>

@include('dashboard_kandang.modal.tambah_kandang')
@include('dashboard_kandang.modal.tambah_karung')

{{-- tambah telur --}}
<form action="{{ route('dashboard_kandang.tambah_telur') }}" method="post">
    @csrf
    <x-theme.modal title="Tambah Telur" size="modal-md" idModal="tambah_telur">
        <div id="load_telur"></div>
    </x-theme.modal>
</form>
{{-- end tambah telur --}}

{{-- tambah populasi --}}
<form action="{{ route('dashboard_kandang.tambah_populasi') }}" method="post">
    @csrf
    <x-theme.modal title="Tambah Populasi" size="modal-lg" idModal="tambah_populasi">
        <div id="load_populasi"></div>
    </x-theme.modal>
</form>
{{-- end tambah populasi --}}

{{-- tambah perencanaan --}}
<form action="{{ route('dashboard_kandang.tambah_perencanaan') }}" method="post">
    @csrf
    <x-theme.modal title="Tambah Perencanaan" size="modal-lg" idModal="tambah_perencanaan">
        <div id="load_perencanaan"></div>
    </x-theme.modal>
</form>
{{-- end tambah perencanaan --}}


{{-- detail perencanaan --}}
<x-theme.modal title="Detail Perencanaan" btnSave="" size="modal-lg-max" idModal="detail_perencanaan">
    <div class="row">
        <div class="col-lg-12">
            <a data-bs-toggle="collapse" href="#perencanaan" class="btn btn-sm btn-primary">History
                Perencanaan</a>
            <button data-bs-toggle="collapse" href="#layer" type="button" class="btn btn-sm btn-primary">History
                Layer</button>
            <a data-bs-toggle="collapse" href="#inputTelur" class="btn btn-sm btn-primary">Input Telur</a>
        </div>
    </div>
    <hr style="border: 1px solid #6777EF;">

    <div id="load_detail_perencanaan"></div>
</x-theme.modal>
{{-- end detail perencanaan --}}

{{-- edit perencanaan --}}
<form action="{{ route('dashboard_kandang.edit_perencanaan') }}" method="post">
    @csrf
    <x-theme.modal title="Edit Perencanaan" size="modal-lg" idModal="edit_perencanaan">
        <div id="hasilEditPerencanaan"></div>
    </x-theme.modal>
</form>
{{-- end edit perencanaan --}}

<form action="{{ route('dashboard_kandang.export_telur') }}" method="post">
    @csrf
    <x-theme.modal title="Export Telur" idModal="export_telur">
        <div class="row">
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Dari</label>
                    <input type="date" name="tgl1" class="form-control">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="form-group">
                    <label for="">Sampai</label>
                    <input type="date" name="tgl2" class="form-control">
                </div>
            </div>
        </div>
    </x-theme.modal>
</form>

<form action="{{ route('commercial_layer') }}" method="post">
    @csrf
    <x-theme.modal title="Export Daily Layer" idModal="commercial_layer">
        <div class="row">
            <div class="form-group">
                <label for="">Kandang</label>
                <select name="id_kandang" class="form-control" id="">
                    <option value="">- Pilih Kandang -</option>
                    @foreach ($kandang as $kd)
                        <option value="{{ $kd->id_kandang }}">{{ $kd->nm_kandang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-theme.modal>
</form>
<form action="{{ route('dashboard_kandang.daily_layer') }}" method="post">
    @csrf
    <x-theme.modal title="Export Daily Layer" idModal="daily_layer">
        <div class="row">
            <div class="form-group">
                <label for="">Kandang</label>
                <select name="id_kandang" class="form-control" id="">
                    <option value="">- Pilih Kandang -</option>
                    @foreach ($kandang as $kd)
                        <option value="{{ $kd->id_kandang }}">{{ $kd->nm_kandang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-theme.modal>
</form>
<form action="{{ route('dashboard_kandang.week_layer') }}" method="post">
    @csrf
    <x-theme.modal title="Export Week Layer" idModal="week_layer">
        <div class="row">
            <div class="form-group">
                <label for="">Kandang</label>
                <select name="id_kandang" class="form-control" id="">
                    <option value="">- Pilih Kandang -</option>
                    @foreach ($kandang as $kd)
                        <option value="{{ $kd->id_kandang }}">{{ $kd->nm_kandang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-theme.modal>
</form>
