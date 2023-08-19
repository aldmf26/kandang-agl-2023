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

            'vitamin' => DB::select("SELECT a.id_pakan, b.nm_produk, sum(a.pcs) as pcs_debit, sum(a.pcs_kredit) as pcs_kredit, c.nm_satuan
            FROM stok_produk_perencanaan as a 
            left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
            left join tb_satuan as c on c.id_satuan = b.dosis_satuan
            where b.kategori in('obat_pakan','obat_air')
            group by a.id_pakan;"),

            'total_populasi' => $pop,

            'vaksin' => DB::table('tb_vaksin_perencanaan as a')->join('kandang as b', 'a.id_kandang', 'b.id_kandang')->get(),

        ];
        return view('stok_pakan.stok', $data);
    }

    public function history_stok(Request $r)
    {
        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-t');

        $history = DB::select("SELECT a.h_opname,a.admin,a.tgl,a.id_pakan, b.nm_produk, sum(a.pcs) as pcs, sum(a.pcs_kredit) as pcs_kredit, c.nm_satuan
        FROM stok_produk_perencanaan as a 
        left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
        left join tb_satuan as c on c.id_satuan = b.dosis_satuan
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
            if($r->selisih[$x] != 0) {
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
                        'total_rp' => 0
                    ];
                    DB::table('stok_produk_perencanaan')->insert($datas);
                }
                return redirect()->route('dashboard_kandang.print_opname', "PAKVITOPN-".$no_nota)->with('sukses', 'Data berhasil di simpan');
            } else {
                return redirect()->route('dashboard_kandang.index')->with('error', 'GAGAL OPNAME KARENA DATA SELISIH KOSONG!');
            }
        }
    }

    public function print_opname($no_nota)
    {
        $history = DB::select("SELECT a.admin,a.tgl,a.id_pakan,b.nm_produk,a.pcs,a.pcs_kredit,a.total_rp,a.biaya_dll,c.stok,d.sum_ttl_rp,d.pcs_sum_ttl_rp FROM `stok_produk_perencanaan` as a 
        LEFT JOIN tb_produk_perencanaan as b ON a.id_pakan = b.id_produk
        LEFT JOIN (
            SELECT a.id_pakan, (sum(a.pcs) - sum(a.pcs_kredit)) as stok
                    FROM stok_produk_perencanaan as a 
                    group by a.id_pakan
        ) as c ON a.id_pakan = c.id_pakan
        LEFT JOIN (
            SELECT a.id_pakan,sum(a.total_rp + a.biaya_dll) as sum_ttl_rp, sum(pcs) as pcs_sum_ttl_rp FROM stok_produk_perencanaan as a
                    WHERE a.h_opname = 'T' AND a.pcs != 0 
                    GROUP BY a.id_pakan
        ) as d on a.id_pakan = d.id_pakan
        WHERE a.no_nota = '$no_nota' ORDER BY a.pcs DESC;");
        $data = [
            'title' => 'Nota Opname Pakan dan Vitamin',
            'no_nota' => $no_nota,
            'history' => $history
        ];
        return view('stok_pakan.cek',$data);
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
        DB::table('tb_vaksin_perencanaan')->insertGetId([
            'tgl' => $r->tgl,
            'id_kandang' => $r->id_kandang,
            'nm_vaksin' => $r->nm_vaksin,
            'qty' => $r->stok,
            'ttl_rp' => $r->ttl_rp,
            'biaya_dll' => $r->biaya_dll,
            'admin' => auth()->user()->name
        ]);

        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data berhasil di simpan');
    }

    public function history_pakvit(Request $r)
    {
        $jenis = $r->jenis;
        $tgl1 = $r->tgl1 ?? date('Y-m-01');
        $tgl2 = $r->tgl2 ?? date('Y-m-d');
        $jenisQ = $jenis != 'pakan' ? "'obat_air', 'obat_pakan'" : "'pakan'";
        $history = DB::select("SELECT a.no_nota,a.h_opname,a.admin,a.tgl,a.id_pakan, b.nm_produk, a.pcs, a.pcs_kredit, c.nm_satuan, a.total_rp
        FROM stok_produk_perencanaan as a 
        left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
        left join tb_satuan as c on c.id_satuan = b.dosis_satuan
        where b.kategori in($jenisQ) AND a.tgl BETWEEN '$tgl1' AND '$tgl2';");

        $data = [
            'history' => $history,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'jenis' => $jenis
        ];
        return view('stok_pakan.history_opname', $data);
    }
}
