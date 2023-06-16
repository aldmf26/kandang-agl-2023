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
            'invoice' => DB::select("SELECT a.no_nota, a.tgl, a.tipe, a.admin, b.nm_customer, sum(a.total_rp) as ttl_rp, a.status, c.debit_bayar , c.kredit_bayar, a.urutan_customer, a.driver
            FROM invoice_telur as a 
            left join customer as b on b.id_customer = a.id_customer
            left join (
                SELECT c.no_nota, sum(c.debit) as debit_bayar, sum(c.kredit) as kredit_bayar
                FROM bayar_telur as c
                group by c.no_nota
            ) as c on c.no_nota = a.no_nota
            where a.tgl between '$tgl1' and '$tgl2'
            group by a.no_nota
            order by a.urutan DESC
            ")

        ];
        return view('penjualan_agl.index', $data);
    }

    public function tbh_invoice_telur(Request $r)
    {
        $max = DB::table('invoice_telur')->latest('urutan')->first();

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->urutan + 1;
        }
        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'customer' => DB::table('customer')->get(),
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
        ];
        return view('penjualan_agl.load_penjualankg', $data);
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
        $max = DB::table('invoice_telur')->latest('urutan')->first();

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->urutan + 1;
        }

        $max_customer = DB::table('invoice_telur')->latest('urutan_customer')->where('id_customer', $r->customer)->first();

        if (empty($max_customer)) {
            $urutan = '1';
        } else {
            $urutan = $max_customer->urutan_customer + 1;
        }


        for ($x = 0; $x < count($r->id_produk); $x++) {

            if ($r->tipe == 'kg') {
                $data = [
                    'tgl' => $r->tgl,
                    'id_customer' => $r->customer,
                    'tipe' => $r->tipe,
                    'no_nota' => 'T' . $nota_t,
                    'id_produk' => $r->id_produk[$x],
                    'pcs' => $r->pcs[$x],
                    'kg' => $r->kg[$x],
                    'kg_jual' => $r->kg_jual[$x],
                    'rp_satuan' => $r->rp_satuan[$x],
                    'total_rp' => $r->total_rp[$x],
                    'admin' => Auth::user()->name,
                    'urutan' => $nota_t,
                    'urutan_customer' => $urutan,
                    'driver' => $r->driver
                ];
                DB::table('invoice_telur')->insert($data);
            } else {
                $data = [
                    'tgl' => $r->tgl,
                    'id_customer' => $r->customer,
                    'tipe' => $r->tipe,
                    'no_nota' => 'T' . $nota_t,
                    'id_produk' => $r->id_produk[$x],
                    'pcs' => $r->pcs[$x],
                    'kg' => $r->kg[$x],
                    'rp_satuan' => $r->rp_satuan[$x],
                    'total_rp' => $r->total_rp[$x],
                    'admin' => Auth::user()->name,
                    'urutan' => $nota_t,
                    'urutan_customer' => $urutan,
                    'driver' => $r->driver
                ];
                DB::table('invoice_telur')->insert($data);
            }
        }
        $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', '517')->first();
        $akun = DB::table('akun')->where('id_akun', '517')->first();

        $urutan = empty($max_akun) ? '1001' : ($max_akun->urutan == 0 ? '1001' : $max_akun->urutan + 1);
        $data = [
            'tgl' => $r->tgl,
            'no_nota' => 'T' . $nota_t,
            'id_akun' => '517',
            'id_buku' => '6',
            'ket' => 'Penjualan Telur',
            'debit' => 0,
            'kredit' => $r->total_penjualan,
            'admin' => Auth::user()->name,
            'no_urut' => $akun->inisial . '-' . $urutan,
            'urutan' => $urutan,
        ];
        DB::table('jurnal')->insert($data);

        $data = [
            'tgl' => $r->tgl,
            'no_nota' => 'T' . $nota_t,
            'debit' => 0,
            'kredit' => $r->total_penjualan,
        ];
        DB::table('bayar_telur')->insert($data);



        for ($x = 0; $x < count($r->id_akun); $x++) {
            $max_akun2 = DB::table('jurnal')->latest('urutan')->where('id_akun', $r->id_akun[$x])->first();
            $akun2 = DB::table('akun')->where('id_akun', $r->id_akun[$x])->first();
            $urutan2 = empty($max_akun2) ? '1001' : ($max_akun2->urutan == 0 ? '1001' : $max_akun2->urutan + 1);
            $data = [
                'tgl' => $r->tgl,
                'no_nota' => 'T' . $nota_t,
                'id_akun' => $r->id_akun[$x],
                'id_buku' => '6',
                'ket' => 'Penjualan Telur',
                'debit' => $r->debit[$x],
                'kredit' => $r->kredit[$x],
                'admin' => Auth::user()->name,
                'no_urut' => $akun2->inisial . '-' . $urutan2,
                'urutan' => $urutan2,
            ];
            DB::table('jurnal')->insert($data);


            if ($akun2->id_klasifikasi == '7') {
                $nota = 'T' . $nota_t;
                DB::table('invoice_telur')->where('no_nota', $nota)->update(['status' => 'unpaid']);
            } else {
                $data = [
                    'tgl' => $r->tgl,
                    'no_nota' => 'T' . $nota_t,
                    'debit' => $r->debit[$x],
                    'kredit' => $r->kredit[$x],
                ];
                DB::table('bayar_telur')->insert($data);
            }
        }

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
            'head_invoice' => DB::selectOne("SELECT *
                FROM invoice_telur as a
                LEFT JOIN customer as c on c.id_customer = a.id_customer
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
            'customer' => DB::table('customer')->get(),
            'akun' => DB::table('akun')->whereIn('id_klasifikasi', ['1', '7'])->get(),
            'nota' => $r->no_nota,
            'invoice2' => DB::selectOne("SELECT a.urutan, a.urutan_customer, a.tgl, a.id_customer, a.id_produk, a.tipe, a.driver, sum(a.total_rp) as total_rp FROM invoice_telur as a where a.no_nota='$r->no_nota'"),
            'jurnal' => DB::select("SELECT * FROM jurnal as a where a.no_nota = '$r->no_nota' and a.id_akun != '517'"),
            'jurnal2' => DB::selectOne("SELECT * FROM jurnal as a where a.no_nota = '$r->no_nota' and a.id_akun = '517'"),
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

    public function edit_penjualan_telur(Request $r)
    {

        DB::table('invoice_telur')->where('no_nota', $r->no_nota)->delete();
        DB::table('jurnal')->where('no_nota', $r->no_nota)->delete();
        DB::table('bayar_telur')->where('no_nota', $r->no_nota)->delete();


        $max = DB::table('invoice_telur')->latest('urutan')->first();
        $max_customer = DB::table('invoice_telur')->latest('urutan_customer')->where('id_customer', $r->customer)->first();

        if (empty($max_customer)) {
            $urutan = '1';
        } else {
            $urutan = $max_customer->urutan_customer + 1;
        }

        if ($r->id_customer == $r->customer) {
            $urutan_cus = $r->urutan_customer;
        } else {
            $urutan_cus = $urutan;
        }

        for ($x = 0; $x < count($r->id_produk); $x++) {

            if ($r->tipe == 'kg') {
                $data = [
                    'tgl' => $r->tgl,
                    'id_customer' => $r->customer,
                    'tipe' => $r->tipe,
                    'no_nota' => $r->no_nota,
                    'id_produk' => $r->id_produk[$x],
                    'pcs' => $r->pcs[$x],
                    'kg' => $r->kg[$x],
                    'kg_jual' => $r->kg_jual[$x],
                    'rp_satuan' => $r->rp_satuan[$x],
                    'total_rp' => $r->total_rp[$x],
                    'admin' => Auth::user()->name,
                    'urutan' => $r->urutan,
                    'urutan_customer' => $urutan_cus,
                    'driver' => $r->driver
                ];
                DB::table('invoice_telur')->insert($data);
            } else {
                $data = [
                    'tgl' => $r->tgl,
                    'id_customer' => $r->customer,
                    'tipe' => $r->tipe,
                    'no_nota' => $r->no_nota,
                    'id_produk' => $r->id_produk[$x],
                    'pcs' => $r->pcs[$x],
                    'kg' => $r->kg[$x],
                    'rp_satuan' => $r->rp_satuan[$x],
                    'total_rp' => $r->total_rp[$x],
                    'admin' => Auth::user()->name,
                    'urutan' => $r->urutan,
                    'urutan_customer' => $urutan_cus,
                    'driver' => $r->driver
                ];
                DB::table('invoice_telur')->insert($data);
            }
        }

        $max_akun = DB::table('jurnal')->latest('urutan')->where('id_akun', '517')->first();
        $akun = DB::table('akun')->where('id_akun', '517')->first();

        $urutan = empty($max_akun) ? '1001' : ($max_akun->urutan == 0 ? '1001' : $max_akun->urutan + 1);
        $data = [
            'tgl' => $r->tgl,
            'no_nota' => $r->no_nota,
            'id_akun' => '517',
            'id_buku' => '6',
            'ket' => 'Penjualan Telur',
            'debit' => 0,
            'kredit' => $r->total_penjualan,
            'admin' => Auth::user()->name,
            'no_urut' => $r->no_urut_penjualan,
            'urutan' => $r->urutan_penjualan,
        ];
        DB::table('jurnal')->insert($data);

        $data = [
            'tgl' => $r->tgl,
            'no_nota' => $r->no_nota,
            'debit' => 0,
            'kredit' => $r->total_penjualan,
        ];
        DB::table('bayar_telur')->insert($data);



        for ($x = 0; $x < count($r->id_akun); $x++) {
            $max_akun2 = DB::table('jurnal')->latest('urutan')->where('id_akun', $r->id_akun[$x])->first();
            $akun2 = DB::table('akun')->where('id_akun', $r->id_akun[$x])->first();
            $urutan2 = empty($max_akun2) ? '1001' : ($max_akun2->urutan == 0 ? '1001' : $max_akun2->urutan + 1);

            if ($r->id_akun2[$x] == $r->id_akun[$x]) {
                $uruan_jurnal = $r->urutan_jurnal[$x];
            } else {
                $uruan_jurnal = $urutan2;
            }

            $data = [
                'tgl' => $r->tgl,
                'no_nota' => $r->no_nota,
                'id_akun' => $r->id_akun[$x],
                'id_buku' => '6',
                'ket' => 'Penjualan Telur',
                'debit' => $r->debit[$x],
                'kredit' => $r->kredit[$x],
                'admin' => Auth::user()->name,
                'no_urut' => $akun2->inisial . '-' . $uruan_jurnal,
                'urutan' => $uruan_jurnal,
            ];
            DB::table('jurnal')->insert($data);


            if ($akun2->id_klasifikasi == '7') {
                $nota = $r->no_nota;
                DB::table('invoice_telur')->where('no_nota', $nota)->update(['status' => 'unpaid']);
            } else {
                $data = [
                    'tgl' => $r->tgl,
                    'no_nota' => $r->no_nota,
                    'debit' => $r->debit[$x],
                    'kredit' => $r->kredit[$x],
                ];
                DB::table('bayar_telur')->insert($data);
            }
        }

        return redirect()->route('penjualan_agrilaras')->with('sukses', 'Data berhasil ditambahkan');
    }

    public function delete_invoice_telur(Request $r)
    {
        DB::table('invoice_telur')->where('no_nota', $r->no_nota)->delete();
        DB::table('jurnal')->where('no_nota', $r->no_nota)->delete();
        DB::table('bayar_telur')->where('no_nota', $r->no_nota)->delete();

        return redirect()->route('penjualan_agrilaras')->with('sukses', 'Data berhasil ditambahkan');
    }
}
