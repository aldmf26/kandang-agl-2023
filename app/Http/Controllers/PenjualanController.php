<?php

namespace App\Http\Controllers;

use App\Models\Jurnal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class PenjualanController extends Controller
{
    protected $tgl1, $tgl2, $period;
    public function __construct(Request $r)
    {
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
            $tgl = "$tahun" . "-" . "$bulan" . "-" . "01";

            $this->tgl1 = date('Y-m-01', strtotime($tgl));
            $this->tgl2 = date('Y-m-t', strtotime($tgl));
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
    }


    public function index(Request $r)
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;

        $data =  [
            'title' => 'Penjualan Agrilaras',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'invoice' => DB::select("SELECT a.no_nota, a.tgl, a.tipe, a.admin, b.nm_customer, sum(a.total_rp) as ttl_rp, a.status, c.debit_bayar , c.kredit_bayar, a.urutan_customer, a.driver, a.lokasi, d.pcs as rak_tf, a.import
            FROM invoice_telur as a 
            left join customer as b on b.id_customer = if(a.id_customer = 0 , a.id_customer2, a.id_customer)
            left join (
                SELECT c.no_nota, sum(c.debit) as debit_bayar, sum(c.kredit) as kredit_bayar
                FROM bayar_telur as c
                group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            left join rak_telur_penjualan as d on d.no_nota = a.no_nota
            where a.tgl between '$tgl1' and '$tgl2' and a.lokasi ='mtd'
            group by a.no_nota
            order by a.urutan DESC
            ")

        ];
        return view('penjualan_agl.index', $data);
    }
    public function plus_customer(Request $r)
    {
        $customer = DB::table('customer')->where('kode_customer', $r->kode_customer)->first();
        if ($customer) {
            return redirect()->route('penjualan_agrilaras')->with('error', 'Data sudah ada');
        }
        $data = [
            'nm_customer' => $r->nm_customer,
            'kode_customer' => $r->kode_customer,
            'active' => 'Y'
        ];
        DB::table('customer')->insert($data);
        return redirect()->route('penjualan_agrilaras')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function tbh_invoice_telur(Request $r)
    {
        $max = DB::table('invoice_telur')->latest('urutan')->first();

        if (empty($max) || $max->urutan == '0') {
            $nota_t = '1001';
        } else {
            $nota_t = $max->urutan + 1;
        }
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'customer' => DB::table('customer')->where('active', 'Y')->get(),
            'nota' => $nota_t,
            'akun' => DB::table('akun')->whereIn('id_klasifikasi', ['1', '7'])->get()
        ];
        return view('penjualan_agl.invoice', $data);
    }

    public function loadkginvoice(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'kandang' => DB::table('kandang')->where('selesai', 'T')->get(),
            'count_kandang' => $r->count_kandang
        ];
        return view('penjualan_agl.load_penjualankg', $data);
    }

    public function tambah_baris_kandang_kg(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'count' => $r->count,
            'kandang' => $r->kandang
        ];
        return view('penjualan_agl.tbh_bariskg', $data);
    }
    public function tambah_baris_kg(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'count' => $r->count
        ];
        return view('penjualan_agl.tbh_bariskg', $data);
    }

    public function tbh_pembayaran(Request $r)
    {
        $data = [
            'count' => $r->count,
            'akun' => DB::table('akun')->whereIn('id_klasifikasi', ['1', '7'])->get()
        ];
        return view('penjualan_agl.tbh_pembayaran', $data);
    }

    public function save_penjualan_telur(Request $r)
    {
        $max = DB::table('invoice_telur')->latest('urutan')->where('lokasi', 'mtd')->first();

        if (empty($max) || $max->urutan == '0') {
            $nota_t = '1001';
        } else {
            $nota_t = $max->urutan + 1;
        }

        $max_customer = DB::table('invoice_telur')->latest('urutan_customer')->where('id_customer', $r->customer)->first();

        if (empty($max_customer)) {
            $urutan_cus = '1';
        } else {
            $urutan_cus = $max_customer->urutan_customer + 1;
        }




        for ($x = 0; $x < count($r->id_produk); $x++) {


            $data = [
                'tgl' => $r->tgl,
                'id_customer' => $r->customer,
                'tipe' => $r->tipe[$x],
                'no_nota' => 'TM' . $nota_t,
                'id_produk' => $r->id_produk[$x],
                'pcs' => $r->pcs[$x],
                'kg' => $r->kg[$x],
                'ikat' => $r->ikat[$x],
                'kg_jual' => $r->kg_jual[$x],
                'rp_satuan' => $r->rp_satuan[$x],
                'total_rp' => $r->total_rp[$x],
                'admin' => Auth::user()->name,
                'urutan' => $nota_t,
                'urutan_customer' => $urutan_cus,
                'driver' => 'kosong',
                'lokasi' => 'mtd'
            ];
            DB::table('invoice_telur')->insert($data);


            $data = [
                'id_telur' => $r->id_produk[$x],
                'tgl' => $r->tgl,
                'pcs_kredit' => $r->pcs[$x],
                'kg_kredit' => $r->kg[$x],
                'admin' => Auth::user()->name,
                'id_gudang' => '2',
                'check' => 'Y',
                'nota_transfer' => 'TM' . $nota_t,
            ];
            DB::table('stok_telur')->insert($data);
        }
        $data = [
            'no_nota' => 'TM' . $nota_t,
            'pcs' => $r->pcs_rak,
        ];
        DB::table('rak_telur_penjualan')->insert($data);
        return redirect()->route('penjualan_agrilaras')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function detail_invoice_telur(Request $r)
    {
        $data = [
            'invoice' => DB::select("SELECT *
            FROM invoice_telur as a
            LEFT JOIN telur_produk as b on b.id_produk_telur = a.id_produk
            LEFT JOIN customer as c on c.id_customer = a.id_customer
            where a.no_nota = '$r->no_nota'
            "),
            'head_invoice' => DB::selectOne("SELECT a.*, c.nm_customer,d.pcs as rak_tf
                FROM invoice_telur as a
                LEFT JOIN customer as c on c.id_customer = if(a.id_customer = 0 , a.id_customer2, a.id_customer)
                LEFT JOIN rak_telur_penjualan as d on d.no_nota = a.no_nota
                where a.no_nota = '$r->no_nota'
            ")
        ];
        return view('penjualan_agl.detail_invoice', $data);
    }

    public function loadpcsinvoice(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
        ];
        return view('penjualan_agl.load_penjualanpcs', $data);
    }

    public function tambah_baris_pcs(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'count' => $r->count
        ];
        return view('penjualan_agl.tbh_barispcs', $data);
    }

    public function edit_invoice_telur(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'customer' => DB::table('customer')->where('active', 'Y')->get(),
            'akun' => DB::table('akun')->whereIn('id_klasifikasi', ['1', '7'])->get(),
            'nota' => $r->no_nota,
            'invoice2' => DB::selectOne("SELECT a.urutan, a.urutan_customer, a.tgl, a.id_customer, a.id_produk, a.tipe, a.driver, sum(a.total_rp) as total_rp FROM invoice_telur as a where a.no_nota='$r->no_nota'"),
            'invoice' => DB::table('invoice_telur')->where('no_nota', $r->no_nota)->get(),
            'rak' => DB::table('rak_telur_penjualan')->where('no_nota', $r->no_nota)->first(),

        ];
        return view('penjualan_agl.edit_invoice', $data);
    }

    public function loadkginvoiceedit(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'invoice' => DB::table('invoice_telur')->where('no_nota', $r->no_nota)->get(),

        ];
        return view('penjualan_agl.load_penjualankgedit', $data);
    }
    public function loadpcsinvoiceedit(Request $r)
    {
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'invoice' => DB::table('invoice_telur')->where('no_nota', $r->no_nota)->get(),

        ];
        return view('penjualan_agl.load_penjualanpcsedit', $data);
    }

    public function edit_penjualan_telur(Request $r)
    {

        DB::table('invoice_telur')->where('no_nota', $r->no_nota)->delete();
        DB::table('stok_telur')->where('nota_transfer', $r->no_nota)->delete();
        DB::table('rak_telur_penjualan')->where('no_nota', $r->no_nota)->delete();

        $max_customer = DB::table('invoice_telur')->latest('urutan_customer')->where('id_customer', $r->customer)->first();

        if (empty($max_customer)) {
            $urutan_cus = '1';
        } else {
            $urutan_cus = $max_customer->urutan_customer + 1;
        }

        for ($x = 0; $x < count($r->id_produk); $x++) {


            $data = [
                'tgl' => $r->tgl,
                'id_customer' => $r->customer,
                'tipe' => $r->tipe[$x],
                'no_nota' => $r->no_nota,
                'id_produk' => $r->id_produk[$x],
                'pcs' => $r->pcs[$x],
                'kg' => $r->kg[$x],
                'ikat' => $r->ikat[$x],
                'kg_jual' => $r->kg_jual[$x],
                'rp_satuan' => $r->rp_satuan[$x],
                'total_rp' => $r->total_rp[$x],
                'admin' => Auth::user()->name,
                'urutan' => $r->urutan,
                'urutan_customer' => $urutan_cus,
                'driver' => 'kosong',
                'lokasi' => 'mtd'
            ];
            DB::table('invoice_telur')->insert($data);


            $data = [
                'id_telur' => $r->id_produk[$x],
                'tgl' => $r->tgl,
                'pcs_kredit' => $r->pcs[$x],
                'kg_kredit' => $r->kg[$x],
                'admin' => Auth::user()->name,
                'id_gudang' => '2',
                'check' => 'Y',
                'nota_transfer' => $r->no_nota,
            ];
            DB::table('stok_telur')->insert($data);
        }
        $data = [
            'no_nota' => $r->no_nota,
            'pcs' => $r->pcs_rak,
        ];
        DB::table('rak_telur_penjualan')->insert($data);
        return redirect()->route('penjualan_agrilaras')->with('sukses', 'Data berhasil di edit');
    }

    public function delete_invoice_telur(Request $r)
    {
        DB::table('invoice_telur')->where('no_nota', $r->no_nota)->delete();
        DB::table('jurnal')->where('no_nota', $r->no_nota)->delete();
        DB::table('bayar_telur')->where('no_nota', $r->no_nota)->delete();
        DB::table('stok_telur')->where('nota_transfer', $r->no_nota)->delete();

        return redirect()->route('penjualan_agrilaras')->with('sukses', 'Data berhasil ditambahkan');
    }
}
