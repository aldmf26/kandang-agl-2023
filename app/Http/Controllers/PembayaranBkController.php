<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranBkController extends Controller
{
    public function index(Request $r)
    {

        $tipe = $r->tipe;
        if ($tipe == 'D') {
            $pembelian = DB::select("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where a.lunas = '$tipe'
            order by a.id_invoice_bk ASC
            ");
        } elseif (empty($tipe)) {
            $pembelian = DB::select("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            order by a.id_invoice_bk ASC
            ");
        } elseif ($tipe == 'Y') {
            $pembelian = DB::select("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where a.total_harga + c.debit - c.kredit = '0'
            order by a.id_invoice_bk ASC
            ");
        } elseif ($tipe == 'T') {
            $pembelian = DB::select("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit
            FROM invoice_bk as a 
            left join tb_suplier as b on b.id_suplier = a.id_suplier
            left join (
            SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
            group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where a.total_harga + if(c.debit is null , 0,c.debit) - if(c.kredit is null , 0 ,c.kredit) != '0'
            order by a.id_invoice_bk ASC
            ");
        }
        $listBulan = DB::table('bulan')->get();

        $draft = DB::select("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        where a.lunas = 'D'
        order by a.id_invoice_bk ASC
        ");

        $paid =  DB::select("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, c.kredit, c.debit
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(c.debit) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        where a.total_harga + c.debit - c.kredit = '0'
        order by a.id_invoice_bk ASC
        ");

        $unpaid = DB::select("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir, a.total_harga, a.lunas, if(c.kredit is null , 0 ,c.kredit) as kredit, if(c.debit is null , 0,c.debit) as debit
        FROM invoice_bk as a 
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        left join (
        SELECT c.no_nota , sum(if(c.debit is null , 0, c.debit)) as debit, sum(c.kredit) as kredit  FROM bayar_bk as c
        group by c.no_nota
        ) as c on c.no_nota = a.no_nota
        where a.total_harga + if(c.debit is null , 0,c.debit) - if(c.kredit is null , 0 ,c.kredit) != '0'
        order by a.id_invoice_bk ASC;
        ");


        $data =  [
            'title' => 'Pembayaran Bahan Baku',
            'pembelian' => $pembelian,
            'listbulan' => $listBulan,
            'tipe' => $tipe,
            'draft' => $draft,
            'paid' => $paid,
            'unpaid' => $unpaid

        ];
        return view('pembayaran_bk.index', $data);
    }

    public function add(Request $r)
    {
        $nota = $r->nota;
        $p = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir,
        a.total_harga, a.lunas,a.jumlah_pembayaran
        FROM invoice_bk as a
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$nota'
        order by a.id_invoice_bk ASC
        ");
        $bayar = DB::select("SELECT a.tgl, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$nota'
        group by a.id_bayar_bk;");


        $data = [
            'title' => 'Pembayaran Bahan Baku',
            'p' => $p,
            'bayar' => $bayar,

        ];

        return view('pembayaran_bk.add', $data);
    }

    public function save_pembayaran(Request $r)
    {
        $cfm_pembayaran = $r->cfm_pembayaran;
        $id_akun = $r->id_akun;
        $kredit = $r->kredit;
        $debit = $r->debit;
        $tgl = $r->tgl_pembayaran;
        $keterangan = $r->ket;

        for ($x = 0; $x < count($id_akun); $x++) {
            $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '2')->first();

            if (empty($max)) {
                $nota_t = '1000';
            } else {
                $nota_t = $max->nomor_nota + 1;
            }
            DB::table('notas')->insert(['nomor_nota' => $nota_t, 'id_buku' => '2']);



            $data = [
                'no_nota' => $cfm_pembayaran,
                'debit' => $debit[$x],
                'kredit' => $kredit[$x],
                'id_akun' => $id_akun[$x],
                'tgl' => $tgl[$x],
                'admin' => Auth::user()->name,
                'ket' => $keterangan[$x],
                'nota_jurnal' => 'JU-' . $nota_t,
                'bayar' => 'Y'
            ];
            DB::table('bayar_bk')->insert($data);
            if ($debit[$x] == '0') {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $kredit[$x],
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_kredit);


                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $kredit[$x],
                    'kredit' => '0',
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_debit);
            } else {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $debit[$x],
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_kredit);


                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $debit[$x],
                    'kredit' => '0',
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_debit);
            }
        }






        return redirect()->route('pembayaranbk')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function tambah(Request $r)
    {
        $nota = $r->no_nota;
        $p = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir,
        a.total_harga, a.lunas,a.jumlah_pembayaran
        FROM invoice_bk as a
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$nota'
        order by a.id_invoice_bk ASC
        ");
        $data =  [
            'count' => $r->count,
            'p' => $p

        ];
        return view('pembayaran_bk.tambah', $data);
    }

    public function edit(Request $r)
    {
        $nota = $r->nota;
        $p = DB::selectOne("SELECT a.tgl, a.no_nota,b.nm_suplier, a.suplier_akhir,
        a.total_harga, a.lunas,a.jumlah_pembayaran
        FROM invoice_bk as a
        left join tb_suplier as b on b.id_suplier = a.id_suplier
        where a.no_nota = '$nota'
        order by a.id_invoice_bk ASC
        ");
        $bayar = DB::select("SELECT a.tgl, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$nota' and a.bayar = 'T'
        group by a.id_bayar_bk;");

        $bayar2 = DB::select("SELECT a.tgl, a.id_akun, a.nota_jurnal, c.nm_suplier, b.suplier_akhir, a.kredit, d.nm_akun, a.ket, a.debit
        FROM bayar_bk as a
        left join invoice_bk as b on b.no_nota = a.no_nota
        left join tb_suplier as c on c.id_suplier = b.id_suplier 
        left join akun as d on d.id_akun = a.id_akun
        where a.no_nota = '$nota' and a.bayar = 'Y'
        group by a.id_bayar_bk;");


        $data = [
            'title' => 'Pembayaran Bahan Baku',
            'p' => $p,
            'bayar' => $bayar,
            'bayar2' => $bayar2

        ];

        return view('pembayaran_bk.edit', $data);
    }

    public function save_edit(Request $r)
    {
        $cfm_pembayaran = $r->cfm_pembayaran;
        $id_akun = $r->id_akun;
        $kredit = $r->kredit;
        $debit = $r->debit;
        $tgl = $r->tgl_pembayaran;
        $keterangan = $r->ket;
        $nota_jurnal = $r->nota_jurnal;

        for ($x = 0; $x < count($nota_jurnal); $x++) {
            DB::table('jurnal')->where('no_nota', $nota_jurnal[$x])->delete();
            DB::table('bayar_bk')->where('nota_jurnal', $nota_jurnal[$x])->delete();
        }

        for ($x = 0; $x < count($id_akun); $x++) {
            $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '2')->first();

            if (empty($max)) {
                $nota_t = '1000';
            } else {
                $nota_t = $max->nomor_nota + 1;
            }
            DB::table('notas')->insert(['nomor_nota' => $nota_t, 'id_buku' => '2']);



            $data = [
                'no_nota' => $cfm_pembayaran,
                'debit' => $debit[$x],
                'kredit' => $kredit[$x],
                'id_akun' => $id_akun[$x],
                'tgl' => $tgl[$x],
                'admin' => Auth::user()->name,
                'ket' => $keterangan[$x],
                'nota_jurnal' => 'JU-' . $nota_t,
                'bayar' => 'Y'
            ];
            DB::table('bayar_bk')->insert($data);
            if ($debit[$x] == '0') {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $kredit[$x],
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_kredit);


                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $kredit[$x],
                    'kredit' => '0',
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_debit);
            } else {
                $data_kredit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => '0',
                    'kredit' => $debit[$x],
                    'id_akun' => '512',
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_kredit);


                $data_debit = [
                    'tgl' => $tgl[$x],
                    'no_nota' => 'JU-' . $nota_t,
                    'id_buku' => '2',
                    'ket' => $keterangan[$x],
                    'debit' => $debit[$x],
                    'kredit' => '0',
                    'id_akun' => $id_akun[$x],
                    'admin' => Auth::user()->name,
                ];
                DB::table('jurnal')->insert($data_debit);
            }
        }
        return redirect()->route('pembayaranbk')->with('sukses', 'Data berhasil ditambahkan');
    }
}