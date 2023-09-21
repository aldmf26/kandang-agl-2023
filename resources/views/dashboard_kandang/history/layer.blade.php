<div class="row">
    <table class="table table-bordered table-hover " id="">
        <thead>
            <tr>
                <th rowspan="2" width="1%" class="text-center dhead">Kdg</th>
                <th colspan="3" class="text-center  putih">Populasi</th>
                <th colspan="7" class="text-center abu"> Telur </th>
                <th colspan="2" class="text-center putih">pakan</th>
            </tr>

            <tr>
                <th width="2%" class="dhead text-center">Minggu <br> (85) <i
                        class="fas text-white fa-question-circle rumus" rumus="minggu" style="cursor: pointer"></i></th>
                <th width="1%" class="dhead text-center">Pop </th>
                <th width="6%" class="dhead text-center">Mati / Jual <i
                        class="fas text-white fa-question-circle rumus" rumus="mati" style="cursor: pointer"></i></th>
                @php
                    $telur = DB::table('telur_produk')->get();
                @endphp
                @foreach ($telur as $d)
                    <th width="1%" class="dhead text-center">
                        {{ ucwords(str_replace('telur', '', strtolower($d->nm_telur))) }}</th>
                @endforeach
                <th width="1%" class="dhead text-center">Ttl Pcs <i class="fas text-white fa-question-circle rumus"
                        rumus="ttlPcs" style="cursor: pointer"></i></th>
                <th width="1%" class="dhead text-center">Ttl Kg <i class="fas text-white fa-question-circle rumus"
                        rumus="ttlKg" style="cursor: pointer"></i></th>
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
                $total_kg_pakan = 0;
            @endphp
            @foreach ($kandang as $no => $d)
                <tr>
                    <td align="center" class="detail_perencanaan" id_kandang="{{ $d->id_kandang }}"
                        data-bs-toggle="modal" data-bs-target="#detail_perencanaan">
                        {{ $d->nm_kandang }}</td>
                    @php
                        $populasi = DB::table('populasi')
                            ->where([['id_kandang', $d->id_kandang], ['tgl', $tgl]])
                            ->first();
                        
                        $mati = $populasi->mati ?? 0;
                        $jual = $populasi->jual ?? 0;
                        $kelas = $mati > 3 ? 'merah' : 'putih';
                        $kelasMgg = $d->mgg >= 85 ? 'merah' : 'putih';
                    @endphp
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_populasi {{ $kelasMgg }}" data-bs-target="#tambah_populasi">
                        {{ $d->mgg }} /
                        {{ number_format(($d->mgg / 85) * 100, 0) }} %</td>

                    @php
                        $popu = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                WHERE a.id_kandang = '$d->id_kandang';");
                        
                        $pop = $popu->stok_awal - $popu->pop;
                        $kelasPop = ($pop / $popu->stok_awal) * 100 <= 85 ? 'merah' : 'putih';
                    @endphp
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_populasi putih" data-bs-target="#tambah_populasi">{{ $pop }} </td>

                    {{-- mati dan jual --}}
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_populasi {{ $kelas }}" data-bs-target="#tambah_populasi">
                        {{ $mati ?? 0 }} / {{ $jual ?? 0 }}</td>
                    {{-- end mati dan jual --}}

                    {{-- telur --}}
                    @php
                        $telur = DB::table('telur_produk')->get();
                        $ttlKg = 0;
                        $ttlPcs = 0;
                        $ttlPcsKemarin = 0;
                        $ttlKgKemarin = 0;
                    @endphp
                    @foreach ($telur as $t)
                        @php
                        $tanggalObjek = Carbon\Carbon::parse($tgl);
                            $tglKemarin = Carbon\Carbon::now()->diffInDays($tanggalObjek);
                            dd($tglKemarin . ' = ' .$tgl);
                            $stok = DB::selectOne("SELECT * FROM stok_telur as a WHERE a.id_kandang = '$d->id_kandang'
                    AND a.tgl = '$tgl' AND a.id_telur = '$t->id_produk_telur'");
                            
                            $stokKemarin = DB::selectOne("SELECT * FROM stok_telur as a WHERE a.id_kandang =
                    '$d->id_kandang'
                    AND a.tgl = '$tglKemarin' AND a.id_telur = '$t->id_produk_telur'");
                            $pcs = $stok->pcs ?? 0;
                            $pcsKemarin = $stokKemarin->pcs ?? 0;
                            
                            $ttlKg += $stok->kg ?? 0;
                            $ttlPcs += $stok->pcs ?? 0;
                            
                            $ttlPcsKemarin += $pcsKemarin;
                            $ttlKgKemarin += $stokKemarin->kg ?? 0;
                            // dd($pcsKemarin - $pcs);
                            $kelasTtlPcsTelur = $ttlPcs - $ttlPcsKemarin < -60 ? 'merah' : 'abu';
                            $kelasTtKgTelur = $ttlKg - $ttlKgKemarin < -2.5 ? 'merah' : 'abu';
                        @endphp
                        <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                            class="tambah_telur " data-bs-target="#tambah_telur">
                            <span>{{ $stok->pcs ?? 0 }}</span>
                        </td>
                    @endforeach
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_telur {{ $kelasTtlPcsTelur }}" data-bs-target="#tambah_telur">
                        {{ $ttlPcs }} ({{ $ttlPcs - $ttlPcsKemarin }})
                    </td>
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" nm_kandang="{{ $d->nm_kandang }}"
                        class="tambah_telur {{ $kelasTtKgTelur }}" data-bs-target="#tambah_telur">
                        {{ number_format($ttlKg, 1) }} ({{ number_format($ttlKg - $ttlKgKemarin, 1) }})
                    </td>
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
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}" class="tambah_perencanaan"
                        data-bs-target="#tambah_perencanaan">
                        {{ empty($gr_pakan) ? 0 : number_format($gr_pakan->ttl / 1000, 1) }}</td>
                    <td data-bs-toggle="modal" id_kandang="{{ $d->id_kandang }}"
                        class="{{ $kelas }} tambah_perencanaan" data-bs-target="#tambah_perencanaan">
                        {{ number_format($gr_perekor, 0) }}</td>

                    {{-- end pakan --}}

                </tr>
                @php
                    $total_populasi += $pop;
                    $total_mati += $mati;
                    $total_jual += $jual;
                    $total_kilo += $ttlKg;
                    $total_kg_pakan += empty($gr_pakan) ? 0 : $gr_pakan->ttl / 1000;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <th colspan="2">Total</th>
            <th class="text-end">{{ number_format($total_populasi, 0) }}</th>
            <th class="text-end">{{ number_format($total_mati, 0) }} / {{ number_format($total_jual, 0) }}</th>
            @php
                $total_pcs = 0;
            @endphp
            @foreach ($telur as $t)
                @php
                    $totalstok = DB::selectOne("SELECT sum(a.pcs) as total_pcs FROM stok_telur as a WHERE a.tgl = '$tgl' AND a.id_telur = '$t->id_produk_telur' and a.id_kandang != '0'");
                    $total_pcs += $totalstok->total_pcs;
                @endphp
                <th class="text-end">{{ number_format($totalstok->total_pcs, 0) }}</th>
            @endforeach
            <th class="text-end">{{ number_format($total_pcs, 0) }}</th>
            <th class="text-end">{{ number_format($total_kilo, 1) }}</th>
            <th class="text-end">{{ number_format($total_kg_pakan, 1) }}</th>
            <th></th>
        </tfoot>
    </table>
</div>
