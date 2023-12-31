<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SettingHal;

class PenjualanUmumController extends Controller
{
    protected $produk;
    public $akunPenjualan = '34';
    public $akunPiutangDagang = '12';

    protected $tgl1, $tgl2, $id_proyek, $period, $id_buku;

    public function __construct(Request $r)
    {
        $this->produk = Produk::with('satuan')->where([['kontrol_stok', 'Y'], ['kategori_id', 3]])->get();

        if (empty($r->period)) {
            $this->tgl1 = date('Y-m-01');
            $this->tgl2 = date('Y-m-t');
        } elseif ($r->period == 'daily') {
            $this->tgl1 = date('Y-m-d');
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'weekly') {
            $this->tgl1 = date('Y-m-d', strtotime("-6 days"));
            $this->tgl2 = date('Y-m-d');
        } elseif ($r->period == 'mounthly') {
            $bulan = $r->bulan;
            $tahun = $r->tahun;
            $tglawal = "$tahun" . "-" . "$bulan" . "-" . "01";
            $tglakhir = "$tahun" . "-" . "$bulan" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tglawal));
            $this->tgl2 = date('Y-m-t', strtotime($tglakhir));
        } elseif ($r->period == 'costume') {
            $this->tgl1 = $r->tgl1;
            $this->tgl2 = $r->tgl2;
        } elseif ($r->period == 'years') {
            $tahun = $r->tahunfilter;
            $tgl_awal = "$tahun" . "-" . "01" . "-" . "01";
            $tgl_akhir = "$tahun" . "-" . "12" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tgl_awal));
            $this->tgl2 = date('Y-m-t', strtotime($tgl_akhir));
        }

        $this->id_proyek = $r->id_proyek ?? 0;
        $this->id_buku = $r->id_buku ?? 2;
    }

    public function index()
    {
        $tgl1 = $this->tgl1;
        $tgl2 = $this->tgl2;
        $id_user = auth()->user()->id;
        $penjualan = DB::select("SELECT *, sum(a.total_rp) as total, count(*) as ttl_produk  FROM `penjualan_agl` as a
        LEFT JOIN customer as b ON a.id_customer = b.id_customer
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.urutan");
        $ttlRp = 0;
        foreach($penjualan as $p) {
              $ttlRp += $p->total;
        }
        $data = [
            'title' => 'Penjualan Umum',
            'penjualan' => $penjualan,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'ttlRp' => $ttlRp,
            'user' => User::where('posisi_id', 1)->get(),
            'halaman' => 17,
            'create' => SettingHal::btnHal(71, $id_user),
            'edit' => SettingHal::btnHal(74, $id_user),
            'detail' => SettingHal::btnHal(75, $id_user),
            'delete' => SettingHal::btnHal(76, $id_user),
        ];
        return view('penjualan2.penjualan', $data);
    }

    public function add()
    {
        $nota = buatNota('penjualan_agl', 'urutan');
        $data = [
            'title' => 'Tambah Penjualan Umum',
            'customer' => DB::table('customer')->get(),
            'produk' => $this->produk,
            'akun' => Akun::all(),
            'no_nota' => $nota
        ];
        return view('penjualan2.add', $data);
    }

    public function tbh_add(Request $r)
    {
        $data = [
            'count' => $r->count,
            'produk' => $this->produk,
        ];
        return view('penjualan2.tbh_add', $data);
    }

    public function tbh_pembayaran(Request $r)
    {
        $data = [
            'count' => $r->count,
            'akun' => Akun::all()
        ];
        return view('penjualan2.tbh_pembayaran', $data);
    }

    public function store(Request $r)
    {
        $nm_customer = DB::table('customer')->where('id_customer', $r->id_customer)->first()->nm_customer;
        $ttlDebit = 0;

        for ($i = 0; $i < count($r->akun_pembayaran); $i++) {
            $ttlDebit += $r->debit[$i] ?? 0 - $r->kredit[$i] ?? 0;
        }
        $max_akun2 = DB::table('jurnal')->latest('urutan')->where('id_akun', $this->akunPenjualan)->first();
        $akun2 = DB::table('akun')->where('id_akun', $this->akunPenjualan)->first();
        $urutan2 = empty($max_akun2) ? '1001' : ($max_akun2->urutan == 0 ? '1001' : $max_akun2->urutan + 1);

        $dataK = [
            'tgl' => $r->tgl,
            'no_nota' => 'PAGL-' . $r->no_nota,
            'id_akun' => $this->akunPenjualan,
            'id_buku' => '10',
            'ket' => 'PAGL-' . $r->nota_manual,
            'no_urut' => $akun2->inisial . '-' . $urutan2,
            'urutan' => $urutan2,
            'kredit' => $ttlDebit,
            'debit' => 0,
            'admin' => auth()->user()->name,
        ];
        $penjualan = Jurnal::create($dataK);


        for ($i = 0; $i < count($r->akun_pembayaran); $i++) {
            $id_akun = $r->akun_pembayaran[$i];

            $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', $id_akun)->first();
            $akun = DB::table('akun')->where('id_akun', $id_akun)->first();
            $urutan = empty($max_akun) ? '1001' : ($max_akun->urutan == 0 ? '1001' : $max_akun->urutan + 1);

            Jurnal::create([
                'tgl' => $r->tgl,
                'id_akun' => $id_akun,
                'id_buku' => 10,
                'no_nota' => 'PAGL-' . $r->no_nota,
                'ket' => "Penjualan $nm_customer",
                'debit' => $r->debit[$i] ?? 0,
                'kredit' => $r->kredit[$i] ?? 0,
                'no_urut' => $akun->inisial . '-' . $urutan,
                'urutan' => $urutan,
                'admin' => auth()->user()->name,
            ]);

            if ($id_akun == $this->akunPiutangDagang) {
                DB::table('invoice_agl')->insert([
                    'no_penjualan' => $r->nota_manual,
                    'no_nota' => 'PAGL-' . $r->no_nota,
                    'tgl' => $r->tgl,
                    'ket' => $r->ket,
                    'total_rp' => $ttlDebit,
                    'status' => 'unpaid',
                    'admin' => auth()->user()->name
                ]);
            }
        }

        for ($i = 0; $i < count($r->id_produk); $i++) {
            DB::table('penjualan_agl')->insert([
                'urutan' => $r->no_nota,
                'nota_manual' => $r->nota_manual,
                'tgl' => $r->tgl,
                'kode' => 'PAGL',
                'id_customer' => $r->id_customer,
                'driver' => $r->driver,
                'id_produk' => $r->id_produk[$i],
                'qty' => $r->qty[$i],
                'rp_satuan' => $r->rp_satuan[$i],
                'total_rp' => $r->total_rp[$i],
                'ket' => $r->ket,
                'id_jurnal' => $penjualan->id,
                'admin' => auth()->user()->name
            ]);
            $getProduk = DB::table('tb_stok_produk')->where('id_produk', $r->id_produk[$i])->orderBy('id_stok_produk', 'DESC')->first();
            $notaProduk = buatNota('tb_stok_produk', 'urutan');
            $jml_sebelumnya = empty($getProduk) ? 0 : $getProduk->jml_sebelumnya ?? 0;
            $jml_sesudahnya = $jml_sebelumnya - $r->qty[$i];

            DB::table("tb_stok_produk")->insert([
                'id_produk' => $r->id_produk[$i],
                'urutan' => $notaProduk,
                'no_nota' => 'SK-' . $notaProduk,
                'tgl' => $r->tgl,
                'jenis' => 'selesai',
                'status' => 'keluar',
                'jml_sebelumnya' => $jml_sebelumnya,
                'jml_sesudahnya' => $jml_sesudahnya,
                'debit' => 0,
                'kredit' => $r->qty[$i],
                'rp_satuan' => 0,
                'ket' => 'PAGL-' . $r->no_nota,
                'kategori_id' => 3,
                'departemen_id' => 1,
                'admin' => auth()->user()->name,
            ]);
        }

        return redirect()->route('penjualan2.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function edit(Request $r)
    {
        $penjualan = DB::selectOne("SELECT *, sum(a.total_rp) as total, count(*) as ttl_produk  FROM `penjualan_agl` as a
        LEFT JOIN customer as b ON a.id_customer = b.id_customer
        WHERE a.urutan = '$r->urutan' ");
        $data = [
            'title' => 'Edit Penjualan Umum',
            'customer' => DB::table('customer')->get(),
            'produk' => $this->produk,
            'getProduk' => DB::table('penjualan_agl as a')
                ->join('tb_produk as b', 'a.id_produk', 'b.id_produk')
                ->where('urutan', $r->urutan)
                ->get(),
            'getPenjualan' => $penjualan,
            'getPembayaran' => DB::table('jurnal')->where([['no_nota', 'PAGL-' . $r->urutan], ['id_akun', '!=', $this->akunPenjualan]])->get(),
            'akun' => Akun::all(),
            'no_nota' => $penjualan->urutan
        ];
        return view('penjualan2.edit', $data);
    }

    public function update(Request $r)
    {
        $cek = DB::table('invoice_agl')->where('no_nota', 'PAGL-' . $r->no_nota)->first();
        if ($cek) {
            return redirect()->route('penjualan2.index')->with('error', 'Gagal Edit Karena nota Piutang Sudah PAID !');
        }
        $ttlDebit = 0;
        $getProduk = DB::table('tb_stok_produk')->where('id_produk', 12)->orderBy('id_stok_produk', 'DESC')->first();
        DB::table('tb_stok_produk')->where('no_nota', 'PAGL-' . $r->no_nota)->delete();
        DB::table('jurnal')->where('no_nota', 'PAGL-' . $r->no_nota)->delete();
        DB::table('penjualan_agl')->where('urutan', $r->no_nota)->delete();


        $max_akun2 = DB::table('jurnal')->latest('urutan')->where('id_akun', $this->akunPenjualan)->first();
        $akun2 = DB::table('akun')->where('id_akun', $this->akunPenjualan)->first();
        $urutan2 = empty($max_akun2) ? '1001' : ($max_akun2->urutan == 0 ? '1001' : $max_akun2->urutan + 1);

        $dataK = [
            'tgl' => $r->tgl,
            'no_nota' => 'PAGL-' . $r->no_nota,
            'id_akun' => $this->akunPenjualan,
            'ket' => 'PAGL-' . $r->nota_manual,
            'no_urut' => $akun2->inisial . '-' . $urutan2,
            'urutan' => $urutan2,
            'kredit' => $ttlDebit,
            'debit' => 0,
            'admin' => auth()->user()->name,
        ];
        $penjualan = Jurnal::create($dataK);

        for ($i = 0; $i < count($r->akun_pembayaran); $i++) {
            $ttlDebit += $r->debit[$i] ?? 0 - $r->kredit[$i] ?? 0;

            $id_akun = $r->akun_pembayaran[$i];

            $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', $id_akun)->first();
            $akun = DB::table('akun')->where('id_akun', $id_akun)->first();
            $urutan = empty($max_akun) ? '1001' : ($max_akun->urutan == 0 ? '1001' : $max_akun->urutan + 1);

            Jurnal::create([
                'tgl' => $r->tgl,
                'id_akun' => $id_akun,
                'no_nota' => 'PAGL-' . $r->no_nota,
                'ket' => 'PAGL-' . $r->no_nota,
                'debit' => $r->debit[$i] ?? 0,
                'kredit' => $r->kredit[$i] ?? 0,
                'no_urut' => $akun->inisial . '-' . $urutan,
                'urutan' => $urutan,
                'admin' => auth()->user()->name,
            ]);
            if ($id_akun == $this->akunPiutangDagang) {
                DB::table('invoice_agl')->insert([
                    'no_penjualan' => $r->nota_manual,
                    'no_nota' => 'PAGL-' . $r->no_nota,
                    'tgl' => $r->tgl,
                    'ket' => $r->ket,
                    'total_rp' => $ttlDebit,
                    'status' => 'unpaid',
                    'admin' => auth()->user()->name
                ]);
            }
        }

        for ($i = 0; $i < count($r->id_produk); $i++) {
            DB::table('penjualan_agl')->insert([
                'urutan' => $r->no_nota,
                'nota_manual' => $r->nota_manual,
                'tgl' => $r->tgl,
                'kode' => 'PAGL',
                'id_customer' => $r->id_customer,
                'driver' => $r->driver,
                'id_produk' => $r->id_produk[$i],
                'qty' => $r->qty[$i],
                'rp_satuan' => $r->rp_satuan[$i],
                'total_rp' => $r->total_rp[$i],
                'ket' => $r->ket,
                'id_jurnal' => $penjualan->id,
                'admin' => auth()->user()->name
            ]);
            $notaProduk = buatNota('tb_stok_produk', 'urutan');
            $jml_sebelumnya = $getProduk->jml_sebelumnya ?? 0;
            $jml_sesudahnya = $jml_sebelumnya - $r->qty[$i];

            DB::table("tb_stok_produk")->insert([
                'id_produk' => $r->id_produk[$i],
                'urutan' => $notaProduk,
                'no_nota' => 'SK-' . $notaProduk,
                'tgl' => $r->tgl,
                'jenis' => 'selesai',
                'status' => 'keluar',
                'jml_sebelumnya' => $jml_sebelumnya,
                'jml_sesudahnya' => $jml_sesudahnya,
                'debit' => 0,
                'kredit' => $r->qty[$i],
                'rp_satuan' => 0,
                'ket' => 'PAGL-' . $r->no_nota,
                'gudang_id' => $getProduk->gudang_id,
                'kategori_id' => 3,
                'departemen_id' => 1,
                'admin' => auth()->user()->name,
            ]);
        }

        return redirect()->route('penjualan2.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function detail($no_nota)
    {
        $penjualan = DB::selectOne("SELECT *, sum(a.total_rp) as total, count(*) as ttl_produk  FROM `penjualan_agl` as a
        LEFT JOIN customer as b ON a.id_customer = b.id_customer
        WHERE a.urutan = '$no_nota' ");
        $data = [
            'title' => 'Detail Penjaulan Umum',
            'head_jurnal' => $penjualan,
            'produk' => DB::table('penjualan_agl as a')
                ->select('a.admin as admin', 'a.*', 'b.nm_produk')
                ->join('tb_produk as b', 'a.id_produk', 'b.id_produk')
                ->where('urutan', $no_nota)
                ->get()
        ];
        return view('penjualan2.detail', $data);
    }

    public function print(Request $r)
    {
        $penjualan = DB::selectOne("SELECT *, sum(a.total_rp) as total, count(*) as ttl_produk  FROM `penjualan_agl` as a
        LEFT JOIN customer as b ON a.id_customer = b.id_customer
        WHERE a.urutan = '$r->urutan' ");
        $data = [
            'title' => 'Cetak Penjaulan Umum',
            'detail' => $penjualan,
            'produk' => DB::table('penjualan_agl as a')
                ->join('tb_produk as b', 'a.id_produk', 'b.id_produk')
                ->where('urutan', $r->urutan)
                ->get()
        ];
        return view('penjualan2.print', $data);
    }

    public function delete(Request $r)
    {
        DB::table('tb_stok_produk')->where('no_nota', 'PAGL-' . $r->urutan)->delete();
        DB::table('jurnal')->where('no_nota', 'PAGL-' . $r->urutan)->delete();
        DB::table('penjualan_agl')->where('urutan', $r->urutan)->delete();

        return redirect()->route('penjualan2.index', ['period' => 'costume', 'tgl1' => $r->tgl1, 'tgl2' => $r->tgl2, 'id_proyek' => 0])->with('sukses', 'Data Berhasil Dihapus');
    }
}
