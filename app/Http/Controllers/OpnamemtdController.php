<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OpnamemtdController extends Controller
{
    public function index(Request $r)
    {
        $max = DB::table('stok_telur')->where('jenis', 'Opname')->latest('nota_transfer')->first();

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = str()->remove('Opname-',$max->nota_transfer) + 1;
        }


        $data = [
            'title' => 'Opname Telur',
            'produk' => DB::table('telur_produk')->get(),
            'id_gudang' => $r->id_gudang,
            'urutan' => $nota_t

        ];
        return view('opname_telur_mtd.index', $data);
    }

    public function save_opname_telur_mtd(Request $r)
    {

        $urutan_opname = DB::selectOne("SELECT a.nota_transfer as urutan
        FROM stok_telur as a WHERE a.jenis = 'Opname' ORDER BY a.id_stok_telur DESC;");

        if (empty($urutan_opname) || $urutan_opname->urutan == '0') {
            $urutan = 1001;
        } else {
            $urutan = str()->remove('Opname-', $urutan_opname->urutan) + 1;
        }

        $max_customer = DB::table('invoice_telur')->latest('urutan_customer')->where('id_customer', '3')->first();

        if (empty($max_customer)) {
            $urutan_cus = '1';
        } else {
            $urutan_cus = $max_customer->urutan_customer + 1;
        }

        for ($x = 0; $x < count($r->id_telur); $x++) {


            if ($r->pcs_selisih[$x] + $r->kg_selisih[$x] == 0) {
            } else {
                DB::table('stok_telur')->where(['opname' => 'T', 'id_gudang' => $r->id_gudang, 'id_telur' => $r->id_telur[$x]])->update(['opname' => 'Y']);
                $data = [
                    'id_telur' => $r->id_telur[$x],
                    'tgl' => $r->tgl,
                    'pcs' => $r->pcs[$x],
                    'kg' => $r->kg[$x],
                    'pcs_selisih' => $r->pcs_selisih[$x],
                    'kg_selisih' => $r->kg_selisih[$x],
                    'admin' => Auth::user()->name,
                    'id_gudang' => $r->id_gudang,
                    'jenis' => 'Opname',
                    'nota_transfer' => 'Opname-' . $urutan,
                    'opname' => 'T',
                    'check' => 'Y'
                ];
                DB::table('stok_telur')->insert($data);
                $data = [
                    'id_customer' => '3',
                    'tgl' => $r->tgl,
                    'no_nota' => 'Opname-' . $urutan,
                    'urutan_customer' => $urutan_cus,
                    'pcs' => $r->pcs_selisih[$x],
                    'kg' => $r->kg_selisih[$x],
                    'admin' => Auth::user()->name,
                    'lokasi' => 'opname',
                    'id_produk' => $r->id_telur[$x],
                ];
                DB::table('invoice_telur')->insert($data);
            }
        }

        return redirect()->route('opname_cek', 'Opname-' . $urutan)->with('sukses', 'Data berhasil ditambahkan');
    }
    public function cek($no_nota, $print = null)
    {
        $data = [
            'nota' => $no_nota,
            'title' => 'Cek Opname',
            'detail' => DB::table('stok_telur')->where([['nota_transfer', $no_nota], ['jenis', 'Opname']])->first(),
            'produk' => DB::table('telur_produk')->get(),
            'datas' => DB::table('stok_telur')->where([['nota_transfer', $no_nota], ['jenis', 'Opname']])->get()
        ];
        $view = empty($print) ? 'cek' : 'print';
        return view('opname_telur_mtd.'.$view, $data);
    }

    public function bayar_opname(Request $r)
    {
        $data = [
            'title' => 'Pembayran Opname',
            'produk' => DB::table('telur_produk')->get(),
            'customer' => DB::table('customer')->get(),
            'akun' => DB::table('akun')->whereIn('id_klasifikasi', ['1', '7'])->get(),
            'nota' => $r->no_nota,
            'invoice2' => DB::selectOne("SELECT a.urutan, a.urutan_customer, a.tgl, a.id_customer, a.id_produk, a.tipe, a.driver, sum(a.total_rp) as total_rp FROM invoice_telur as a where a.no_nota='$r->no_nota'"),
            'invoice' => DB::table('invoice_telur')->where('no_nota', $r->no_nota)->get(),
        ];
        return view('opname_telur_mtd.bayar_opname', $data);
    }

    public function save_bayar_opname(Request $r)
    {
        for ($x = 0; $x < count($r->id_invoice_telur); $x++) {
            # code...
            $data = [
                'rp_satuan' => $r->rp_satuan[$x],
                'total_rp' => $r->total_rp[$x],
                'cek' => 'Y'
            ];
            DB::table('invoice_telur')->where('id_invoice_telur', $r->id_invoice_telur[$x])->update($data);
        }

        $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', '517')->first();
        $akun = DB::table('akun')->where('id_akun', '517')->first();
        $urutan = empty($max_akun) ? '1001' : ($max_akun->urutan == 0 ? '1001' : $max_akun->urutan + 1);
        $data = [
            'no_nota' => $r->no_nota,
            'id_akun' => '517',
            'ket' => 'Telur ' . $r->customer,
            'debit' => '0',
            'kredit' => $r->total_penjualan,
            'id_buku' => '6',
            'admin' => Auth::user()->name,
            'no_urut' => $akun->inisial . '-' . $urutan,
            'urutan' => $urutan,
            'tgl' => $r->tgl
        ];
        DB::table('jurnal')->insert($data);

        $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', '520')->first();
        $akun = DB::table('akun')->where('id_akun', '520')->first();
        $urutan = empty($max_akun) ? '1001' : ($max_akun->urutan == 0 ? '1001' : $max_akun->urutan + 1);
        $data = [
            'no_nota' => $r->no_nota,
            'id_akun' => '520',
            'ket' => 'Telur ' . $r->customer,
            'debit' => $r->total_penjualan,
            'kredit' => '0',
            'id_buku' => '6',
            'admin' => Auth::user()->name,
            'no_urut' => $akun->inisial . '-' . $urutan,
            'urutan' => $urutan,
            'tgl' => $r->tgl
        ];
        DB::table('jurnal')->insert($data);
        return redirect()->route('bukukan_opname_martadah')->with('sukses', 'Data berhasil di opname');
    }

    public function bukukan_opname_martadah(Request $r)
    {
        $penjualan = DB::select("SELECT a.no_nota, a.tgl, a.tipe, a.admin, b.nm_customer, sum(a.pcs) as pcs, sum(a.kg) as kg, a.status, a.cek, a.urutan_customer, a.admin
        FROM invoice_telur as a 
        left join customer as b on b.id_customer = a.id_customer
          where a.lokasi = 'opname'
        group by a.no_nota
        order by a.id_invoice_telur DESC");
        $data = [
            'title' => 'Opname Martadah',
            'invoice' => $penjualan,
        ];
        return view('opname_telur_mtd.penerimaan_uang', $data);
    }

    public function terima_opname(Request $r)
    {
        $data = [
            'title' => 'Penerimaan Uang Martadah',
            'invoice' => DB::select("SELECT FROM invoice_telur as a"),
            'produk' => DB::table('telur_produk')->get()
        ];
        return view('opname_telur_mtd.bukukan_opname', $data);
    }

    public function history_opname_mtd(Request $r)
    {
        $today = date("Y-m-d");
        $enamhari = date("Y-m-d", strtotime("-6 days", strtotime($today)));
        if (empty($r->tgl1)) {
            $tgl1 = $enamhari;
            $tgl2 = date('Y-m-d');
        } else {
            $tgl1 = $r->tgl1;
            $tgl2 = $r->tgl2;
        }
        $data = [
            'produk' => DB::table('telur_produk')->get(),
            'gudang' => DB::table('gudang_telur')->get(),
            'invoice' => DB::select("SELECT a.pcs_selisih,a.kg_selisih,a.tgl, a.nota_transfer, b.nm_telur, a.pcs, a.kg, a.admin
            FROM stok_telur as a 
            left join telur_produk as b on b.id_produk_telur = a.id_telur
            where a.tgl BETWEEN '2023-08-12' and '$tgl2' and a.id_gudang='1' and a.jenis ='opname';"),
            'tgl1' => $tgl1,
            'tgl2' => $tgl2
        ];
        return view('opname_telur_mtd.history', $data);
    }
}
