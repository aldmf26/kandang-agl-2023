<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ObatPakanController extends Controller
{
    public function load_stok_pakan(Request $r)
    {
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

            'vaksin' => DB::table('tb_vaksin_perencanaan as a')->join('kandang as b', 'a.id_kandang', 'b.id_kandang')->get(),

        ];
        return view('stok_pakan.stok', $data);
    }

    public function history_stok(Request $r)
    {
        $tgl1 = $r->tgl1 ?? '2022-01-01';
        $tgl2 = $r->tgl2 ?? date('Y-m-t');

        $data = [
            'stok' => DB::select("SELECT a.tgl, b.nm_produk, a.pcs, a.pcs_kredit, a.admin, a.h_opname
            FROM stok_produk_perencanaan as a 
            left join tb_produk_perencanaan as b on b.id_produk = a.id_pakan
            where a.tgl BETWEEN '$tgl1' and '$tgl2' and a.id_pakan = '$r->id_pakan'
            GROUP by a.id_stok_telur;"),
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
            where b.kategori in('obat_pakan','obat_air') and a.opname = 'T' and a.tgl between '2020-01-01' and '$tgl'
            group by a.id_pakan;"),
            'tgl' => $tgl
        ];
        return view('opname.opname_pakan', $data);
    }

    public function save_opname_pakan(Request $r)
    {
        $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '4')->first();
        if (empty($max)) {
            $no_nota = '1000';
        } else {
            $no_nota = $max->nomor_nota + 1;
        }
        // DB::table('notas')->insert(['nomor_nota' => $no_nota, 'id_buku' => '4']);
        // $no_nota = strtoupper(str()->random(5));
        for ($x = 0; $x < count($r->id_pakan); $x++) {
            $id_pakan = $r->id_pakan[$x];
            $hrga = DB::selectOne("SELECT sum((a.total_rp + a.biaya_dll)/a.pcs) as rata_rata
            FROM stok_produk_perencanaan as a 
            where a.id_pakan = '$id_pakan' and a.pcs != '0' and a.h_opname ='T'
            group by a.id_pakan;");

            $selisih = $r->stk_program[$x] - $r->stk_aktual[$x];
            if ($selisih < 0) {
                $qty_selisih = $selisih * -1;
                // $data = [
                //     'id_akun' => '522',
                //     'id_buku' => '4',
                //     'ket' => 'Penyesuian stok pakan',
                //     'debit' => empty($hrga->rata_rata) ? '0' : $qty_selisih * $hrga->rata_rata,
                //     'kredit' => '0',
                //     'tgl' => $r->tgl,
                //     'no_nota' => 'JPP-' . $no_nota,
                //     'admin' => auth()->user()->name,
                // ];
                // DB::table('jurnal')->insert($data);
                // $data = [
                //     'id_akun' => '521',
                //     'id_buku' => '4',
                //     'ket' => 'Penyesuian stok pakan',
                //     'debit' => 0,
                //     'kredit' => empty($hrga->rata_rata) ? '0' : $qty_selisih * $hrga->rata_rata,
                //     'tgl' => $r->tgl,
                //     'no_nota' => 'JPP-' . $no_nota,
                //     'admin' => auth()->user()->name,
                // ];
                // DB::table('jurnal')->insert($data);
                $datas = [
                    'pcs' => $r->stk_aktual[$x],
                    'id_pakan' => $r->id_pakan[$x],
                    'opname' => 'T',
                    'tgl' => $r->tgl,
                    'admin' => auth()->user()->name,
                    'no_nota' => $no_nota,
                    'h_opname' => 'Y',
                    'pcs' => $qty_selisih,
                    'pcs_kredit' => 0,
                    'total_rp' => empty($hrga->rata_rata) ? '0' : $qty_selisih * $hrga->rata_rata
                ];
                DB::table('stok_produk_perencanaan')->insert($datas);
            } else {
                $qty_selisih = $selisih;
                // $data = [
                //     'id_akun' => '521',
                //     'id_buku' => '4',
                //     'ket' => 'Penyesuian stok pakan',
                //     'debit' => $qty_selisih * $hrga->rata_rata,
                //     'kredit' => '0',
                //     'tgl' => $r->tgl,
                //     'no_nota' => 'JPP-' . $no_nota,
                //     'admin' => auth()->user()->name,
                // ];
                // DB::table('jurnal')->insert($data);
                // $data = [
                //     'id_akun' => '522',
                //     'id_buku' => '4',
                //     'ket' => 'Penyesuian stok pakan',
                //     'debit' => 0,
                //     'kredit' => $qty_selisih * $hrga->rata_rata,
                //     'tgl' => $r->tgl,
                //     'no_nota' => 'JPP-' . $no_nota,
                //     'admin' => auth()->user()->name,
                // ];
                // DB::table('jurnal')->insert($data);

                $datas = [
                    'pcs' => $r->stk_aktual[$x],
                    'id_pakan' => $r->id_pakan[$x],
                    'opname' => 'T',
                    'tgl' => $r->tgl,
                    'admin' => auth()->user()->name,
                    'no_nota' => $no_nota,
                    'h_opname' => 'Y',
                    'pcs' => 0,
                    'pcs_kredit' => $qty_selisih,
                    'total_rp' => empty($hrga->rata_rata) ? '0' : $qty_selisih * $hrga->rata_rata
                ];
                DB::table('stok_produk_perencanaan')->insert($datas);
            }

            DB::table('stok_produk_perencanaan')->where(['id_pakan' => $r->id_pakan[$x], 'opname' => 'T'])->update(['opname' => 'Y', 'no_nota' => $no_nota]);
        }
        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data berhasil di simpan');
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
        for ($x = 0; $x < count($r->id_pakan); $x++) {
            if ($r->kategori == 'pakan') {
                $data = [
                    'id_pakan' => $r->id_pakan[$x],
                    'pcs' => $r->pcs[$x] * 50000,
                    'total_rp' => $r->ttl_rp[$x],
                    'biaya_dll' => $r->biaya_dll[$x],
                    'admin' => auth()->user()->name,
                    'tgl' => $r->tgl
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
                    'tgl' => $r->tgl
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
}
