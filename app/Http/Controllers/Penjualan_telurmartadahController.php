<?php

namespace App\Http\Controllers;

use App\Exports\PenjualanTelurMtdExport;
use App\Models\Gudang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Penjualan_telurmartadahController extends Controller
{
    protected $tgl1, $tgl2, $period, $produk, $gudang;
    public function __construct(Request $r)
    {
        $this->produk = Produk::with('satuan')->where([['kontrol_stok', 'Y'], ['kategori_id', 3]])->get();
        $this->gudang = Gudang::where('kategori_id', 3)->get();
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
    public function penjualan_telur()
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;
        $transfer = DB::select("SELECT a.void, a.cek, a.tgl, a.no_nota, a.customer,  b.nm_telur,  sum(a.total_rp) as ttl_rp , a.admin, a.admin_cek, c.nm_customer
        FROM invoice_telur as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk
        left join customer as c on c.id_customer = a.id_customer2
        WHERE a.lokasi = 'mtd' and a.tgl between '$tgl1' and '$tgl2'
        GROUP by a.no_nota
        order by a.no_nota DESC
        ");
        $ttl = 0;
        $ttlBelumDicek = 0;
        foreach ($transfer as $d) {
            $ttl += $d->ttl_rp;
            if (empty($d->admin_cek)) {
                $ttlBelumDicek += $d->ttl_rp;
            }
        }
        $data = [
            'title' => 'Penjualan Telur Martadah',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'penjualan' => $transfer,
            'ttl_rp' => $ttl,
            'ttlBelumDicek' => $ttlBelumDicek,
        ];
        return view('dashboard_kandang.penjualan_telur.penjualan_telur', $data);
    }

    public function get_detail_penjualan_mtd(Request $r)
    {
        $data = [
            'telur' => DB::select("SELECT * FROM invoice_mtd as a left join telur_produk as b on b.id_produk_telur = a.id_produk where a.no_nota = '$r->no_nota'"),
            'no_nota' => $r->no_nota
        ];
        return view('dashboard_kandang.penjualan_telur.detail_telur', $data);
    }

    public function add_penjualan_telur()
    {
        $max = DB::table('invoice_telur')->latest('urutan')->where('lokasi', 'mtd')->first();


        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->urutan + 1;
        }

        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'customer' => DB::select('SELECT * FROM `customer` WHERE npwp IS NOT NULL OR ktp IS NOT NULL;'),
            'nota' => $nota_t,
            'akun' => DB::table('akun')->whereIn('id_klasifikasi', ['1', '7'])->get()
        ];

        return view('dashboard_kandang.penjualan_telur.add_penjualan_telur', $data);
    }

    public function tambah_baris_jual_mtd(Request $r)
    {
        $data = [
            'count' => $r->count,
            'produk' => DB::table('telur_produk')->get(),
        ];
        return view('dashboard_kandang.penjualan_telur.tambah_baris', $data);
    }

    public function edit_telur(Request $r)
    {
        $penjualan_mtd = DB::select("SELECT a.*, b.nm_telur FROM invoice_mtd as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk
        where a.no_nota = '$r->no_nota';");

        $penjualan_mtd_detail = DB::selectOne("SELECT a.*, b.nm_telur, c.urutan FROM invoice_mtd as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk
        left join invoice_telur as c on c.no_nota = a.no_nota
        where a.no_nota = '$r->no_nota';");
        $data =  [
            'title' => 'Edit Stok Telur',
            'invoice' => $penjualan_mtd,
            'invoice2' => $penjualan_mtd_detail,
            'kandang' => DB::table('kandang')->get(),
            'produk' => DB::table('telur_produk')->get(),
        ];
        return view('dashboard_kandang.penjualan_telur.edit', $data);
    }

    public function save_penjualan_telur(Request $r)
    {
        $max = DB::table('invoice_telur')->latest('urutan')->where('lokasi', 'mtd')->first();

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->urutan + 1;
        }

        $max_customer = DB::table('invoice_telur')->latest('urutan_customer')->where('id_customer', $r->customer)->first();

        if (empty($max_customer)) {
            $urutan_cus = '1';
        } else {
            $urutan_cus = $max_customer->urutan_customer + 1;
        }

        DB::table('invoice_mtd')->where('no_nota', $r->no_nota)->delete();
        DB::table('invoice_telur')->where('no_nota', $r->no_nota)->delete();
        DB::table('stok_telur')->where('nota_transfer', $r->no_nota)->delete();

        $pcs_pcs = $r->pcs_pcs;
        $kg_pcs = $r->kg_pcs;
        $rp_pcs = $r->rp_pcs;

        $ikat = $r->ikat;
        $kg_ikat = $r->kg_ikat;
        $rp_ikat = $r->rp_ikat;

        $pcs_kg = $r->pcs_kg;
        $kg_kg = $r->kg_kg;
        $rak_kg = $r->rak_kg;
        $rp_kg = $r->rp_kg;
        $kg_kg_kotor = $r->kg_kg_kotor;


        for ($x = 0; $x < count($r->id_produk); $x++) {
            $pcs_ikat = $ikat[$x] * 180;
            $total_pcs = $pcs_ikat + $pcs_pcs[$x] + $pcs_kg[$x];

            $kg_bersih_ikat = $kg_ikat[$x] - $ikat[$x];
            $rk = $pcs_kg[$x] / 30;
            $rak_kali = round($rk  * 0.12, 1);
            $kg_bersih_kg = $kg_kg[$x] + $rak_kali;
            $total_kg_kotor = $kg_pcs[$x] + $kg_ikat[$x] + $kg_kg_kotor[$x];

            $total_kg_bersih = $kg_bersih_ikat + $kg_kg[$x];
            $total_rp_satuan = $rp_pcs[$x] + $rp_ikat[$x] + $rp_kg[$x];

            $ttl_rp_pcs = $pcs_pcs[$x] * $rp_pcs[$x];
            $ttl_rp_ikat = $kg_bersih_ikat * $rp_ikat[$x];
            $ttl_rp_kg = $kg_kg[$x] * $r->rp_kg[$x];

            $total_rp = $ttl_rp_pcs + $ttl_rp_ikat + $ttl_rp_kg;


            $data = [
                'tgl' => $r->tgl,
                'customer' => $r->customer,
                'id_customer2' => $r->id_customer2,
                'no_nota' => 'TM' . $nota_t,
                'id_produk' => $r->id_produk[$x],
                'pcs' => $total_pcs,
                'kg' => $total_kg_kotor,
                'kg_jual' => $total_kg_bersih,
                'ikat' => $ikat[$x],
                'rp_satuan' => $total_rp_satuan,
                'total_rp' => $total_rp,
                'admin' => auth()->user()->name,
                'urutan' => $nota_t,
                'urutan_customer' => $urutan_cus,
                'driver' => '',
                'lokasi' => 'mtd'
            ];
            DB::table('invoice_telur')->insert($data);
            $data = [
                'tgl' => $r->tgl,
                'customer' => $r->customer,
                'no_hp' => $r->no_hp,
                'no_nota' => 'TM' . $nota_t,
                'id_produk' => $r->id_produk[$x],

                'pcs_pcs' => $pcs_pcs[$x],
                'kg_pcs' => $kg_pcs[$x],
                'rp_pcs' => $rp_pcs[$x],

                'ikat' => $ikat[$x],
                'kg_ikat' => $kg_ikat[$x],
                'rp_ikat' => $rp_ikat[$x],

                'pcs_kg' => $pcs_kg[$x],
                'kg_kg_kotor' => $kg_kg_kotor[$x],
                'kg_kg' => $kg_kg[$x],
                'rak_kg' =>  $rk,
                'rp_kg' => $rp_kg[$x],
            ];
            DB::table('invoice_mtd')->insert($data);


            DB::table('stok_telur')->insert([
                'id_kandang' => 0,
                'id_telur' => $r->id_produk[$x],
                'tgl' => $r->tgl,
                'pcs_kredit' => $total_pcs,
                'kg_kredit' => $total_kg_kotor,
                'pcs' => 0,
                'kg' => 0,
                'admin' => auth()->user()->name,
                'id_gudang' => 1,
                'nota_transfer' => 'TM' . $nota_t,
                'ket' => '',
                'jenis' => 'penjualan',
                'check' => 'Y'
            ]);
        }
        $no_nota = 'TM' . $nota_t;
        return redirect()->route('dashboard_kandang.cek_penjualan_telur', ['no_nota' => $no_nota])->with('sukses', 'Data berhasil ditambahkan');
    }



    public function detail_penjualan_mtd(Request $r)
    {
        $penjualan_mtd = DB::select("SELECT a.*, b.nm_telur FROM invoice_mtd as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk
        where a.no_nota = '$r->no_nota';");

        $penjualan_mtd_detail = DB::selectOne("SELECT a.*, b.nm_telur FROM invoice_mtd as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk
        where a.no_nota = '$r->no_nota';");

        $data = [
            'invoice' => $penjualan_mtd,
            'invoice2' => $penjualan_mtd_detail,
        ];

        return view('dashboard_kandang.penjualan_telur.detail', $data);
    }

    public function save_edit_telur(Request $r)
    {
        $max = DB::table('invoice_telur')->latest('urutan')->where('lokasi', 'mtd')->first();

        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->urutan + 1;
        }

        $max_customer = DB::table('invoice_telur')->latest('urutan_customer')->where('id_customer', $r->customer)->first();

        if (empty($max_customer)) {
            $urutan_cus = '1';
        } else {
            $urutan_cus = $max_customer->urutan_customer + 1;
        }
        $voucher = DB::table('tb_void')->where([['no_nota', $r->no_nota], ['voucher', $r->voucher], ['status', 'T']])->count();
        $voucherUpdate = $voucher > 0 || $r->tgl == date('Y-m-d') ? true : false;
        $no_nota = $r->no_nota;
        if ($voucherUpdate) {
            $cekAdmin = DB::table('invoice_telur')->where('no_nota', $no_nota)->first();

            $cekTransfer = DB::table('stok_telur')->where([['tgl', '>=', $r->tgl], ['jenis', 'Opname']])->first();


            if (empty($cekTransfer)) {
                DB::table('stok_telur')->where('nota_transfer', $no_nota)->delete();
            }
            DB::table('tb_void')->where([['no_nota', $no_nota], ['voucher', $r->voucher]])->update(['status' => 'Y']);

            DB::table('invoice_mtd')->where('no_nota', $no_nota)->delete();
            DB::table('invoice_telur')->where('no_nota', $no_nota)->delete();


            $pcs_pcs = $r->pcs_pcs;
            $kg_pcs = $r->kg_pcs;
            $rp_pcs = $r->rp_pcs;

            $ikat = $r->ikat;
            $kg_ikat = $r->kg_ikat;
            $rp_ikat = $r->rp_ikat;

            $pcs_kg = $r->pcs_kg;
            $kg_kg = $r->kg_kg;
            $kg_kg_kotor = $r->kg_kg_kotor;
            $rak_kg = $r->rak_kg;
            $rp_kg = $r->rp_kg;


            for ($x = 0; $x < count($r->id_produk); $x++) {
                $pcs_ikat = $ikat[$x] * 180;
                $total_pcs = $pcs_ikat + $pcs_pcs[$x] + $pcs_kg[$x];

                $kg_bersih_ikat = $kg_ikat[$x] - $ikat[$x];
                $rk = $pcs_kg[$x] / 30;
                $rak_kali = round($rk  * 0.12, 1);
                $kg_bersih_kg = $kg_kg[$x] + $rak_kali;
                $total_kg_kotor = $kg_pcs[$x] + $kg_ikat[$x] + $kg_kg_kotor[$x];

                $total_kg_bersih = $kg_bersih_ikat + $kg_kg[$x];
                $total_rp_satuan = $rp_pcs[$x] + $rp_ikat[$x] + $rp_kg[$x];

                $ttl_rp_pcs = $pcs_pcs[$x] * $rp_pcs[$x];
                $ttl_rp_ikat = $kg_bersih_ikat * $rp_ikat[$x];
                $ttl_rp_kg = $kg_kg[$x] * $r->rp_kg[$x];

                $total_rp = $ttl_rp_pcs + $ttl_rp_ikat + $ttl_rp_kg;

                $data = [
                    'tgl' => $r->tgl,
                    'customer' => $r->customer,
                    'no_nota' => $no_nota,
                    'id_produk' => $r->id_produk[$x],
                    'pcs' => $total_pcs,
                    'kg' => $total_kg_kotor,
                    'kg_jual' => $total_kg_bersih,
                    'ikat' => $ikat[$x],
                    'rp_satuan' => $total_rp_satuan,
                    'total_rp' => $total_rp,
                    'admin' => auth()->user()->name,
                    'urutan' => $r->urutan,
                    'urutan_customer' => $urutan_cus,
                    'driver' => '',
                    'lokasi' => 'mtd'
                ];
                DB::table('invoice_telur')->insert($data);
                $data = [
                    'tgl' => $r->tgl,
                    'customer' => $r->customer,
                    'no_hp' => $r->no_hp,
                    'no_nota' => $no_nota,
                    'id_produk' => $r->id_produk[$x],

                    'pcs_pcs' => $pcs_pcs[$x],
                    'kg_pcs' => $kg_pcs[$x],
                    'rp_pcs' => $rp_pcs[$x],

                    'ikat' => $ikat[$x],
                    'kg_ikat' => $kg_ikat[$x],
                    'rp_ikat' => $rp_ikat[$x],

                    'pcs_kg' => $pcs_kg[$x],
                    'kg_kg' => $kg_kg[$x],
                    'kg_kg_kotor' => $kg_kg_kotor[$x],
                    'rak_kg' =>  $rk,
                    'rp_kg' => $rp_kg[$x],
                    'void' => 'T'
                ];
                DB::table('invoice_mtd')->insert($data);

                if (empty($cekTransfer)) {
                    DB::table('stok_telur')->insert([
                        'id_kandang' => 0,
                        'id_telur' => $r->id_produk[$x],
                        'tgl' => $r->tgl,
                        'pcs_kredit' => $total_pcs,
                        'kg_kredit' => $total_kg_kotor,
                        'pcs' => 0,
                        'kg' => 0,
                        'admin' => auth()->user()->name,
                        'id_gudang' => 1,
                        'nota_transfer' => $no_nota,
                        'ket' => '',
                        'jenis' => 'penjualan',
                        'check' => 'Y'
                    ]);
                }
            }
        } else {
            return redirect()->route('dashboard_kandang.edit_telur', ['no_nota' => $no_nota])->with('error', 'Voucher Update Salah!');
        }


        return redirect()->route('dashboard_kandang.cek_penjualan_telur', ['no_nota' => $no_nota])->with('sukses', 'Data berhasil ditambahkan');
    }

    public function delete_penjualan_mtd(Request $r)
    {
        DB::table('invoice_mtd')->where('no_nota', $r->no_nota)->delete();
        DB::table('invoice_telur')->where('no_nota', $r->no_nota)->delete();
        DB::table('stok_telur')->where('nota_transfer', $r->no_nota)->delete();
        return redirect()->route('dashboard_kandang.penjualan_telur')->with('sukses', 'Data berhasil dihapus');
    }
    public function void_penjualan_mtd(Request $r)
    {
        void($r->no_nota, 'Penjualan Martadah', 'invoice_telur');

        return redirect()->route('dashboard_kandang.penjualan_telur')->with('sukses', 'Data berhasil ditambahkan void');
    }

    public function cek_penjualan_telur(Request $r)
    {
        $penjualan_mtd = DB::select("SELECT a.*, b.nm_telur FROM invoice_mtd as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk
        where a.no_nota = '$r->no_nota';");

        $penjualan_mtd_detail = DB::selectOne("SELECT a.*, b.nm_telur FROM invoice_mtd as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk
        where a.no_nota = '$r->no_nota';");

        $data = [
            'title' => 'Cek Nota',
            'invoice' => $penjualan_mtd,
            'invoice2' => $penjualan_mtd_detail,
        ];
        return view('dashboard_kandang.penjualan_telur.cek', $data);
    }

    public function penjualan_telur_export($tgl1, $tgl2)
    {

        $tbl = DB::select("SELECT 
        a.void, 
        a.cek, 
        a.tgl, 
        a.no_nota, 
        a.customer, 
        b.nm_telur, 
        sum(a.total_rp) as ttl_rp, 
        a.admin, 
        a.admin_cek, 
        c.pcs_pcs, 
        c.kg_pcs as pcs_kg, 
        c.rp_pcs, 
        c.ikat as ikat_ikat, 
        c.kg_ikat as ikat_kg, 
        c.rp_ikat as rp_ikat, 
        c.pcs_kg as rak_pcs, 
        c.kg_kg as rak_kg_kotor, 
        c.rak_kg as rak_kg_bersih, 
        c.rp_kg  as rp_rak
      FROM 
        invoice_telur as a 
        left join telur_produk as b on b.id_produk_telur = a.id_produk 
        LEFT JOIN (
          SELECT 
            no_nota, 
            sum(pcs_pcs) as pcs_pcs, 
            SUM(kg_pcs) as kg_pcs, 
            sum(rp_pcs) as rp_pcs, 
            sum(ikat) as ikat, 
            sum(kg_ikat) as kg_ikat, 
            sum(rp_ikat) as rp_ikat, 
            sum(pcs_kg) as pcs_kg, 
            sum(kg_kg) as kg_kg, 
            sum(rak_kg) as rak_kg, 
            sum(rp_kg) as rp_kg 
          FROM 
            `invoice_mtd` 
          WHERE 
            tgl BETWEEN '$tgl1' 
            AND '$tgl2' 
          GROUP BY 
            no_nota
        ) as c ON c.no_nota = a.no_nota 
      WHERE 
        a.lokasi = 'mtd' 
        and a.tgl between '$tgl1' 
        and '$tgl2' 
      GROUP by 
        a.no_nota 
      order by 
        a.no_nota DESC;
        ");

        $totalrow = count($tbl) + 1;

        return Excel::download(new PenjualanTelurMtdExport($tbl, $totalrow), 'Export Penjualan Telur Mtd.xlsx');
    }
}
