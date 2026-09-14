<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatPakanController extends Controller
{
    public function load_stok_pakan(Request $r)
    {
        $c = DB::table('kandang')->get();
        $pop = 0;
        foreach ($c as $d) {
            $popu = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                WHERE a.id_kandang = '$d->id_kandang';");

            $pop += $popu->stok_awal - $popu->pop;
        }
        $tgl1 = "2023-08-10";
        $tgl2 = date('Y-m-t');
        $data = [
            'pakan' => DB::select("SELECT a.id_pakan, b.nm_produk, sum(a.pcs) as pcs_debit, sum(a.pcs_kredit) as pcs_kredit, c.nm_satuan
            FROM stok_produk_perencanaan as a 
            left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
            left join tb_satuan as c on c.id_satuan = b.dosis_satuan
            where b.kategori ='pakan'
            group by a.id_pakan;"),

            'vitamin' => DB::select("SELECT b.kategori,a.id_pakan, b.nm_produk, sum(a.pcs) as pcs_debit, sum(a.pcs_kredit) as pcs_kredit, c.nm_satuan
            FROM stok_produk_perencanaan as a 
            left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
            left join tb_satuan as c on c.id_satuan = b.dosis_satuan
            where b.kategori in('obat_pakan','obat_air','obat_ayam')
            group by a.id_pakan;"),

            'total_populasi' => $pop,

            'vaksin' => DB::table('tb_produk_perencanaan as b')
                ->leftJoin('stok_produk_perencanaan as a', 'a.id_pakan', '=', 'b.id_produk')
                ->leftJoin('tb_satuan as c', 'c.id_satuan', '=', 'b.dosis_satuan')
                ->where('b.kategori', 'vaksin')
                ->groupBy('b.id_produk', 'b.nm_produk', 'c.nm_satuan')
                ->orderBy('b.nm_produk')
                ->selectRaw('b.id_produk as id_pakan, b.nm_produk, COALESCE(SUM(a.pcs), 0) as pcs_debit, COALESCE(SUM(a.pcs_kredit), 0) as pcs_kredit, c.nm_satuan')
                ->get(),

        ];
        return view('stok_pakan.stok', $data);
    }

    public function history_pakan(Request $r)
    {
        $data = [
            'datas' => DB::select("SELECT b.nm_produk,a.pcs,a.total_rp,a.biaya_dll,a.tgl,a.admin,a.no_nota FROM `stok_produk_perencanaan` as a 
            JOIN tb_produk_perencanaan as b on a.id_pakan = b.id_produk
            WHERE a.pcs != 0 AND b.kategori = 'pakan'")
        ];
        return view('stok_pakan.history', $data);
    }

    public function history_stok(Request $r)
    {
        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-t');

        $history = DB::select("SELECT a.h_opname,a.admin,a.tgl,a.id_pakan, b.nm_produk, k.nm_kandang, sum(a.pcs) as pcs, sum(a.pcs_kredit) as pcs_kredit, c.nm_satuan
        FROM stok_produk_perencanaan as a 
        left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
        left join tb_satuan as c on c.id_satuan = b.dosis_satuan
        left join kandang as k on k.id_kandang = a.id_kandang
        where a.tgl BETWEEN '$tgl1' AND '$tgl2' and a.id_pakan = '$r->id_pakan'
        group by a.id_stok_telur;");

        $data = [
            'stok' => $history,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'id_pakan' => $r->id_pakan
        ];
        return view('stok_pakan.history_stok', $data);
    }

    public function opname_pakan(Request $r)
    {
        $tgl = $r->tgl ?? date('Y-m-d');

        $data = [
            'pakan' => DB::select("SELECT a.id_pakan, b.nm_produk, sum(a.pcs) as pcs_debit, sum(a.pcs_kredit) as pcs_kredit, c.nm_satuan
            FROM stok_produk_perencanaan as a 
            left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
            left join tb_satuan as c on c.id_satuan = b.dosis_satuan
            where b.kategori = 'pakan'
            group by a.id_pakan;"),
            'tgl' => $tgl
        ];
        return view('opname.opname_pakan', $data);
    }
    public function opnme_vitamin(Request $r)
    {
        $tgl = $r->tgl ?? date('Y-m-d');
        $data = [
            'pakan' => DB::select("SELECT a.id_pakan, b.nm_produk, sum(a.pcs) as pcs_debit, sum(a.pcs_kredit) as pcs_kredit, c.nm_satuan
            FROM stok_produk_perencanaan as a 
            left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
            left join tb_satuan as c on c.id_satuan = b.dosis_satuan
            where b.kategori in('obat_pakan','obat_air') 
            group by a.id_pakan;"),
            'tgl' => $tgl
        ];
        return view('opname.opname_pakan', $data);
    }

    public function save_opname_pakan(Request $r)
    {
        DB::beginTransaction();
        try {
            $notaTerakhir = DB::table('stok_produk_perencanaan as a')
                ->join('tb_produk_perencanaan as b', 'a.id_pakan', 'b.id_produk')
                ->where([
                    ['a.no_nota', 'LIKE', "%PAKVITOPN-%"]
                ])
                ->orderBy('id_stok_telur', 'DESC')
                ->groupBy('a.no_nota')
                ->first();
            $no_nota = empty($notaTerakhir) ? 1000 : str()->remove("PAKVITOPN-", $notaTerakhir->no_nota) + 1;
            for ($x = 0; $x < count($r->id_pakan); $x++) {
                DB::table('stok_produk_perencanaan')->where(['id_pakan' => $r->id_pakan[$x], 'opname' => 'T'])->update(['opname' => 'Y']);
                $id_pakan = $r->id_pakan[$x];

                $selisih = $r->stk_program[$x] - $r->stk_aktual[$x];
                if ($r->selisih[$x] != 0) {
                    if ($selisih < 0) {
                        $qty_selisih = $selisih * -1;

                        $datas = [
                            'pcs' => $r->stk_aktual[$x],
                            'id_pakan' => $r->id_pakan[$x],
                            'opname' => 'T',
                            'tgl' => $r->tgl,
                            'admin' => auth()->user()->name,
                            'no_nota' => "PAKVITOPN-" . $no_nota,
                            'h_opname' => 'Y',
                            'pcs' => $qty_selisih,
                            'pcs_kredit' => 0,
                            'pcs_selisih' => $r->selisih[$x],
                            'total_rp' => 0
                        ];
                        DB::table('stok_produk_perencanaan')->insert($datas);
                    } else {
                        $qty_selisih = $selisih;

                        $datas = [
                            'pcs' => $r->stk_aktual[$x],
                            'id_pakan' => $r->id_pakan[$x],
                            'opname' => 'T',
                            'tgl' => $r->tgl,
                            'admin' => auth()->user()->name,
                            'no_nota' => "PAKVITOPN-" . $no_nota,
                            'h_opname' => 'Y',
                            'pcs' => 0,
                            'pcs_kredit' => $qty_selisih,
                            'pcs_selisih' => $r->selisih[$x],
                            'total_rp' => 0
                        ];
                        DB::table('stok_produk_perencanaan')->insert($datas);
                    }
                } else {
                    continue;
                }
            }
            DB::commit();
            return redirect()->route('dashboard_kandang.print_opname', "PAKVITOPN-" . $no_nota)->with('sukses', 'Data berhasil di simpan');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->route('dashboard_kandang.index')->with('error', $e->getMessage());
        }
    }

    public function print_opname($no_nota, $print = null)
    {
        $history = DB::select("SELECT a.admin,a.tgl,a.id_pakan,b.nm_produk,a.pcs,a.pcs_kredit,a.total_rp,a.biaya_dll,c.stok,d.sum_ttl_rp,d.pcs_sum_ttl_rp 
        FROM `stok_produk_perencanaan` as a 
        LEFT JOIN tb_produk_perencanaan as b ON a.id_pakan = b.id_produk
        LEFT JOIN (
            SELECT a.id_pakan, (sum(a.pcs) - sum(a.pcs_kredit)) as stok
                    FROM stok_produk_perencanaan as a 
                    group by a.id_pakan
        ) as c ON a.id_pakan = c.id_pakan
        LEFT JOIN (
            SELECT a.id_pakan,sum(a.total_rp + a.biaya_dll) as sum_ttl_rp, sum(pcs) as pcs_sum_ttl_rp FROM stok_produk_perencanaan as a
                    WHERE a.h_opname = 'T' AND a.pcs != 0 and  a.admin not in('import','nanda')
                    GROUP BY a.id_pakan
        ) as d on a.id_pakan = d.id_pakan
        WHERE a.no_nota = '$no_nota' ORDER BY a.pcs DESC;");
        $data = [
            'title' => 'Nota Opname Pakan dan Vitamin',
            'no_nota' => $no_nota,
            'history' => $history
        ];
        $view = empty($print) ? 'cek' : 'print';
        return view("stok_pakan.$view", $data);
    }

    public function tambah_pakan_stok(Request $r)
    {
        $data = [
            'produk' => DB::table('tb_produk_perencanaan')->where('kategori', 'pakan')->get(),
            'kategori' => 'pakan'
        ];
        return view('stok_pakan.tbh_stok', $data);
    }
    public function tambah_vitamin(Request $r)
    {
        $data = [
            'produk' => DB::select("SELECT * FROM tb_produk_perencanaan as a where a.kategori in('obat_pakan','obat_air')"),
            'kategori' => 'vitamin'
        ];
        return view('stok_pakan.tbh_stok_vitamin', $data);
    }

    function get_satuan(Request $r)
    {
        $produk = DB::selectOne("SELECT * FROM tb_produk_perencanaan as a 
        left join tb_satuan as c on c.id_satuan = a.dosis_satuan
        where a.id_produk = '$r->id_produk'");

        echo $produk->nm_satuan;
    }

    public function save_tambah_pakan(Request $r)
    {
        $jenis = $r->kategori == 'pakan' ? "PKNMSK" : "VITMSK";

        $notaTerakhir = DB::table('stok_produk_perencanaan as a')
            ->join('tb_produk_perencanaan as b', 'a.id_pakan', 'b.id_produk')
            ->where([
                ['b.kategori', $r->kategori != 'pakan' ? '!= pakan' : 'pakan'],
                ['a.no_nota', 'LIKE', "%$jenis%"]
            ])
            ->orderBy('id_stok_telur', 'DESC')
            ->groupBy('a.no_nota')
            ->first();
        $no_nota = empty($notaTerakhir) ? 1000 : str()->remove("$jenis-", $notaTerakhir->no_nota) + 1;

        for ($x = 0; $x < count($r->id_pakan); $x++) {
            if ($r->kategori == 'pakan') {
                $data = [
                    'id_pakan' => $r->id_pakan[$x],
                    'pcs' => $r->pcs[$x] * 50000,
                    'total_rp' => $r->ttl_rp[$x],
                    'biaya_dll' => $r->biaya_dll[$x],
                    'admin' => auth()->user()->name,
                    'tgl' => $r->tgl,
                    'no_nota' => "$jenis-" . $no_nota
                ];
                DB::table('stok_produk_perencanaan')->insert($data);
                $data = [
                    'tgl' => $r->tgl,
                    'debit' => $r->pcs[$x],
                    'kredit' => 0,
                    'id_gudang' => '1',
                    'admin' =>  auth()->user()->name,
                    'jenis' => 'karung'
                ];
                DB::table('stok_ayam')->insert($data);
            } else {
                $data = [
                    'id_pakan' => $r->id_pakan[$x],
                    'pcs' => $r->pcs[$x],
                    'total_rp' => $r->ttl_rp[$x],
                    'biaya_dll' => $r->biaya_dll[$x],
                    'admin' => auth()->user()->name,
                    'tgl' => $r->tgl,
                    'no_nota' => $jenis . '-' . $no_nota
                ];
                DB::table('stok_produk_perencanaan')->insert($data);
            }
        }

        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data berhasil di simpan');
    }

    public function tambah_baris_stok(Request $r)
    {
        $data = [
            'produk' => DB::table('tb_produk_perencanaan')->where('kategori', 'pakan')->get(),
            'count' => $r->count,
            'kategori' => 'pakan'

        ];
        return view('stok_pakan.tbh_baris_stok', $data);
    }
    public function tambah_baris_stok_vitamin(Request $r)
    {
        $data = [
            'produk' => DB::select("SELECT * FROM tb_produk_perencanaan as a where a.kategori in('obat_pakan','obat_air')"),
            'count' => $r->count,
            'kategori' => 'vitamin'
        ];
        return view('stok_pakan.tbh_baris_stok', $data);
    }

    public function save_vaksin(Request $r)
    {
        $data = $r->validate([
            'tgl' => ['required', 'date'],
            'id_kandang' => ['required', 'integer', 'exists:kandang,id_kandang'],
            'id_pakan' => ['required', 'integer', 'exists:tb_produk_perencanaan,id_produk'],
            'stok' => ['required', 'numeric', 'min:0.01'],
        ]);

        $produkVaksin = DB::table('tb_produk_perencanaan')
            ->where('id_produk', $data['id_pakan'])
            ->where('kategori', 'vaksin')
            ->first();

        if (!$produkVaksin) {
            return redirect()->route('dashboard_kandang.index')
                ->with('error', 'Produk yang dipilih bukan kategori vaksin.');
        }

        $akunVaksin = DB::table('akun_perkiraan')
            ->where('aktif', 1)
            ->where('nama', 'Vaksin Ayam Belum Terbiayakan')
            ->first();
        $akunBiayaVaksin = DB::table('akun_perkiraan')
            ->where('aktif', 1)
            ->where('nama', 'Biaya Pokok Penjualan Telur (Vaksin) Tanpa Stok')
            ->first();

        if (!$akunVaksin || !$akunBiayaVaksin) {
            return redirect()->route('dashboard_kandang.index')->with(
                'error',
                'Akun Vaksin Ayam Belum Terbiayakan atau Biaya Pokok Penjualan Telur (Vaksin) Tanpa Stok belum tersedia/aktif.'
            );
        }

        DB::transaction(function () use ($data, $produkVaksin, $akunVaksin, $akunBiayaVaksin) {
            $namaKandang = DB::table('kandang')
                ->where('id_kandang', $data['id_kandang'])
                ->value('nm_kandang');
            $keterangan = 'Pemakaian vaksin ' . $produkVaksin->nm_produk . ' - Kandang ' . $namaKandang;

            $barisStok = DB::table('stok_produk_perencanaan')
                ->where('id_pakan', $data['id_pakan'])
                ->lockForUpdate()
                ->get(['pcs', 'pcs_kredit', 'total_rp', 'biaya_dll']);

            $stokTersedia = (float) $barisStok->sum(fn ($stok) => $stok->pcs - $stok->pcs_kredit);
            if ($stokTersedia < $data['stok']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'stok' => 'Stok vaksin tidak mencukupi. Stok tersedia: ' . $stokTersedia,
                ]);
            }

            $totalQtyMasuk = (float) $barisStok->sum('pcs');
            $totalNilaiMasuk = (float) $barisStok->sum(
                fn ($stok) => $stok->pcs > 0 ? $stok->total_rp + $stok->biaya_dll : 0
            );
            $hargaSatuan = $totalQtyMasuk > 0 ? $totalNilaiMasuk / $totalQtyMasuk : 0;
            $nilaiPemakaian = round($hargaSatuan * $data['stok'], 2);

            $notaTerakhir = DB::table('stok_produk_perencanaan')
                ->where('no_nota', 'like', 'VAKKLR-%')
                ->orderByDesc('id_stok_telur')
                ->lockForUpdate()
                ->value('no_nota');
            $urutan = $notaTerakhir
                ? ((int) str_replace('VAKKLR-', '', $notaTerakhir)) + 1
                : 1000;
            $noNota = 'VAKKLR-' . $urutan;

            DB::table('stok_produk_perencanaan')->insert([
                'id_kandang' => $data['id_kandang'],
                'id_pakan' => $data['id_pakan'],
                'pcs' => 0,
                'pcs_kredit' => $data['stok'],
                'total_rp' => $nilaiPemakaian,
                'biaya_dll' => 0,
                'tgl' => $data['tgl'],
                'no_nota' => $noNota,
                'admin' => auth()->user()->name,
            ]);

            $sekarang = now();
            $batchId = DB::table('impor_jurnal_perkiraan')->insertGetId([
                'nama_file' => 'Pemakaian Vaksin ' . $noNota,
                'hash_file' => hash('sha256', strtolower('Pemakaian Vaksin') . '|' . $noNota),
                'periode_awal' => $data['tgl'],
                'periode_akhir' => $data['tgl'],
                'jumlah_transaksi' => 1,
                'jumlah_detail' => 2,
                'total_debit' => $nilaiPemakaian,
                'total_kredit' => $nilaiPemakaian,
                'status' => 'aktif',
                'diimpor_oleh' => auth()->id(),
                'created_at' => $sekarang,
                'updated_at' => $sekarang,
            ]);

            DB::table('jurnal_perkiraan')->insert([
                [
                    'id_impor_jurnal_perkiraan' => $batchId,
                    'id_akun_perkiraan' => $akunBiayaVaksin->id_akun_perkiraan,
                    'tanggal' => $data['tgl'],
                    'nomor_transaksi' => $noNota,
                    'tipe_transaksi' => 'Pemakaian Vaksin',
                    'urutan_detail' => 1,
                    'deskripsi' => $keterangan,
                    'debit' => $nilaiPemakaian,
                    'kredit' => 0,
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ],
                [
                    'id_impor_jurnal_perkiraan' => $batchId,
                    'id_akun_perkiraan' => $akunVaksin->id_akun_perkiraan,
                    'tanggal' => $data['tgl'],
                    'nomor_transaksi' => $noNota,
                    'tipe_transaksi' => 'Pemakaian Vaksin',
                    'urutan_detail' => 2,
                    'deskripsi' => $keterangan,
                    'debit' => 0,
                    'kredit' => $nilaiPemakaian,
                    'created_at' => $sekarang,
                    'updated_at' => $sekarang,
                ],
            ]);
        });

        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Pemakaian vaksin dan jurnal berhasil disimpan');
    }

    public function history_pakvit(Request $r)
    {
        $jenis = $r->jenis;
        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-d');
        $jenisMapping = [
            'pakan' => "'pakan'",
            'pakan_opname' => "'pakan'",
            'vitamin' => "'obat_air', 'obat_pakan'",
            'vitamin_opname' => "'obat_air', 'obat_pakan'",
        ];
        $jenisQ = $jenisMapping[$jenis];

        $whereOpname = in_array($jenis, ['pakan_opname', 'vitamin_opname']) ?  " AND a.h_opname = 'Y'" : '';
        $historyBaru = DB::select("SELECT a.h_opname,a.no_nota,a.admin,a.tgl,a.id_pakan,b.nm_produk,a.pcs,a.pcs_kredit,a.total_rp,a.biaya_dll,c.stok,d.sum_ttl_rp,d.pcs_sum_ttl_rp FROM `stok_produk_perencanaan` as a 
        LEFT JOIN tb_produk_perencanaan as b ON a.id_pakan = b.id_produk
        LEFT JOIN (
            SELECT a.id_pakan, (sum(a.pcs) - sum(a.pcs_kredit)) as stok
                    FROM stok_produk_perencanaan as a 
                    group by a.id_pakan
        ) as c ON a.id_pakan = c.id_pakan
        LEFT JOIN (
            SELECT a.id_pakan,sum(a.total_rp + a.biaya_dll) as sum_ttl_rp, sum(pcs) as pcs_sum_ttl_rp FROM stok_produk_perencanaan as a
                    WHERE a.h_opname = 'T' AND a.pcs != 0 and  a.admin not in('import','nanda')
                    GROUP BY a.id_pakan
        ) as d on a.id_pakan = d.id_pakan
        WHERE b.kategori IN ($jenisQ) AND a.tgl BETWEEN '$tgl1' AND '$tgl2' AND a.h_opname = 'Y'
        ORDER BY a.pcs DESC;");

        $history = DB::select("SELECT a.no_nota,a.h_opname,a.admin,a.tgl,a.id_pakan, b.nm_produk, a.pcs, a.pcs_kredit, c.nm_satuan, a.total_rp
        FROM stok_produk_perencanaan as a 
        left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
        left join tb_satuan as c on c.id_satuan = b.dosis_satuan
        where b.kategori in($jenisQ) AND a.tgl BETWEEN '$tgl1' AND '$tgl2' $whereOpname ORDER BY a.h_opname ASC;");

        $data = [
            'history' => $historyBaru,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'jenis' => $jenis
        ];
        return view('stok_pakan.history_opname', $data);
    }
}
