<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class rakController extends Controller
{
    public function create(Request $r)
    {
        $cek = DB::table('tb_rak_telur')->where('no_nota', 'LIKE', '%RAKMSK%')->latest('no_nota')->first();
        $no_nota = empty($cek) ? 1001 : str()->remove('RAKMSK-', $cek->no_nota) + 1;
        $wadah = [];
        foreach ($r->all() as $key => $value) {
            $wadah[$key] = str_replace(',', '', $value);
        }
        DB::table('tb_rak_telur')->insert([
            'tgl' => $wadah['tgl'],
            'debit' => $wadah['debit'],
            'total_rp' => $wadah['total_rp'],
            'biaya_dll' => $wadah['biaya_dll'],
            'admin' => auth()->user()->name,
            'no_nota' => "RAKMSK-$no_nota"
        ]);
        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data Berhasil dimasukan');
    }

    public function opname(Request $r)
    {
        $cek = DB::table('tb_rak_telur')->where('no_nota', 'LIKE', '%RAKOPN%')->latest('no_nota')->first();
        $no_nota = empty($cek) ? 1001 : str()->remove('RAKOPN-', $cek->no_nota) + 1;
        $wadah = [];
        foreach ($r->all() as $key => $value) {
            $wadah[$key] = str_replace(',', '', $value);
        }

        $selisih = $wadah['selisih'];
        $stokProgram = $wadah['stok_program'];
        $stokAktual = $wadah['stok_aktual'];
        $tgl = $wadah['tgl'];
        $getBiaya = DB::selectOne("SELECT sum(a.total_rp + a.biaya_dll) as ttl_rp, sum(a.debit - a.kredit) as ttl_debit FROM `tb_rak_telur` as a
                  WHERE a.id_gudang = 1 AND a.no_nota LIKE '%RAKMSK%'");
        $hargaSatuan = $getBiaya->ttl_rp / $getBiaya->ttl_debit;
        $rupiah = $hargaSatuan * $selisih;


        if ($selisih != 0) {
            if ($selisih < 0) {
                $qty_selisih = $selisih * -1;
                $datas = [
                    'debit' => $qty_selisih,
                    'opname' => 'T',
                    'tgl' => $tgl,
                    'admin' => auth()->user()->name,
                    'no_nota' => "RAKOPN-" . $no_nota,
                    'h_opname' => 'Y',
                    'selisih' => $selisih,
                    'total_rp' => $rupiah
                ];
            } else {
                $datas = [
                    'debit' => $stokAktual,
                    'opname' => 'T',
                    'tgl' => $tgl,
                    'admin' => auth()->user()->name,
                    'no_nota' => "RAKOPN-" . $no_nota,
                    'h_opname' => 'Y',
                    'selisih' => $selisih,
                    'kredit' => $stokProgram,
                    'total_rp' => $rupiah
                ];
            }
            DB::table('tb_rak_telur')->insert($datas);
        }

        return redirect()->route('rak.print_opname', "RAKOPN-" . $no_nota)->with('sukses', 'Data Berhasil dimasukan');
    }

    public function print_opname($no_nota, $print = null)
    {
        $history = DB::selectOne("SELECT * FROM `tb_rak_telur` WHERE no_nota = '$no_nota'");
        $get = DB::selectOne("SELECT sum(a.total_rp + a.biaya_dll) as ttl_rp, sum(a.debit - a.kredit) as ttl_debit FROM `tb_rak_telur` as a
                                    WHERE a.id_gudang = 1 AND a.no_nota LIKE '%RAKMSK%'");
        $data = [
            'title' => 'Nota Opname Rak Telur',
            'no_nota' => $no_nota,
            'history' => $history,
            'getBiaya' => $get,
        ];
        $view = empty($print) ? 'cek_rak' : 'print_rak';
        return view("dashboard_kandang.modal.$view", $data);
    }
}
