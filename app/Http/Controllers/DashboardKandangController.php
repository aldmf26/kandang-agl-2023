<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class DashboardKandangController extends Controller
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

    public function index()
    {
        $tgl = date('Y-m-d');
        $data = [
            'title' => 'Dashboard Kandang',
            'kandang' => DB::table('kandang as a')
                ->select(DB::raw("FLOOR(DATEDIFF('$tgl', a.chick_in) / 7) AS mgg"), 'a.*')
                ->where('selesai', 'T')
                ->get(),
            'telur' => DB::table('telur_produk')->get(),
            'produkPakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'pakan')->get(),
            'produk' => $this->produk,
            'stok_ayam' => DB::selectOne("SELECT sum(a.debit - a.kredit) as saldo_kandang FROM stok_ayam as a where a.id_gudang = '1' and a.jenis = 'ayam'"),
            'stok_ayam_bjm' => DB::selectOne("SELECT sum(a.debit - a.kredit) as saldo_bjm FROM stok_ayam as a where a.id_gudang = '2' and a.jenis = 'ayam'"),
            'stok_karung' => DB::selectOne("SELECT sum(a.debit - a.kredit) as saldo_karung FROM stok_ayam as a where a.id_gudang = '1' and a.jenis = 'karung'"),

            'stok_pupuk' => DB::selectOne("SELECT sum(a.debit - a.kredit) as saldo_pupuk FROM stok_ayam as a where a.id_gudang = '1' and a.jenis = 'pupuk'"),
            'history_ayam' => DB::table('stok_ayam')->where('jenis', 'ayam')->where('id_gudang', '1')->get(),
            'history_karung' => DB::table('stok_ayam')->where('jenis', 'karung')->where('id_gudang', '1')->get()
        ];
        return view('dashboard_kandang.index', $data);
    }

    public function kandang_selesai($id_kandang)
    {
        DB::table('kandang')->where('id_kandang', $id_kandang)->update(['selesai' => 'Y']);
        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Kandang Berhasil Di selesaikan');
    }

    public function tambah_telur(Request $r)
    {
        DB::table('stok_telur')->where([['id_kandang', $r->id_kandang], ['tgl', $r->tgl]])->delete();
        DB::table('stok_telur_new')->where([['id_kandang', $r->id_kandang], ['tgl', $r->tgl]])->delete();

        for ($i = 0; $i < count($r->id_telur); $i++) {
            $ikat = $r->ikat[$i];
            $ikat_kg = $r->ikat_kg[$i];

            $rak = $r->rak[$i];
            $rak_kg = $r->rak_kg[$i];

            $pcs = $r->pcs[$i];
            $ttl_kg_pcs = $r->ttl_kg_pcs[$i];

            $ttlPcs = ($ikat * 180) + ($rak * 30) + $pcs;
            $ttlKg = $ikat_kg + $rak_kg + $ttl_kg_pcs;

            $data = [
                'id_kandang' => $r->id_kandang,
                'id_telur' => $r->id_telur[$i],
                'tgl' => $r->tgl,
                'admin' => auth()->user()->name,
                'ikat' => $ikat,
                'ikat_kg' => $ikat_kg,
                'rak' => $rak,
                'rak_kg' => $rak_kg,
                'pcs' => $pcs,
                'pcs_kg' => $r->pcs_kg[$i],
                'potongan_pcs' => $r->potongan_pcs[$i],
                'ttl_kg_pcs' => $ttl_kg_pcs,
            ];
            DB::table('stok_telur_new')->insert($data);

            $dataStok = [
                'id_kandang' => $r->id_kandang,
                'id_telur' => $r->id_telur[$i],
                'tgl' => $r->tgl,
                'pcs' => $ttlPcs,
                'kg' => $ttlKg,
                'pcs_kredit' => 0,
                'kg_kredit' => 0,
                'admin' => auth()->user()->name,
                'id_gudang' => 1,
                'nota_transfer' => '',
                'ket' => '',
            ];
            DB::table('stok_telur')->insert($dataStok);
        }

        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function load_telur($id_kandang)
    {
        $data = [
            'telur' => DB::table('telur_produk')->get(),
            'kandang' => DB::table('kandang')->where('id_kandang', $id_kandang)->first()
        ];
        return view('dashboard_kandang.modal.load_telur', $data);
    }

    public function tambah_populasi(Request $r)
    {
        $jual = 0;
        for ($x = 0; $x < count($r->id_kandang); $x++) {
            DB::table('populasi')->where([['id_kandang', $r->id_kandang[$x]], ['tgl', $r->tgl[$x]]])->delete();
            DB::table('populasi')->insert([
                'id_kandang' => $r->id_kandang[$x],
                'mati' => $r->mati[$x],
                'jual' => $r->jual[$x],
                'tgl' => $r->tgl[$x],
                'admin' => auth()->user()->name
            ]);
            $pesan = $r->mati[$x] > 3 ? 'error' : 'sukses';
            $jual += $r->jual[$x];
            $tgl = $r->tgl[$x];
        }
        DB::table('stok_ayam')->where([['id_gudang', 1], ['tgl', $tgl], ['transfer',  'T']])->delete();
        DB::table('stok_ayam')->insert([
            'tgl' => $tgl,
            'debit' => $jual,
            'kredit' => 0,
            'id_gudang' => 1,
            'admin' => auth()->user()->name,
            'jenis' => 'ayam'
        ]);

        return redirect()->route('dashboard_kandang.index')->with($pesan, 'Data Berhasil Ditambahkan');
    }

    public function load_populasi($id_kandang)
    {
        $data = [
            'kandang' => DB::table('kandang')->get()
            // 'kandang' => DB::table('kandang')->where('id_kandang', $id_kandang)->first()
        ];
        return view('dashboard_kandang.modal.load_populasi', $data);
    }

    public function transfer_stok()
    {
        $tgl1 =  $this->tgl1;
        $tgl2 =  $this->tgl2;
        $transfer = DB::select("SELECT 
        b.nm_telur, sum(a.pcs_pcs + (a.ikat * 180) + a.pcs_kg) as pcs, a.* ,
        sum(a.pcs_kg + a.kg_ikat + (((a.pcs_kg / 15) * 0.12) + a.kg_kg)) as kg
        FROM `invoice_mtd` as a
        LEFT JOIN telur_produk as b ON a.id_produk = b.id_produk_telur
        WHERE a.jenis = 'tf'
        GROUP BY a.no_nota
        ORDER BY a.id_invoice_mtd DESC;");
        $data = [
            'title' => 'Transfer Stok',
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
            'transfer' => $transfer
        ];
        return view('dashboard_kandang.history.transfer_stok', $data);
    }

    public function add_transfer_stok(Request $r)
    {
        $cek = DB::table('invoice_mtd')->where('jenis', 'tf')->orderBy('id_invoice_mtd', 'DESC')->first();
        $nota_t = empty($cek) ? 1000 + 1 : str()->remove('TF-', $cek->no_nota) + 1;

        $data = [
            'title' => 'Buat Invoice',
            'produk' => DB::table('telur_produk')->get(),
            'customer' => DB::table('customer')->get(),
            'nota' => $nota_t,
            'akun' => DB::table('akun')->whereIn('id_klasifikasi', ['1', '7'])->get()
        ];
        return view('stok_telur.transfer', $data);
    }

    function tbh_baris_transfer_mtd(Request $r)
    {
        $data = [
            'count' => $r->count,
            'produk' => DB::table('telur_produk')->get(),
        ];
        return view('stok_telur.tbh_baris_transfet_mtd', $data);
    }

    public function save_transfer(Request $r)
    {
        $cek = DB::table('invoice_mtd')->where('jenis', 'tf')->orderBy('id_invoice_mtd', 'DESC')->first();
        $nota_t = empty($cek) ? 1000 + 1 : str()->remove('TF-', $cek->no_nota) + 1;

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
        for ($x = 0; $x < count($r->id_produk); $x++) {

            $pcs_ikat = $ikat[$x] * 180;
            $total_pcs = $pcs_ikat + $pcs_pcs[$x] + $pcs_kg[$x];
            $total_kg_kotor = $kg_pcs[$x] + $kg_ikat[$x] + $kg_kg[$x];
            $data = [
                'tgl' => $r->tgl,
                'id_telur' => $r->id_produk[$x],
                'pcs_kredit' => $total_pcs,
                'kg_kredit' => $total_kg_kotor,
                'admin' => auth()->user()->name,
                'nota_transfer' => 'TF-' . $nota_t,
                'id_gudang' => 1,
                'jenis' => 'tf'
            ];
            DB::table('stok_telur')->insert($data);
            $data = [
                'tgl' => $r->tgl,
                'id_telur' => $r->id_produk[$x],
                'pcs' => $total_pcs,
                'kg' => $total_kg_kotor,
                'admin' => auth()->user()->name,
                'nota_transfer' => 'TF-' . $nota_t,
                'id_gudang' => 2,
                'jenis' => 'tf'
            ];
            DB::table('stok_telur')->insert($data);

            $data = [
                'tgl' => $r->tgl,
                'no_nota' => 'TF-' . $nota_t,
                'pcs_pcs' => $pcs_pcs[$x],
                'kg_pcs' => $kg_pcs[$x],
                'ikat' => $ikat[$x],
                'kg_ikat' => $kg_ikat[$x],
                'pcs_kg' => $pcs_kg[$x],
                'kg_kg' => $kg_kg[$x],
                'rak_kg' => $rak_kg[$x],
                'id_produk' => $r->id_produk[$x],
                'jenis' => 'tf',
                'admin' => auth()->user()->name
            ];
            DB::table('invoice_mtd')->insert($data);
        }
        return redirect()->route('dashboard_kandang.cek_transfer', ['nota' => "TF-$nota_t"])->with('sukses', 'Data berhasil di transfer');
    }

    public function edit_transfer_stok(Request $r)
    {
        $data = [
            'title' => 'Edit Transfer',
            'nota' => $r->nota,
            'tgl' => DB::table('invoice_mtd')->where([['no_nota', $r->nota], ['jenis', 'tf']])->first()->tgl,
            'produk' => DB::table('telur_produk')->get(),
            'datas' => DB::table('invoice_mtd')->where([['no_nota', $r->nota], ['jenis', 'tf']])->get()
        ];
        return view('stok_telur.edit_transfer', $data);
    }

    public function update_transfer(Request $r)
    {

        $nota_t = $r->no_nota;

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
        $voucher = DB::table('tb_void')->where([['no_nota', $nota_t], ['voucher', $r->voucher], ['status', 'T']])->count();
        $voucherUpdate = $voucher > 0 || $r->tgl == date('Y-m-d') ? true : false;

        if ($voucherUpdate) {
            DB::table('stok_telur')->where('nota_transfer', $nota_t)->delete();
            DB::table('invoice_mtd')->where('no_nota', $nota_t)->delete();
            for ($x = 0; $x < count($r->id_produk); $x++) {

                $pcs_ikat = $ikat[$x] * 180;
                $total_pcs = $pcs_ikat + $pcs_pcs[$x] + $pcs_kg[$x];
                $total_kg_kotor = $kg_pcs[$x] + $kg_ikat[$x] + $kg_kg[$x];
                $data = [
                    'tgl' => $r->tgl,
                    'id_telur' => $r->id_produk[$x],
                    'pcs_kredit' => $total_pcs,
                    'kg_kredit' => $total_kg_kotor,
                    'admin' => auth()->user()->name,
                    'nota_transfer' => $nota_t,
                    'id_gudang' => 1,
                    'jenis' => 'tf'
                ];
                DB::table('stok_telur')->insert($data);
                $data = [
                    'tgl' => $r->tgl,
                    'id_telur' => $r->id_produk[$x],
                    'pcs' => $total_pcs,
                    'kg' => $total_kg_kotor,
                    'admin' => auth()->user()->name,
                    'nota_transfer' => $nota_t,
                    'id_gudang' => 2,
                    'jenis' => 'tf'
                ];
                DB::table('stok_telur')->insert($data);

                $data = [
                    'tgl' => $r->tgl,
                    'no_nota' => $nota_t,
                    'pcs_pcs' => $pcs_pcs[$x],
                    'kg_pcs' => $kg_pcs[$x],
                    'ikat' => $ikat[$x],
                    'kg_ikat' => $kg_ikat[$x],
                    'pcs_kg' => $pcs_kg[$x],
                    'kg_kg' => $kg_kg[$x],
                    'rak_kg' => $rak_kg[$x],
                    'id_produk' => $r->id_produk[$x],
                    'jenis' => 'tf',
                    'admin' => auth()->user()->name
                ];
                DB::table('invoice_mtd')->insert($data);
            }
            DB::table('tb_void')->where([['no_nota', $nota_t], ['voucher', $r->voucher]])->update(['status' => 'Y']);
        } else {
            return redirect()->route('dashboard_kandang.edit_transfer_stok', ['nota' => $nota_t])->with('error', 'Voucher Update Salah!');
        }
        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data berhasil di transfer');
    }

    public function cek_transfer(Request $r)
    {
        $data = [
            'title' => 'Nota Transfer',
            'nota' => $r->nota,
            'tgl' => DB::table('invoice_mtd')->where([['no_nota', $r->nota], ['jenis', 'tf']])->first()->tgl,
            'produk' => DB::table('telur_produk')->get(),
            'datas' => DB::table('invoice_mtd')->where([['no_nota', $r->nota], ['jenis', 'tf']])->get()
        ];
        return view('stok_telur.cek_transfer', $data);
    }

    public function detail_transfer($no_nota)
    {
        $data = [
            'title' => 'Nota Transfer',
            'nota' => $no_nota,
            'tgl' => DB::table('invoice_mtd')->where([['no_nota', $no_nota], ['jenis', 'tf']])->first()->tgl,
            'produk' => DB::table('telur_produk')->get(),
            'datas' => DB::table('invoice_mtd')->where([['no_nota', $no_nota], ['jenis', 'tf']])->get()
        ];
        return view('stok_telur.detail_transfer', $data);
    }

    public function void_transfer(Request $r)
    {
        void($r->no_nota, 'transfer martadah');

        return redirect()->route('dashboard_kandang.transfer_stok', ['id_gudan' => 1])->with('sukses', 'Berhasil void');
    }

    public function delete_transfer(Request $r)
    {
        DB::table('invoice_mtd')->where('no_nota', $r->no_nota)->delete();
        DB::table('stok_telur')->where('nota_transfer', $r->no_nota)->delete();

        return redirect()->route('dashboard_kandang.transfer_stok', ['id_gudang' => 1])->with('sukses', 'Berhasil hapus data');
    }

    public function penjualan_umum()
    {

        $tgl1 = $this->tgl1;
        $tgl2 = $this->tgl2;
        $id_user = auth()->user()->id;
        $penjualan = DB::select("SELECT *, sum(a.total_rp) as total, count(*) as ttl_produk  FROM `penjualan_agl` as a
        WHERE a.tgl BETWEEN '$tgl1' AND '$tgl2'
        GROUP BY a.urutan
        ORDER BY a.id_penjualan DESC");
        $data = [
            'title' => 'Penjualan Umum',
            'penjualan' => $penjualan,
            'tgl1' => $tgl1,
            'tgl2' => $tgl2,
        ];
        return view('dashboard_kandang.penjualan_umum.penjualan_umum', $data);
    }

    public function add_penjualan_umum()
    {
        $kd_produk = Produk::latest('kd_produk')->first();
        $nota = buatNota('penjualan_agl', 'urutan');
        $data = [
            'title' => 'Tambah Penjualan Umum',
            'customer' => DB::table('customer')->get(),
            'produk' => $this->produk,
            'no_nota' => $nota,
            'kd_produk' => empty($kd_produk) ? 1 : $kd_produk->kd_produk + 1,
            'satuan' => DB::table('tb_satuan')->get(),
            'gudang' => $this->gudang,
        ];
        return view('dashboard_kandang.penjualan_umum.add', $data);
    }

    public function tbh_add(Request $r)
    {
        $data = [
            'count' => $r->count,
            'produk' => $this->produk,
        ];
        return view('penjualan2.tbh_add', $data);
    }

    public function get_stok(Request $r)
    {
        $cek = DB::selectOne("SELECT f.debit,f.kredit FROM tb_produk as a
        LEFT join (
                  SELECT 
                    max(b.tgl) as tgl, 
                    b.id_produk, 
                    b.urutan, 
                    SUM(b.debit) as debit, 
                    sum(b.kredit) as kredit 
                  FROM 
                    tb_stok_produk as b 
                  where 
                    b.jenis = 'selesai'
                  group by 
                    b.id_produk
                ) as f on f.id_produk = a.id_produk 
        WHERE a.id_produk = '$r->id_telur'");
        echo json_encode($cek);
    }

    public function save_penjualan_umum(Request $r)
    {
        for ($i = 0; $i < count($r->id_produk); $i++) {
            DB::table('penjualan_agl')->insert([
                'urutan' => $r->no_nota,
                'nota_manual' => $r->nota_manual,
                'tgl' => $r->tgl,
                'kode' => 'PUM',
                'id_customer' => $r->id_customer,
                'driver' => '',
                'id_produk' => $r->id_produk[$i],
                'qty' => $r->qty[$i],
                'rp_satuan' => $r->rp_satuan[$i],
                'total_rp' => $r->total_rp[$i],
                'ket' => '',
                'id_jurnal' => 0,
                'admin' => auth()->user()->name,
                'lokasi' => 'mtd'
            ]);
        }

        return redirect()->route('dashboard_kandang.penjualan_umum')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function edit_penjualan(Request $r)
    {
        $penjualan = DB::selectOne("SELECT *, sum(a.total_rp) as total, count(*) as ttl_produk  FROM `penjualan_agl` as a
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
            'no_nota' => $penjualan->urutan
        ];
        return view('dashboard_kandang.penjualan_umum.edit', $data);
    }

    public function update_penjualan(Request $r)
    {
        $voucher = DB::table('tb_void')->where([['no_nota', "PUM-$r->no_nota"], ['voucher', $r->voucher], ['status', 'T']])->count();
        $voucherUpdate = $voucher > 0 || $r->tgl == date('Y-m-d') ? true : false;
        if ($voucherUpdate) {
            DB::table('tb_void')->where([['no_nota', "PUM-$r->no_nota"], ['voucher', $r->voucher]])->update(['status' => 'Y']);
            DB::table('tb_stok_produk')->where('no_nota', 'PUM-' . $r->no_nota)->delete();
            DB::table('penjualan_agl')->where('urutan', $r->no_nota)->delete();

            for ($i = 0; $i < count($r->id_produk); $i++) {
                DB::table('penjualan_agl')->insert([
                    'urutan' => $r->no_nota,
                    'nota_manual' => $r->nota_manual,
                    'tgl' => $r->tgl,
                    'id_customer' => $r->id_customer,
                    'driver' => '',
                    'kode' => 'PUM',
                    'id_produk' => $r->id_produk[$i],
                    'qty' => $r->qty[$i],
                    'rp_satuan' => $r->rp_satuan[$i],
                    'total_rp' => $r->total_rp[$i],
                    'ket' => '',
                    'id_jurnal' => 0,
                    'admin' => auth()->user()->name,
                    'lokasi' => 'mtd'
                ]);
            }
        } else {
            return redirect()->route('dashboard_kandang.edit_penjualan', ['urutan' => $r->no_nota])->with('error', 'Voucher Update Salah!');
        }

        return redirect()->route('dashboard_kandang.penjualan_umum')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function void_penjualan_umum(Request $r)
    {
        void($r->no_nota, 'Penjualan Umum', 'penjualan_agl');

        return redirect()->route('dashboard_kandang.penjualan_umum')->with('sukses', 'Data berhasil ditambahkan void');
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

    public function load_detail_nota($urutan)
    {
        $urutan = explode(", ", $urutan);
        $id_produk = $urutan[count($urutan) - 1];

        $produk = DB::table('penjualan_agl as a')
            ->select('a.admin as admin', 'a.*', 'b.nm_produk')
            ->join('tb_produk as b', 'a.id_produk', 'b.id_produk')
            ->where('a.id_produk', $id_produk)
            ->whereIn('a.urutan', $urutan)
            ->get();
        $data = [
            'title' => 'Detail Penjaulan Umum',
            'produk' => $produk
        ];
        return view('dashboard_kandang.penjualan_umum.detail', $data);
    }

    public function load_perencanaan($id_kandang)
    {

        $pop = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                            LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                            WHERE a.id_kandang = '$id_kandang';");
        $data = [
            'title' => 'Perencanaan',
            'id_kandang' => $id_kandang,
            'kandang' => DB::table('kandang')->where('id_kandang', $id_kandang)->first(),
            'obatAyam' => DB::table('tb_produk_perencanaan')->where('kategori', 'obat_ayam')->get(),
            'pop' => $pop
        ];
        return view('dashboard_kandang.perencanaan.index', $data);
    }

    public function load_pakan_perencanaan()
    {

        $data = [
            'title' => 'Perencanaan',
            'pakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'pakan')->get(),

        ];
        return view('dashboard_kandang.perencanaan.load_pakan_perencanaan', $data);
    }
    public function tbh_pakan(Request $r)
    {
        $data = [
            'pakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'pakan')->get(),
            'count' => $r->count
        ];
        return view('dashboard_kandang.perencanaan.tbh_pakan', $data);
    }
    public function save_tambah_pakan(Request $r)
    {
        $id = DB::table('tb_produk_perencanaan')->insertGetId([
            'nm_produk' => $r->nm_produk,
            'kategori' => 'pakan',
            'tgl' => date('Y-m-d'),
            'admin' => auth()->user()->name
        ]);

        DB::table('stok_produk_perencanaan')->insert([
            'id_kandang' => 0,
            'id_pakan' => $id,
            'tgl' => date('Y-m-d'),
            'pcs' => $r->stok_awal,
            'pcs_kredit' => 0,
            'admin' => auth()->user()->name,
            'cek_admin' => '',
            'total_rp' => $r->total_rp
        ]);
    }

    public function load_obat_pakan()
    {

        $data = [
            'title' => 'Perencanaan',
            'pakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'obat_pakan')->get(),
        ];
        return view('dashboard_kandang.perencanaan.load_obat_pakan', $data);
    }

    public function get_stok_pakan(Request $r)
    {
        $stok = DB::selectOne("SELECT sum(pcs - pcs_kredit) as stok FROM stok_produk_perencanaan WHERE id_pakan = '$r->id_pakan'");
        echo $stok->stok;
    }
    public function tbh_obatPakan(Request $r)
    {
        $data = [
            'pakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'obat_pakan')->get(),
            'count' => $r->count
        ];
        return view('dashboard_kandang.perencanaan.tbh_obatPakan', $data);
    }
    public function save_tambah_obat_pakan(Request $r)
    {
        $id = DB::table('tb_produk_perencanaan')->insertGetId([
            'nm_produk' => $r->nm_produk,
            'kategori' => 'obat_pakan',
            'tgl' => date('Y-m-d'),
            'dosis_satuan' => $r->dosis_satuan ?? '',
            'campuran_satuan' => $r->campuran_satuan ?? '',
            'admin' => auth()->user()->name
        ]);

        DB::table('stok_produk_perencanaan')->insert([
            'id_kandang' => 0,
            'id_pakan' => $id,
            'tgl' => date('Y-m-d'),
            'pcs' => $r->stok_awal,
            'pcs_kredit' => 0,
            'admin' => auth()->user()->name,
            'cek_admin' => '',
            'total_rp' => $r->total_rp
        ]);
    }
    public function get_stok_obat_pakan(Request $r)
    {
        $stok = DB::selectOne("SELECT b.nm_satuan AS dosis_satuan, c.nm_satuan AS campuran_satuan
                FROM tb_produk_perencanaan a
                LEFT JOIN tb_satuan b ON a.dosis_satuan = b.id_satuan
                LEFT JOIN tb_satuan c ON a.campuran_satuan = c.id_satuan
                WHERE a.id_produk = '$r->id_produk';");

        $data = [
            'dosis_satuan' => $stok->dosis_satuan,
            'campuran_satuan' => $stok->campuran_satuan,
        ];
        echo json_encode($data);
    }

    public function load_obat_air()
    {

        $data = [
            'title' => 'Perencanaan',
            'pakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'obat_air')->get(),
        ];
        return view('dashboard_kandang.perencanaan.load_obat_air', $data);
    }
    public function tbh_obatAir(Request $r)
    {
        $data = [
            'pakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'obat_air')->get(),
            'count' => $r->count
        ];
        return view('dashboard_kandang.perencanaan.tbh_obatAir', $data);
    }
    public function save_tambah_obat_air(Request $r)
    {
        $id = DB::table('tb_produk_perencanaan')->insertGetId([
            'nm_produk' => $r->nm_produk,
            'kategori' => 'obat_air',
            'tgl' => date('Y-m-d'),
            'dosis_satuan' => $r->dosis_satuan ?? '',
            'campuran_satuan' => $r->campuran_satuan ?? '',
            'admin' => auth()->user()->name
        ]);

        DB::table('stok_produk_perencanaan')->insert([
            'id_kandang' => 0,
            'id_pakan' => $id,
            'tgl' => date('Y-m-d'),
            'pcs' => $r->stok_awal,
            'pcs_kredit' => 0,
            'admin' => auth()->user()->name,
            'cek_admin' => '',
            'total_rp' => $r->total_rp
        ]);
    }
    public function get_stok_obat_air(Request $r)
    {
        $stok = DB::selectOne("SELECT b.nm_satuan AS dosis_satuan, c.nm_satuan AS campuran_satuan
                FROM tb_produk_perencanaan a
                LEFT JOIN tb_satuan b ON a.dosis_satuan = b.id_satuan
                LEFT JOIN tb_satuan c ON a.campuran_satuan = c.id_satuan
                WHERE a.id_produk = '$r->id_produk';");

        $data = [
            'dosis_satuan' => $stok->dosis_satuan,
            'campuran_satuan' => $stok->campuran_satuan,
        ];
        echo json_encode($data);
    }

    public function load_obat_ayam()
    {
        $data = [
            'title' => 'Perencanaan',
            'pakan' => DB::table('tb_produk_perencanaan')->where('kategori', 'obat_ayam')->get(),
        ];
        return view('dashboard_kandang.perencanaan.load_obat_ayam', $data);
    }
    public function save_tambah_obat_ayam(Request $r)
    {
        $id = DB::table('tb_produk_perencanaan')->insertGetId([
            'nm_produk' => $r->nm_produk,
            'kategori' => 'obat_ayam',
            'tgl' => date('Y-m-d'),
            'dosis_satuan' => $r->dosis_satuan ?? '',
            'campuran_satuan' => $r->campuran_satuan ?? '',
            'admin' => auth()->user()->name
        ]);

        DB::table('stok_produk_perencanaan')->insert([
            'id_kandang' => 0,
            'id_pakan' => $id,
            'tgl' => date('Y-m-d'),
            'pcs' => $r->stok_awal,
            'pcs_kredit' => 0,
            'admin' => auth()->user()->name,
            'cek_admin' => '',
            'total_rp' => $r->total_rp
        ]);
    }
    public function get_stok_obat_ayam(Request $r)
    {
        $stok = DB::selectOne("SELECT b.nm_satuan AS dosis_satuan, c.nm_satuan AS campuran_satuan
                FROM tb_produk_perencanaan a
                LEFT JOIN tb_satuan b ON a.dosis_satuan = b.id_satuan
                LEFT JOIN tb_satuan c ON a.campuran_satuan = c.id_satuan
                WHERE a.id_produk = '$r->id_produk';");

        echo $stok->dosis_satuan;
    }

    public function getHargaSatuan($id_pakan)
    {
        return DB::selectOne("SELECT sum(a.total_rp / a.pcs) as rata_rata
                FROM stok_produk_perencanaan as a 
                where a.id_pakan = '$id_pakan' and a.pcs != '0' and a.h_opname ='T'
                group by a.id_pakan;");
    }
    public function getNoNota()
    {
        $max = DB::table('notas')->latest('nomor_nota')->where('id_buku', '2')->first();
        $nota_t = empty($max) ? '1000' : $max->nomor_nota + 1;
        DB::table('notas')->insert(['nomor_nota' => $nota_t, 'id_buku' => '2']);
        return $nota_t;
    }

    public function rumus(Request $r)
    {
        if ($r->rumus == 'ttlKg') {
            echo "<b>Note =</b> <em >Jika Ttl Kg hari ini - Ttl Kg Kemarin = 2.5kg maka merah</em>";
        }
        if ($r->rumus == 'ttlPcs') {
            echo "<b>Note =</b> <em >Jika Ttl Pcs hari ini - Ttl Pcs Kemarin = kurang 60 pcs maka merah</em>";
        }

        if ($r->rumus == 'grEkor') {
            echo "<b>Gr / Ekor =</b> <em >Total pakan / Populasi</em><br><br>";
            echo "<b>Note =</b> <em >Gr perekor - 100 maka kolom merah</em>";
        }
        if ($r->rumus == 'mati') {
            echo "<b>Note =</b> <em >Mati lebih dari 3 maka kolom merah</em>";
        }
        if ($r->rumus == 'populasi') {
            echo "<b>Note =</b> <em >Mati lebih dari 3 maka kolom merah</em>";
        }
        if ($r->rumus == 'minggu') {
            echo "<b>Note =</b> <em >Minggu lebih dari 85 maka kolom merah</em>";
        }
    }

    public function tambah_perencanaan(Request $r)
    {
        $tgl = $r->tgl;
        $id_kandang = $r->id_kandang;
        $kg_pakan_box = $r->kg_pakan_box;
        $populasi = $r->populasi;
        $gr_pakan_ekor = $r->gr_pakan_ekor;
        $kg_karung = $r->kg_karung;
        $kg_karung_sisa = $r->kg_karung_sisa;
        $no_nota = strtoupper(str()->random(5));
        if (!empty($r->id_pakan)) {
            $total_kg_pakan = 0;
            for ($i = 0; $i < count($r->id_pakan); $i++) {
                if ($r->stok[$i] < $r->gr_pakan[$i]) {
                    $error = 'error';
                    $pesan = 'STOK KURANG!! PERENCANAAN GAGAL DITAMBAH';
                } else {


                    $dataPakan = [
                        'id_kandang' => $id_kandang,
                        'id_produk_pakan' => $r->id_pakan[$i],
                        'tgl' => $tgl,
                        'no_nota' => $no_nota,
                        'gr' => $r->gr_pakan[$i],
                        'persen' => $r->persen_pakan[$i],
                        'admin' => auth()->user()->name
                    ];
                    DB::table('tb_pakan_perencanaan')->insert($dataPakan);

                    $dataStok = [
                        'id_kandang' => $id_kandang,
                        'id_pakan' => $r->id_pakan[$i],
                        'tgl' => $tgl,
                        'pcs' => 0,
                        'total_rp' => 0,
                        'no_nota' => $no_nota,
                        'pcs_kredit' =>  $r->gr_pakan[$i],
                        'admin' => auth()->user()->name
                    ];
                    DB::table('stok_produk_perencanaan')->insert($dataStok);
                }
                $total_kg_pakan += $r->gr_pakan[$i];
            }
            $data = [
                'tgl' => $tgl,
                'debit' => ($total_kg_pakan / 1000) * 0.3,
                'kredit' => 0,
                'id_gudang' => '1',
                'admin' =>  auth()->user()->name,
                'jenis' => 'pupuk'
            ];
            DB::table('stok_ayam')->insert($data);

            if (!empty($kg_pakan_box)) {
                $dataKarung = [
                    'tgl' => $tgl,
                    'id_kandang' => $id_kandang,
                    'karung' => $kg_pakan_box,
                    'gr' => $kg_karung,
                    'gr2' => $kg_karung_sisa,
                    'no_nota' => $no_nota,
                ];
                DB::table('tb_karung_perencanaan')->insert($dataKarung);

                $data = [
                    'tgl' => $tgl,
                    'debit' => 0,
                    'kredit' => $kg_pakan_box,
                    'id_gudang' => '1',
                    'admin' =>  auth()->user()->name,
                    'jenis' => 'karung'
                ];
                DB::table('stok_ayam')->insert($data);
            }

            if (!empty($r->id_obat_pakan[0])) {
                for ($i = 0; $i < count($r->id_obat_pakan); $i++) {
                    $data1 = [
                        'kategori' => 'obat_pakan',
                        'id_produk' => $r->id_obat_pakan[$i],
                        'dosis' => $r->dosis_obat_pakan[$i],
                        'campuran' => $r->campuran_obat_pakan[$i],
                        'tgl' => $tgl,
                        'no_nota' => $no_nota,
                        'id_kandang' => $id_kandang,
                        'admin' => auth()->user()->name,
                    ];

                    DB::table('tb_obat_perencanaan')->insert($data1);
                    $id_obat_pakan = $r->id_obat_pakan[$i];
                    $dataStok = [
                        'id_kandang' => $id_kandang,
                        'id_pakan' => $id_obat_pakan,
                        'tgl' => $tgl,
                        'pcs' => 0,
                        'total_rp' => 0,
                        'no_nota' => $no_nota,
                        'id_kandang' => $id_kandang,
                        'pcs_kredit' => $r->dosis_obat_pakan[$i],
                        'admin' => auth()->user()->name
                    ];
                    DB::table('stok_produk_perencanaan')->insert($dataStok);
                }
            }

            if (!empty($r->id_obat_air[0])) {
                for ($i = 0; $i < count($r->id_obat_air); $i++) {
                    $data1 = [
                        'kategori' => 'obat_air',
                        'id_produk' => $r->id_obat_air[$i],
                        'dosis' => $r->dosis_obat_air[$i],
                        'campuran' => $r->campuran_obat_air[$i],
                        'tgl' => $tgl,
                        'no_nota' => $no_nota,
                        'waktu' => $r->waktu_obat_air[$i],
                        'ket' => $r->ket_obat_air[$i],
                        'cara_pemakaian' => $r->cara_pemakaian_obat_air[$i],
                        'id_kandang' => $id_kandang,
                        'admin' => auth()->user()->name,
                    ];
                    DB::table('tb_obat_perencanaan')->insert($data1);

                    $id_obat_air = $r->id_obat_air[$i];
                    $dataStok = [
                        'id_kandang' => $id_kandang,
                        'id_pakan' => $id_obat_air,
                        'tgl' => $tgl,
                        'pcs' => 0,
                        'total_rp' => 0,
                        'no_nota' => $no_nota,
                        'pcs_kredit' =>  $r->dosis_obat_air[$i],
                        'admin' => auth()->user()->name
                    ];
                    DB::table('stok_produk_perencanaan')->insert($dataStok);
                }
            }

            if (!empty($r->id_obat_ayam[0])) {
                $data1 = [
                    'id_kandang' => $id_kandang,
                    'kategori' => 'obat_ayam',
                    'id_produk' => $r->id_obat_ayam,
                    'dosis' => $r->dosis_obat_ayam,
                    'campuran' => 0,
                    'tgl' => $tgl,
                    'no_nota' => $no_nota,
                    'admin' => auth()->user()->name,
                ];
                DB::table('tb_obat_perencanaan')->insert($data1);

                $id_obat_ayam = $r->id_obat_ayam;
                $dataStok = [
                    'id_kandang' => $id_kandang,
                    'id_pakan' => $id_obat_ayam,
                    'tgl' => $tgl,
                    'pcs' => 0,
                    'total_rp' => 0,
                    'no_nota' => $no_nota,
                    'pcs_kredit' =>  $r->dosis_obat_ayam,
                    'admin' => auth()->user()->name
                ];
                DB::table('stok_produk_perencanaan')->insert($dataStok);
            }

            return redirect()->route('dashboard_kandang.index')->with($error ?? 'sukses', $pesan ?? 'Data Perencanaan Berhasil ditambahkan');
        }
    }

    public function load_detail_perencanaan($id_kandang)
    {
        $data = [
            'title' => 'Detail Perencanaan',
            'kandang' => DB::table('kandang')->where('id_kandang', $id_kandang)->first()
        ];
        return view('dashboard_kandang.modal.detail_perencanaan', $data);
    }

    public function getQueryObatPerencanaan($tgl, $id_kandang, $kategori)
    {
        return DB::table('tb_obat_perencanaan as a')
            ->select(
                'a.tgl',
                'b.id_produk',
                'b.nm_produk',
                'a.waktu',
                'a.cara_pemakaian as cara',
                'a.id_kandang',
                'a.ket',
                'a.dosis',
                'a.campuran',
                'c.nm_satuan as satuan',
                'd.nm_satuan as satuan2'
            )
            ->leftJoin('tb_produk_perencanaan as b', 'a.id_produk', 'b.id_produk')
            ->leftJoin('tb_satuan as c', 'b.dosis_satuan', 'c.id_satuan')
            ->leftJoin('tb_satuan as d', 'b.campuran_satuan', 'd.id_satuan')
            ->where([['a.tgl', $tgl], ['a.id_kandang', $id_kandang], ['a.kategori', $kategori]])
            ->get();
    }

    public function viewHistoryPerencanaan(Request $r)
    {
        $id_kandang = $r->id_kandang;
        $tgl = $r->tgl;

        $tgl1 = date('Y-m-d', strtotime('-1 days', strtotime($tgl)));

        $pop = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                            LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                            WHERE a.id_kandang = '$id_kandang'");
        $populasi = $pop->stok_awal - $pop->pop;

        $kandang = DB::table('kandang')->where('id_kandang', $id_kandang)->first();
        $pakan = DB::selectOne("SELECT *,sum(gr) as total FROM tb_pakan_perencanaan as a 
                    WHERE a.tgl = '$tgl' AND a.id_kandang = '$id_kandang' 
                    GROUP BY a.id_kandang");

        $umur = DB::selectOne("SELECT TIMESTAMPDIFF(WEEK, a.chick_in, '$tgl') as mgg FROM kandang as a 
        WHERE a.id_kandang = '$id_kandang'");

        $pakan1 = DB::table('tb_karung_perencanaan')->where([['id_kandang', $id_kandang], ['tgl', $tgl]])->first();

        $pakan2 = DB::select("SELECT  a.tgl, b.nm_produk as nm_pakan, a.persen, a.gr as gr_pakan
        FROM tb_pakan_perencanaan as a 
        left join tb_produk_perencanaan as b on a.id_produk_pakan = b.id_produk 
        where a.id_kandang = '$id_kandang' AND  a.tgl = '$tgl'");

        $obat_pakan = $this->getQueryObatPerencanaan($tgl, $id_kandang, 'obat_pakan');

        $obat_air = $this->getQueryObatPerencanaan($tgl, $id_kandang, 'obat_air');
        $obat_ayam = $this->getQueryObatPerencanaan($tgl, $id_kandang, 'obat_ayam');
        $data = [
            'tgl_per' => $tgl,
            'id_kandang' => $id_kandang,
            'kandang' => $kandang,
            'pakan' => $pakan,
            'populasi' => $populasi,
            'umur' => $umur,
            'pakan1' => $pakan1,
            'pakan2' => $pakan2,
            'obat_pakan' => $obat_pakan,
            'obat_air' => $obat_air,
            'obat_ayam' => $obat_ayam,
        ];
        return view("dashboard_kandang.history.hasilPerencanaan", $data);
    }

    public function viewHistoryEditPerencanaan(Request $r)
    {
        $tgl = $r->tgl;
        $tgl1 = date('Y-m-d', strtotime('-1 days', strtotime($tgl)));
        $id_kandang = $r->id_kandang;
        $pakan_id = DB::select("SELECT a.no_nota, a.id_pakan_perencanaan,a.tgl,b.id_produk, b.nm_produk as nm_pakan, a.persen, a.gr as gr_pakan
                    FROM tb_pakan_perencanaan as a 
                    left join tb_produk_perencanaan as b on a.id_produk_pakan = b.id_produk 
                    where a.id_kandang = '$id_kandang' AND  a.tgl = '$tgl'");
        $pakan = DB::table('tb_produk_perencanaan')->where('kategori', 'pakan')->get();
        $obat = DB::table('tb_produk_perencanaan')->where('kategori', 'obat_pakan')->get();
        $obat_air2 = DB::table('tb_produk_perencanaan')->where('kategori', 'obat_air')->get();
        $obat_ayam = DB::table('tb_produk_perencanaan')->where('kategori', 'obat_ayam')->get();
        $karung = DB::table('tb_karung_perencanaan')->where([['id_kandang', $id_kandang], ['tgl', $tgl]])->first();
        $kandang = DB::table('kandang')->get();
        $pop = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                            LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                            WHERE a.id_kandang = '$id_kandang'");
        $populasi = $pop->stok_awal - $pop->pop;
        $gr_pakan = DB::selectOne("SELECT sum(a.gr) as ttl, a.no_nota FROM tb_pakan_perencanaan as a where a.id_kandang = '$id_kandang' and a.tgl = '$tgl' group by a.id_kandang");
        $obat_pakan = $this->getQueryObatPerencanaan($tgl, $id_kandang, 'obat_pakan');
        $obat_air = $this->getQueryObatPerencanaan($tgl, $id_kandang, 'obat_air');
        $obat_aym = $this->getQueryObatPerencanaan($tgl, $id_kandang, 'obat_ayam');

        $data = [
            'tgl' => $tgl,
            'id_kandang' => $id_kandang,
            'pakan_id' => $pakan_id,
            'pakan' => $pakan,
            'karung' => $karung,
            'kandang' => $kandang,
            'populasi' => $populasi,
            'gr_pakan' => $gr_pakan,
            'obat_pakan' => $obat_pakan,
            'obat_air' => $obat_air,
            'obat' => $obat,
            'obat_air2' => $obat_air2,
            'obat_ayam' => $obat_ayam,
            'obat_aym' => $obat_aym,
        ];
        return view('dashboard_kandang.history.editPerencanaan', $data);
    }

    public function edit_perencanaan(Request $r)
    {
        $no_nota = $r->no_nota;
        $id_kandang = $r->id_kandang;
        $tgl = $r->tgl;
        $kg_pakan_box = $r->kg_pakan_box;
        $populasi = $r->populasi;
        $gr_pakan_ekor = $r->gr_pakan_ekor;
        $kg_karung = $r->kg_karung;
        $kg_karung_sisa = $r->kg_karung_sisa;

        DB::table('stok_produk_perencanaan')->where('no_nota', $no_nota)->delete();
        DB::table('tb_karung_perencanaan')->where('no_nota', $no_nota)->delete();
        DB::table('tb_obat_perencanaan')->where('no_nota', $no_nota)->delete();
        DB::table('tb_pakan_perencanaan')->where('no_nota', $no_nota)->delete();

        $no_nota = strtoupper(str()->random(5));

        if (!empty($r->id_pakan)) {
            for ($i = 0; $i < count($r->id_pakan); $i++) {
                $dataPakan = [
                    'id_kandang' => $id_kandang,
                    'id_produk_pakan' => $r->id_pakan[$i],
                    'tgl' => $tgl,
                    'no_nota' => $no_nota,
                    'gr' => $r->gr_pakan[$i],
                    'persen' => $r->persen_pakan[$i],
                    'admin' => auth()->user()->name
                ];
                DB::table('tb_pakan_perencanaan')->insert($dataPakan);
                $id_pakan = $r->id_pakan[$i];
                $stok = DB::selectOne("SELECT sum(pcs) as stok FROM stok_produk_perencanaan WHERE id_pakan = '$id_pakan'");
                $dataStok = [
                    'id_kandang' => $id_kandang,
                    'id_pakan' => $r->id_pakan[$i],
                    'tgl' => $tgl,
                    'pcs' => 0,
                    'total_rp' => 0,
                    'no_nota' => $no_nota,
                    'pcs_kredit' => ($r->gr_pakan[$i] / 1000) - $stok->stok,
                    'admin' => auth()->user()->name
                ];
                DB::table('stok_produk_perencanaan')->insert($dataStok);
            }
        }

        if (!empty($kg_pakan_box)) {
            $dataKarung = [
                'tgl' => $tgl,
                'id_kandang' => $id_kandang,
                'karung' => $kg_pakan_box,
                'gr' => $kg_karung,
                'gr2' => $kg_karung_sisa,
                'no_nota' => $no_nota,
            ];
            DB::table('tb_karung_perencanaan')->insert($dataKarung);
        }

        if (!empty($r->id_obat_pakan)) {
            for ($i = 0; $i < count($r->id_obat_pakan); $i++) {
                $data1 = [
                    'kategori' => 'obat_pakan',
                    'id_produk' => $r->id_obat_pakan[$i],
                    'dosis' => $r->dosis_obat_pakan[$i],
                    'campuran' => $r->campuran_obat_pakan[$i],
                    'tgl' => $tgl,
                    'no_nota' => $no_nota,
                    'id_kandang' => $id_kandang,
                    'admin' => auth()->user()->name,
                ];

                DB::table('tb_obat_perencanaan')->insert($data1);
                $id_obat_pakan = $r->id_obat_pakan[$i];
                $stok = DB::selectOne("SELECT sum(pcs) as stok FROM stok_produk_perencanaan WHERE id_pakan = '$id_obat_pakan'");
                $dataStok = [
                    'id_kandang' => $id_kandang,
                    'id_pakan' => $id_obat_pakan,
                    'tgl' => $tgl,
                    'pcs' => 0,
                    'total_rp' => 0,
                    'no_nota' => $no_nota,
                    'id_kandang' => $id_kandang,
                    'pcs_kredit' => ($r->dosis_obat_pakan[$i]  / 1000) - $stok->stok,
                    'admin' => auth()->user()->name
                ];
                DB::table('stok_produk_perencanaan')->insert($dataStok);
            }
        }

        if (!empty($r->id_obat_air)) {
            for ($i = 0; $i < count($r->id_obat_air); $i++) {
                $data1 = [
                    'kategori' => 'obat_air',
                    'id_produk' => $r->id_obat_air[$i],
                    'dosis' => $r->dosis_obat_air[$i],
                    'campuran' => $r->campuran_obat_air[$i],
                    'tgl' => $tgl,
                    'no_nota' => $no_nota,
                    'waktu' => $r->waktu_obat_air[$i],
                    'ket' => $r->ket_obat_air[$i],
                    'cara_pemakaian' => $r->cara_pemakaian_obat_air[$i],
                    'id_kandang' => $id_kandang,
                    'admin' => auth()->user()->name,
                ];

                DB::table('tb_obat_perencanaan')->insert($data1);

                $id_obat_air = $r->id_obat_air[$i];
                $stok = DB::selectOne("SELECT sum(pcs) as stok FROM stok_produk_perencanaan WHERE id_pakan = '$id_obat_air'");
                $dataStok = [
                    'id_kandang' => $id_kandang,
                    'id_pakan' => $id_obat_air,
                    'tgl' => $tgl,
                    'pcs' => 0,
                    'total_rp' => 0,
                    'no_nota' => $no_nota,
                    'pcs_kredit' => ($r->dosis_obat_pakan[$i] / 1000) - $stok->stok,
                    'admin' => auth()->user()->name
                ];
                DB::table('stok_produk_perencanaan')->insert($dataStok);
            }
        }

        if (!empty($r->id_obat_ayam)) {
            $data1 = [
                'kategori' => 'obat_air',
                'id_produk' => $r->id_obat_ayam,
                'dosis' => $r->dosis_obat,
                'campuran' => $r->campuran_obat,
                'tgl' => $tgl,
                'no_nota' => $no_nota,
                'admin' => auth()->user()->name,
            ];

            DB::table('tb_obat_perencanaan')->insert($data1);

            $id_obat_ayam = $r->id_obat_ayam;
            $stok = DB::selectOne("SELECT sum(pcs) as stok FROM stok_produk_perencanaan WHERE id_pakan = '$id_obat_ayam'");
            $dataStok = [
                'id_kandang' => $id_kandang,
                'id_pakan' => $id_obat_ayam,
                'tgl' => $tgl,
                'pcs' => 0,
                'total_rp' => 0,
                'no_nota' => $no_nota,
                'pcs_kredit' => ($r->dosis_obat_ayam[$i] / 1000) - $stok->stok,
                'admin' => auth()->user()->name
            ];
            DB::table('stok_produk_perencanaan')->insert($dataStok);
        }

        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data Perencanaan Berhasil didedit');
    }

    public function hasilLayer(Request $r)
    {
        $data = [
            'title' => 'Layer',
            'kandang' => DB::table('kandang as a')
                ->select(DB::raw("FLOOR(DATEDIFF('$r->tgl', a.chick_in) / 7) AS mgg"), 'a.*')
                ->where('selesai', 'T')
                ->get(),
            'tgl' => $r->tgl
        ];
        return view('dashboard_kandang.history.layer', $data);
    }

    public function getProdukObat($id_kandang, $jenis)
    {
        return DB::select("SELECT a.waktu,a.cara_pemakaian as cara,a.tgl,b.nm_produk, a.dosis,a.campuran, e.nm_satuan as dosis_satuan, f.nm_satuan as campuran_satuan,(a.dosis) as dosis_obat,d.debit
        FROM tb_obat_perencanaan as a
        LEFT JOIN tb_produk_perencanaan as b  ON a.id_produk = b.id_produk
        LEFT JOIN tb_satuan as e ON b.dosis_satuan = e.id_satuan
        LEFT JOIN tb_satuan as f on b.campuran_satuan = f.id_satuan
        LEFT JOIN (
            SELECT a.id_produk,SUM(b.debit) as debit FROM `tb_produk_perencanaan` as a
            LEFT JOIN jurnal as b ON a.id_produk = SUBSTRING_INDEX(RIGHT(b.ket, LENGTH(b.ket) - INSTR(b.ket, '-')), '-', -1)
            WHERE a.kategori = '$jenis' AND b.debit != 0
            GROUP BY a.id_produk
        ) AS d ON d.id_produk = a.id_produk
        WHERE b.kategori = '$jenis' AND a.id_kandang = '$id_kandang' ORDER BY a.tgl DESC;");
    }

    public function export_telur(Request $r)
    {
        $pakan = DB::select("SELECT a.tgl, b.nm_produk,a.persen,a.gr,d.nm_satuan,c.nm_kandang FROM `tb_pakan_perencanaan` as a
        LEFT JOIN tb_produk_perencanaan as b ON a.id_produk_pakan = b.id_produk
        LEFT JOIN kandang as c ON a.id_kandang = c.id_kandang
        LEFT JOIN tb_satuan as d ON b.dosis_satuan = d.id_satuan GROUP BY a.id_produk_pakan");

        $obat_pakan = DB::select("SELECT *,a.dosis, a.campuran FROM `tb_obat_pakan` as a
        LEFT JOIN tb_barang_pv as b ON b.id_barang = a.id_obat
        LEFT JOIN tb_kandang as c ON c.id_kandang = a.id_kandang
        GROUP By a.id_obat_pakan");

        $obat_air = DB::select("SELECT *,a.dosis, a.campuran FROM `tb_obat_air` as a
        LEFT JOIN tb_barang_pv as b ON b.id_barang = a.id_obat
        LEFT JOIN tb_kandang as c ON c.id_kandang = a.id_kandang
        GROUP By a.id_obat_air");

        $obat_ayam = DB::select("SELECT *,a.dosis FROM `tb_obat_ayam` as a
        LEFT JOIN tb_barang_pv as b ON b.id_barang = a.id_obat
        LEFT JOIN tb_kandang as c ON c.id_kandang = a.id_kandang
        GROUP By a.id_obat_ayam");
    }

    public function getDataObat($kategori = '')
    {
        if (empty($kategori)) {
            $query = DB::select("SELECT a.tgl, b.nm_produk,a.persen,a.gr,d.nm_satuan,c.nm_kandang FROM `tb_pakan_perencanaan` as a
        LEFT JOIN tb_produk_perencanaan as b ON a.id_produk_pakan = b.id_produk
        LEFT JOIN kandang as c ON a.id_kandang = c.id_kandang
        LEFT JOIN tb_satuan as d ON b.dosis_satuan = d.id_satuan GROUP BY a.id_produk_pakan");
        } else {
            $query = DB::select("SELECT c.nm_kandang,a.tgl, b.nm_produk, d.nm_satuan as dosis_satuan,e.nm_satuan as campuran_satuan, a.dosis,b.campuran_satuan, a.campuran,a.waktu,a.ket, a.cara_pemakaian as cara FROM tb_obat_perencanaan as a
            LEFT JOIN tb_produk_perencanaan as b on a.id_produk = b.id_produk
            LEFT JOIN kandang as c ON a.id_kandang = c.id_kandang
            LEFT JOIN tb_satuan as d ON b.dosis_satuan = d.id_satuan 
            LEFT JOIN tb_satuan as e ON b.campuran_satuan = e.id_satuan 
            WHERE b.kategori =  '$kategori' GROUP BY a.id_obat_perencanaan;");
        }
        return $query;
    }

    public function export_perencanaan()
    {
        $pakan = $this->getDataObat();
        $obat_pakan = $this->getDataObat('obat_pakan');
        $obat_air = $this->getDataObat('obat_air');
        $obat_ayam = $this->getDataObat('obat_ayam');

        $spreadsheet = new Spreadsheet;

        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        $style = array(
            'font' => array(
                'size' => 9
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
        );

        // pakan
        $sheet->setCellValue('A1', 'Pakan')
            ->setCellValue('B1', 'No')
            ->setCellValue('C1', 'Tanggal')
            ->setCellValue('D1', 'Nama Pakan')
            ->setCellValue('E1', 'Persen Pakan')
            ->setCellValue('F1', 'Gr Pakan')
            ->setCellValue('G1', 'Satuan')
            ->setCellValue('H1', 'Kandang');

        $kolom = 2;
        foreach ($pakan as $i => $p) {
            $sheet->setCellValue("B$kolom", $i + 1)
                ->setCellValue("C$kolom", $p->tgl)
                ->setCellValue("D$kolom", $p->nm_produk)
                ->setCellValue("E$kolom", $p->persen)
                ->setCellValue("F$kolom", $p->gr)
                ->setCellValue("G$kolom", $p->nm_satuan)
                ->setCellValue("H$kolom", $p->nm_kandang);
            $kolom++;
        }
        $batas = $kolom - 1;
        $sheet->getStyle("B1:H$batas")->applyFromArray($style);

        // obat pakan
        $sheet->setCellValue('J1', 'Obat Pakan')
            ->setCellValue('K1', 'ID')
            ->setCellValue('L1', 'Tanggal')
            ->setCellValue('M1', 'Nama Obat')
            ->setCellValue('N1', 'Dosis')
            ->setCellValue('O1', 'Satuan')
            ->setCellValue('P1', 'Campuran')
            ->setCellValue('Q1', 'Satuan')
            ->setCellValue('R1', 'Kandang');

        $kolom = 2;
        foreach ($obat_pakan as $no => $d) {
            $sheet->setCellValue("K$kolom", $no + 1)
                ->setCellValue("L$kolom", $d->tgl)
                ->setCellValue("M$kolom", $d->nm_produk)
                ->setCellValue("N$kolom", $d->dosis)
                ->setCellValue("O$kolom", $d->dosis_satuan)
                ->setCellValue("P$kolom", $d->campuran)
                ->setCellValue("Q$kolom", $d->campuran_satuan)
                ->setCellValue("R$kolom", $d->nm_kandang);
            $kolom++;
        }
        $batas = $kolom - 1;
        $sheet->getStyle("K1:R$batas")->applyFromArray($style);


        // obat Air
        $sheet->setCellValue('T1', 'Obat Air')
            ->setCellValue('U1', 'ID')
            ->setCellValue('V1', 'Tanggal')
            ->setCellValue('W1', 'Nama Obat')
            ->setCellValue('X1', 'Dosis')
            ->setCellValue('Y1', 'Satuan')
            ->setCellValue('Z1', 'Campuran')
            ->setCellValue('AA1', 'Satuan')
            ->setCellValue('AB1', 'Waktu')
            ->setCellValue('AC1', 'Cara')
            ->setCellValue('AD1', 'Keterangan')
            ->setCellValue('AE1', 'Kandang');

        $kolom = 2;
        foreach ($obat_air as $no => $d) {
            $sheet->setCellValue("U$kolom", $no + 1)
                ->setCellValue("V$kolom", $d->tgl)
                ->setCellValue("W$kolom", $d->nm_produk)
                ->setCellValue("X$kolom", $d->dosis)
                ->setCellValue("Y$kolom", $d->dosis_satuan)
                ->setCellValue("Z$kolom", $d->campuran)
                ->setCellValue("AA$kolom", $d->campuran_satuan)
                ->setCellValue("AB$kolom", $d->waktu)
                ->setCellValue("AC$kolom", $d->cara)
                ->setCellValue("AD$kolom", $d->ket)
                ->setCellValue("AE$kolom", $d->nm_kandang);
            $kolom++;
        }
        $batas = $kolom - 1;
        $sheet->getStyle("U1:AE$batas")->applyFromArray($style);

        // Obat Ayam
        $sheet->setCellValue('AG1', 'Obat Ayam')
            ->setCellValue('AH1', 'ID')
            ->setCellValue('AI1', 'Tanggal')
            ->setCellValue('AJ1', 'Nama Obat')
            ->setCellValue('AK1', 'Dosis / Ayam')
            ->setCellValue('AL1', 'Satuan')
            ->setCellValue('AM1', 'TTL Dosis')
            ->setCellValue('AN1', 'Satuan')
            ->setCellValue('AO1', 'Kandang');

        $kolom = 2;
        foreach ($obat_ayam as $no => $d) {
            $pop = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                            LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                            WHERE a.id_kandang = '$d->id_kandang'");
            $populasi = $pop->stok_awal - $pop->pop;

            $sheet->setCellValue("AH$kolom", $no + 1)
                ->setCellValue("AI$kolom", $d->tgl)
                ->setCellValue("AJ$kolom", $d->nm_produk)
                ->setCellValue("AK$kolom", $d->dosis)
                ->setCellValue("AL$kolom", 'Gr')
                ->setCellValue("AM$kolom", $d->dosis * $populasi)
                ->setCellValue("AN$kolom", 'Gr')
                ->setCellValue("AO$kolom", $d->nm_kandang);
            $kolom++;
        }
        $batas = $kolom - 1;
        $sheet->getStyle("AH1:AO$batas")->applyFromArray($style);


        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Pakan Perencanan.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function daily_layer(Request $r)
    {
        $id_kandang = $r->id_kandang;

        $kandang = DB::selectOne("SELECT a.stok_awal as ayam_awal, a.nm_kandang, b.nm_strain FROM `kandang` as a
        LEFT JOIN strain as b on a.id_strain = b.id_strain
        WHERE a.id_kandang = '$id_kandang'");

        $spreadsheet = new Spreadsheet;

        $style = array(
            'font' => array(
                'size' => 9
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

            ),
        );
        $style2 = array(
            'font' => array(
                'size' => 18,
                'setBold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ADD8E6')
            ),
        );
        $style3 = array(
            'font' => array(
                'size' => 9,
                'setBold' => true
            ),
        );

        // daily production
        $pullet = DB::select("SELECT a.tgl, sum(c.mati + c.jual) as pop, b.stok_awal, SUM(a.gr) as kg_pakan, TIMESTAMPDIFF(WEEK, b.chick_in , a.tgl) AS mgg,
        c.mati as death, c.jual as culling, normal.normalPcs, normal.normalKg, abnormal.abnormalPcs, abnormal.abnormalKg, d.pcs,d.kg, sum(d.pcs) as ttl_pcs, SUM(d.kg) as ttl_kg, b.chick_in as ayam_awal
        FROM tb_pakan_perencanaan as a
        LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
        LEFT JOIN populasi as c ON c.id_kandang = a.id_kandang AND c.tgl = a.tgl
        LEFT JOIN stok_telur as d ON d.id_kandang = a.id_kandang AND d.tgl = a.tgl
        LEFT JOIN (
            SELECT a.tgl,a.id_kandang, sum(a.pcs) as normalPcs, sum(a.kg) as normalKg FROM stok_telur as a
            WHERE a.id_telur = 1 AND a.id_kandang = '$id_kandang'
            GROUP BY a.tgl
        ) as normal ON normal.id_kandang = a.id_kandang AND normal.tgl = a.tgl
        LEFT JOIN (
            SELECT a.tgl,a.id_kandang, sum(a.pcs) as abnormalPcs, sum(a.kg) as abnormalKg FROM stok_telur as a
            WHERE a.id_telur != 1 AND a.id_kandang = '$id_kandang'
            GROUP BY a.tgl
        ) as abnormal ON abnormal.id_kandang = a.id_kandang AND abnormal.tgl = a.tgl
        WHERE a.id_kandang = '$id_kandang'
        GROUP BY a.tgl
        ORDER BY a.tgl ASC");
        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('DAILY PRODUCTION');

        $sheet1->setCellValue('A1', 'DAILY COMMERCIAL LAYER PRODUCTION')
            ->setCellValue('A3', "HEN HOUSE/POPULATION : $kandang->ayam_awal")
            ->setCellValue('A5', "HOUSE : $kandang->nm_kandang")
            ->setCellValue('A7', "STRAIN : $kandang->nm_strain")
            ->setCellValue('A9', 'DATE')
            ->setCellValue('B9', 'AGE OF BIRD')
            ->setCellValue('C9', 'CHICK AMOUNT')
            ->setCellValue('D9', 'DEPLETION')
            ->setCellValue('I9', 'FEED CONSUMTION')
            ->setCellValue('K9', 'EGG PRODUCTION')

            ->setCellValue('D10', 'BIRD DEATH')
            ->setCellValue('E10', 'BIRD CULLING')
            ->setCellValue('F10', 'BIRD TOTAL')
            ->setCellValue('G10', '%')
            ->setCellValue('H10', 'CUM')
            ->setCellValue('I10', 'PER DAY (KG)')
            ->setCellValue('J10', 'CUM')
            ->setCellValue('K10', 'PERDAY')

            ->setCellValue('K11', 'NORMAL')
            ->setCellValue('L11', 'ABNORMAL')
            ->setCellValue('M11', 'TOTAL')
            ->setCellValue('N11', '%HD')
            ->setCellValue('O11', 'CUM (BUTIR)')
            ->setCellValue('P11', 'WIGHT (KG) ')
            ->setCellValue('Q11', 'CUM (KG)')
            ->setCellValue('R9', 'EGG WEIGHT (g)')
            ->setCellValue('S9', 'FCR')
            ->setCellValue('S10', 'PER DAY')
            ->setCellValue('T10', 'CUM');

        $kolom = 12;
        $kum = 0;
        $cum_kg = 0;
        $cum_ttlpcs = 0;
        $cum_ttlkg = 0;

        foreach ($pullet as $d) {
            $kum += $d->death + $d->culling;
            $cum_kg += $d->kg_pakan;
            $cum_ttlpcs += $d->ttl_pcs;
            $cum_ttlkg += $d->ttl_kg;
            $populasi = $d->stok_awal - $d->pop;

            $birdTotal = $d->death + $d->culling;

            $sheet1->setCellValue("A$kolom", date('Y-m-d', strtotime($d->tgl)))
                ->setCellValue("B$kolom", $d->mgg)
                ->setCellValue("C$kolom", $populasi - $birdTotal ?? 0)
                ->setCellValue("D$kolom", $d->death ?? 0)
                ->setCellValue("E$kolom", $d->culling ?? 0)
                ->setCellValue("F$kolom", $birdTotal);
            $death = $d->death ?? 0;
            $culling = $d->culling ?? 0;
            $pop = $populasi  ?? 0;
            $sheet1->setCellValue("G$kolom", ($birdTotal) > 0 && $pop > 0 ? number_format((($death + $culling) / $pop) * 100, 2) : 0)
                ->setCellValue("H$kolom", $kum)
                ->setCellValue("I$kolom", $d->kg_pakan)
                ->setCellValue("J$kolom", $cum_kg)
                ->setCellValue("K$kolom", $d->normalPcs ?? 0)
                ->setCellValue("L$kolom", $d->abnormalPcs ?? 0)
                ->setCellValue("M$kolom", $d->abnormalPcs ?? 0 + $d->normalPcs ?? 0)
                ->setCellValue("N$kolom", $pop > 0 ? number_format(($d->ttl_pcs / $pop) * 100, 2) : 0)
                ->setCellValue("O$kolom", $cum_ttlpcs);
            $ttlPcs = $d->normalPcs ?? 0 + $d->abnormalPcs ?? 0;
            $weightKg = $d->ttl_kg - ($ttlPcs / 180);
            $sheet1->setCellValue("P$kolom", number_format($weightKg, 2))
                ->setCellValue("Q$kolom", number_format($cum_ttlkg - ($cum_ttlpcs / 180), 2))
                ->setCellValue("R$kolom", empty($d->normalPcs) ? 0 : number_format(($weightKg / $d->normalPcs ?? 0) * 1000, 2))
                ->setCellValue("S$kolom", number_format($d->kg_pakan ?? 0 / $weightKg ?? 0, 2))
                ->setCellValue("T$kolom", empty($cum_ttlpcs) ? 0 : number_format($cum_kg / ($cum_ttlkg - ($cum_ttlpcs / 180)), 2));

            $kolom++;
        }
        $sheet1->mergeCells("A1:R1")
            ->mergeCells("A3:C3")
            ->mergeCells("A5:C5")
            ->mergeCells("A7:C7")

            ->mergeCells("A9:A11")
            ->mergeCells("B9:B11")
            ->mergeCells("C9:C11")

            ->mergeCells("D10:D11")
            ->mergeCells("E10:E11")
            ->mergeCells("F10:F11")
            ->mergeCells("G10:G11")
            ->mergeCells("H10:H11")

            ->mergeCells("I10:I11")
            ->mergeCells("J10:J11")

            ->mergeCells("S10:S11")
            ->mergeCells("T10:T11")

            ->mergeCells("D9:H9")
            ->mergeCells("R9:R11")
            ->mergeCells("I9:J9")
            ->mergeCells("K9:Q9")

            ->mergeCells("S9:T9");

        $sheet1->getStyle('A1:T1')->applyFromArray($style2);
        $sheet1->getStyle('A9:T11')->applyFromArray($style2);
        $sheet1->getStyle('A3:B3')->applyFromArray($style3);
        $sheet1->getStyle('A5:B5')->applyFromArray($style3);
        $sheet1->getStyle('A7:B7')->applyFromArray($style3);
        $sheet1->getStyle('A9:T11')->applyFromArray($style);
        $sheet1->getStyle('A9:T9')->getAlignment()->setWrapText(true);
        $sheet1->getColumnDimension('B')->setWidth(10.64);
        $sheet1->getColumnDimension('D')->setWidth(8.36);
        $sheet1->getColumnDimension('F')->setWidth(9.82);
        $batas = $kolom - 1;
        $sheet1->getStyle('A12:T' . $batas)->applyFromArray($style);
        // end daily -----------------------------------------------

        // obat pakan -----------------
        $obat_pakan = DB::select("SELECT a.tgl,b.nm_produk, a.dosis,a.campuran, e.nm_satuan as dosis_satuan, f.nm_satuan as campuran_satuan,(a.dosis * c.ttl_pakan) as dosis_obat,d.debit
        FROM tb_obat_perencanaan as a
        LEFT JOIN tb_produk_perencanaan as b  ON a.id_produk = b.id_produk
        LEFT JOIN tb_satuan as e ON b.dosis_satuan = e.id_satuan
        LEFT JOIN tb_satuan as f on b.campuran_satuan = f.id_satuan
        LEFT JOIN (
            SELECT a.id_kandang , a.tgl, SUM(a.gr) AS ttl_pakan
                FROM tb_pakan_perencanaan AS a
                GROUP BY a.tgl , a.id_kandang
        )AS c ON c.id_kandang = a.id_kandang AND c.tgl = a.tgl
        LEFT JOIN (
            SELECT a.id_produk,SUM(b.debit) as debit FROM `tb_produk_perencanaan` as a
            LEFT JOIN jurnal as b ON a.id_produk = SUBSTRING_INDEX(RIGHT(b.ket, LENGTH(b.ket) - INSTR(b.ket, '-')), '-', -1)
            WHERE a.kategori = 'obat_pakan' AND b.debit != 0
            GROUP BY a.id_produk
        ) AS d ON d.id_produk = a.id_produk
        WHERE b.kategori = 'obat_pakan' AND a.id_kandang = '$id_kandang' ORDER BY a.tgl DESC");

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(1);
        $sheet2 = $spreadsheet->getActiveSheet(1);
        $sheet2->setTitle('OBAT PAKAN');
        $sheet2->setCellValue('A1', 'Tanggal')
            ->setCellValue('B1', 'Nama Obat')
            ->setCellValue('C1', 'Dosis')
            ->setCellValue('D1', 'Satuan')
            ->setCellValue('E1', 'Campuran')
            ->setCellValue('F1', 'Satuan')
            ->setCellValue('G1', 'Ttl Dosis')
            ->setCellValue('H1', 'Cost');

        $kolom = 2;
        foreach ($obat_pakan as $d) {
            $sheet2->setCellValue("A$kolom", date('Y-m-d', strtotime($d->tgl)))
                ->setCellValue("B$kolom", $d->nm_produk)
                ->setCellValue("C$kolom", $d->dosis)
                ->setCellValue("D$kolom", $d->dosis_satuan)
                ->setCellValue("E$kolom", $d->campuran)
                ->setCellValue("F$kolom", $d->campuran_satuan)
                ->setCellValue("G$kolom", $d->dosis_obat)
                ->setCellValue("H$kolom", $d->debit);
            $kolom++;
        }

        $batas = $kolom - 1;
        $sheet2->getStyle('A1:H' . $batas)->applyFromArray($style);
        // end obat pakan ---------------------------------

        // obat air -------------------------
        $obat_air = $this->getProdukObat($id_kandang, 'obat_air');

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(2);
        $sheet3 = $spreadsheet->getActiveSheet(2);
        $sheet3->setTitle('OBAT AIR');
        $sheet3->setCellValue('A1', 'Tanggal')
            ->setCellValue('B1', 'Nama Obat')
            ->setCellValue('C1', 'Dosis')
            ->setCellValue('D1', 'Satuan')
            ->setCellValue('E1', 'Campuran')
            ->setCellValue('F1', 'Satuan')
            ->setCellValue('G1', 'Waktu')
            ->setCellValue('H1', 'Cara')
            ->setCellValue('I1', 'Cost');

        $kolom = 2;
        foreach ($obat_air as $d) {
            $sheet3->setCellValue('A' . $kolom, date('Y-m-d', strtotime($d->tgl)))
                ->setCellValue('B' . $kolom, $d->nm_produk)
                ->setCellValue("C$kolom", $d->dosis)
                ->setCellValue("D$kolom", $d->dosis_satuan)
                ->setCellValue("E$kolom", $d->campuran)
                ->setCellValue("F$kolom", $d->campuran_satuan)
                ->setCellValue('G' . $kolom, $d->waktu)
                ->setCellValue('H' . $kolom, $d->cara)
                ->setCellValue('I' . $kolom, round($d->debit, 0));
            $kolom++;
        }
        $batas = $kolom - 1;
        $sheet3->getStyle('A1:I' . $batas)->applyFromArray($style);
        // end obat air --------------------------------------------


        // obat ayam -----------------------
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(3);
        $sheet4 = $spreadsheet->getActiveSheet(3);
        $sheet4->setTitle('OBAT AYAM');
        $sheet4->setCellValue('A1', 'Tanggal')
            ->setCellValue('B1', 'Nama Obat')
            ->setCellValue('C1', 'Dosis')
            ->setCellValue('D1', 'Satuan')
            ->setCellValue('E1', 'Dosis Perekor')
            ->setCellValue('F1', 'Cost');

        $obat_ayam = $this->getProdukObat($id_kandang, 'obat_ayam');
        $pop = DB::selectOne("SELECT sum(a.mati + a.jual) as pop,b.stok_awal FROM populasi as a
                            LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
                            WHERE a.id_kandang = '$id_kandang'");
        $populasi = $pop->stok_awal - $pop->pop;
        $kolom = 2;
        foreach ($obat_ayam as $d) {
            $sheet4->setCellValue('A' . $kolom, date('Y-m-d', strtotime($d->tgl)))
                ->setCellValue('B' . $kolom, $d->nm_produk)
                ->setCellValue('C' . $kolom, $d->dosis * $populasi)
                ->setCellValue('D' . $kolom, $d->dosis_satuan)
                ->setCellValue('E' . $kolom, $d->dosis)
                ->setCellValue('F' . $kolom, round($d->debit, 0));
            $kolom++;
        }

        $batas = $kolom - 1;
        $sheet4->getStyle('A1:F' . $batas)->applyFromArray($style);
        // end obat ayam -----------------------------------------

        // vaksin ------------------------
        $vaksin = DB::table('tb_vaksin_perencanaan')->where('id_kandang', $id_kandang)->get();
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex(4);
        $sheet5 = $spreadsheet->getActiveSheet(4);
        $sheet5->setTitle('VAKSIN');
        $sheet5->setCellValue('A1', 'Tanggal')
            ->setCellValue('B1', 'Nama Vaksin')
            ->setCellValue('C1', 'Dosis')
            ->setCellValue('D1', 'Cost');

        $kolom = 2;
        foreach ($vaksin as $d) {
            $sheet5->setCellValue("A$kolom", date('Y-m-d', strtotime($d->tgl)))
                ->setCellValue("B$kolom", $d->nm_vaksin)
                ->setCellValue("C$kolom", $d->qty)
                ->setCellValue("D$kolom", $d->ttl_rp);
            $kolom++;
        }
        $batas = $kolom - 1;
        $sheet5->getStyle('A1:D' . $batas)->applyFromArray($style);
        // end vaksin ---------------------------------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Daily Layer.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function week_layer(Request $r)
    {
        $id_kandang = $r->id_kandang;
        $kandang = DB::selectOne("SELECT a.stok_awal as ayam_awal, a.nm_kandang, a.strain as nm_strain FROM `kandang` as a
        WHERE a.id_kandang = '$id_kandang'");

        $spreadsheet = new Spreadsheet;

        $style = array(
            'font' => array(
                'size' => 9
            ),
            'borders' => array(
                'allBorders' => array(
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ),
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

            ),
        );
        $style2 = array(
            'font' => array(
                'size' => 18,
                'setBold' => true
            ),
            'alignment' => array(
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ),
            'fill' => array(
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => array('argb' => 'ADD8E6')
            ),
        );
        $style3 = array(
            'font' => array(
                'size' => 9,
                'setBold' => true
            ),
        );

        // daily production
        $pullet = DB::select("SELECT a.tgl, sum(c.mati + c.jual) as pop, b.stok_awal, SUM(a.gr) as kg_pakan, TIMESTAMPDIFF(WEEK, b.chick_in , a.tgl) AS mgg,
        c.mati as death, c.jual as culling, normal.normalPcs, normal.normalKg, abnormal.abnormalPcs, abnormal.abnormalKg, d.pcs,d.kg, sum(d.pcs) as ttl_pcs, SUM(d.kg) as ttl_kg, b.chick_in as ayam_awal
        
        FROM tb_pakan_perencanaan as a
        LEFT JOIN kandang as b ON a.id_kandang = b.id_kandang
        LEFT JOIN populasi as c ON c.id_kandang = a.id_kandang AND c.tgl = a.tgl
        LEFT JOIN stok_telur as d ON d.id_kandang = a.id_kandang AND d.tgl = a.tgl
        LEFT JOIN (
            SELECT a.tgl,a.id_kandang, sum(a.pcs) as normalPcs, sum(a.kg) as normalKg FROM stok_telur as a
            WHERE a.id_telur = 1 AND a.id_kandang = '$id_kandang'
            GROUP BY a.tgl
        ) as normal ON normal.id_kandang = a.id_kandang AND normal.tgl = a.tgl
        LEFT JOIN (
            SELECT a.tgl,a.id_kandang, sum(a.pcs) as abnormalPcs, sum(a.kg) as abnormalKg FROM stok_telur as a
            WHERE a.id_telur != 1 AND a.id_kandang = '$id_kandang'
            GROUP BY a.tgl
        ) as abnormal ON abnormal.id_kandang = a.id_kandang AND abnormal.tgl = a.tgl
        WHERE a.id_kandang = '$id_kandang'
        GROUP BY a.tgl
        ORDER BY a.tgl ASC");
        $spreadsheet->setActiveSheetIndex(0);
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Recording CV Agrilaras');

        $sheet1->getStyle('A1:Z1')->applyFromArray($style2);
        $sheet1->getStyle('A9:Z11')->applyFromArray($style2);
        $sheet1->getStyle('A3:B3')->applyFromArray($style3);
        $sheet1->getStyle('A5:B5')->applyFromArray($style3);
        $sheet1->getStyle('A7:B7')->applyFromArray($style3);
        $sheet1->getStyle('A9:Z11')->applyFromArray($style);
        $sheet1->getStyle('A9:Z9')->getAlignment()->setWrapText(true);
        $sheet1->getColumnDimension('B')->setWidth(10.64);
        $sheet1->getColumnDimension('D')->setWidth(8.36);
        $sheet1->getColumnDimension('F')->setWidth(9.82);

        $sheet1->setCellValue('A1', 'COMMERCIAL LAYER PRODUCTION')
            ->mergeCells("A1:Z1")
            ->mergeCells("A3:C3")
            ->mergeCells("A5:C5")
            ->mergeCells("A7:C7")
            ->setCellValue('A3', "HEN HOUSE/POPULATION : $kandang->ayam_awal")
            ->setCellValue('A5', "HOUSE : $kandang->nm_kandang")
            ->setCellValue('A7', "STRAIN : $kandang->nm_strain")
            ->setCellValue('A9', 'DATE END OF WEEK')
            ->setCellValue('B9', 'WEEK OF PROD')
            ->setCellValue('B11', 'AGE')
            ->setCellValue('C11', 'AOL')
            ->setCellValue('D9', 'CHICK AMOUNT')
            ->setCellValue('E9', 'DEPLETION')
            ->setCellValue('E10', 'PER WEEK')
            ->setCellValue('E11', 'BIRD')
            ->setCellValue('F11', '(%)')
            ->setCellValue('G10', 'CUM')
            ->setCellValue('G11', 'BIRD')
            ->setCellValue('H11', '(%)')
            ->mergeCells("A9:A11")
            ->mergeCells("B9:C10")
            ->mergeCells("D9:D11")
            ->mergeCells("E9:H9")
            ->mergeCells("E10:F11")
            ->mergeCells("G10:H11")

            ->setCellValue('I9', 'FEED CONSUMTION (kg)')
            ->mergeCells("I9:K9")

            ->setCellValue('I10', 'PER WEEK')
            ->mergeCells("I10:I11")

            ->setCellValue('J10', 'CUM')
            ->mergeCells("J10:J11")

            ->setCellValue('K10', 'FEED/DAY 100/BIRDS')
            ->mergeCells("K10:K11")

            ->setCellValue('L9', 'EGG PRODUCTION')
            ->mergeCells("L9:Q9")

            ->setCellValue('L10', 'PER WEEK')
            ->mergeCells("L10:L11")

            ->setCellValue('M10', 'CUM')
            ->mergeCells("M10:M11")

            ->setCellValue('N10', '%HD')
            ->setCellValue('N11', 'ACT')
            ->setCellValue('O11', 'STD')
            ->mergeCells("N10:O10")

            ->setCellValue('P10', 'CUM HH')
            ->setCellValue('P11', 'STD')
            ->setCellValue('Q11', 'ACT')
            ->mergeCells("P10:Q10")

            ->setCellValue('R9', 'EGG WEIGHT (GRAM/BUTIR)')
            ->mergeCells("R9:S10")

            ->setCellValue('R11', 'STD')
            ->setCellValue('S11', 'ACT')
            ->setCellValue('T9', 'WEIGHT EGG PRODUCTION')
            ->mergeCells("T9:W9")

            ->setCellValue('T10', 'PER WEEK')
            ->setCellValue('T11', 'KG')
            ->setCellValue('U11', 'CUM')
            ->mergeCells("T10:U10")

            ->setCellValue('V10', 'CUM HH (KG)')
            ->mergeCells("V10:W10")

            ->setCellValue('V11', 'STD')
            ->setCellValue('W11', 'ACT')
            ->setCellValue('X9', 'FCR')
            ->mergeCells("X9:Y9")

            ->setCellValue('X10', 'PER WEEK')
            ->setCellValue('Y10', 'CUM')
            ->mergeCells("X10:X11")
            ->mergeCells("Y10:Y11")

            ->setCellValue('Z9', 'KG EGG/ 100BIRDS/ DAY')
            ->mergeCells("Z9:Z11");

        $kolom = 12;
        $kum = 0;
        $cum_kg = 0;
        $cum_ttlpcs = 0;
        $cum_ttlkg = 0;

        // foreach ($pullet as $d) {
        //     $kum += $d->death + $d->culling;
        //     $cum_kg += $d->kg_pakan;
        //     $cum_ttlpcs += $d->ttl_pcs;
        //     $cum_ttlkg += $d->ttl_kg;
        //     $populasi = $d->stok_awal - $d->pop;

        //     $birdTotal = $d->death + $d->culling;

        //     $sheet1->setCellValue("A$kolom", date('Y-m-d', strtotime($d->tgl)))
        //         ->setCellValue("B$kolom", $d->mgg)
        //         ->setCellValue("C$kolom", $populasi - $birdTotal ?? 0)
        //         ->setCellValue("D$kolom", $d->death ?? 0)
        //         ->setCellValue("E$kolom", $d->culling ?? 0)
        //         ->setCellValue("F$kolom", $birdTotal);
        //     $death = $d->death ?? 0;
        //     $culling = $d->culling ?? 0;
        //     $pop = $populasi  ?? 0;
        //     $sheet1->setCellValue("G$kolom", ($birdTotal) > 0 && $pop > 0 ? number_format((($death + $culling) / $pop) * 100, 2) : 0)
        //         ->setCellValue("H$kolom", $kum)
        //         ->setCellValue("I$kolom", $d->kg_pakan)
        //         ->setCellValue("J$kolom", $cum_kg)
        //         ->setCellValue("K$kolom", $d->normalPcs ?? 0)
        //         ->setCellValue("L$kolom", $d->abnormalPcs ?? 0)
        //         ->setCellValue("M$kolom", $d->abnormalPcs ?? 0 + $d->normalPcs ?? 0)
        //         ->setCellValue("N$kolom", $pop > 0 ? number_format(($d->ttl_pcs / $pop) * 100, 2) : 0)
        //         ->setCellValue("O$kolom", $cum_ttlpcs);
        //     $ttlPcs = $d->normalPcs ?? 0 + $d->abnormalPcs ?? 0;
        //     $weightKg = $d->ttl_kg - ($ttlPcs / 180);
        //     $sheet1->setCellValue("P$kolom", number_format($weightKg, 2))
        //         ->setCellValue("Q$kolom", number_format($cum_ttlkg - ($cum_ttlpcs / 180), 2))
        //         ->setCellValue("R$kolom", empty($d->normalPcs) ? 0 : number_format(($weightKg / $d->normalPcs ?? 0) * 1000,2))
        //         ->setCellValue("S$kolom", number_format($d->kg_pakan ?? 0 / $weightKg ?? 0,2))
        //         ->setCellValue("T$kolom", number_format($cum_kg / ($cum_ttlkg - ($cum_ttlpcs / 180)),2));

        //     $kolom++;
        // }

        $batas = $kolom - 1;
        $sheet1->getStyle('A12:T' . $batas)->applyFromArray($style);
        // end daily -----------------------------------------------


        // vaksin ------------------------
        $vaksin = DB::table('tb_vaksin_perencanaan')->where('id_kandang', $id_kandang)->get();
        // $spreadsheet->createSheet();
        // $spreadsheet->setActiveSheetIndex(4);
        // $sheet5 = $spreadsheet->getActiveSheet(4);
        // $sheet5->setTitle('VAKSIN');
        // $sheet5->setCellValue('A1', 'Tanggal')
        //     ->setCellValue('B1', 'Nama Vaksin')
        //     ->setCellValue('C1', 'Dosis')
        //     ->setCellValue('D1', 'Cost');

        // $kolom = 2;
        // foreach ($vaksin as $d) {
        //     $sheet5->setCellValue("A$kolom", date('Y-m-d', strtotime($d->tgl)))
        //         ->setCellValue("B$kolom", $d->nm_vaksin)
        //         ->setCellValue("C$kolom", $d->qty)
        //         ->setCellValue("D$kolom", $d->ttl_rp);
        //     $kolom++;
        // }
        // $batas = $kolom - 1;
        // $sheet5->getStyle('A1:D' . $batas)->applyFromArray($style);
        // end vaksin ---------------------------------------------

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Daily Layer.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit();
    }

    public function transfer_ayam(Request $r)
    {
        $data = [
            'tgl' => $r->tgl,
            'debit' => $r->qty,
            'kredit' => 0,
            'id_gudang' => '2',
            'admin' =>  auth()->user()->name,
            'jenis' => 'ayam',
            'transfer' => 'Y'
        ];
        DB::table('stok_ayam')->insert($data);

        $data = [
            'tgl' => $r->tgl,
            'debit' => 0,
            'kredit' => $r->qty,
            'id_gudang' => '1',
            'admin' =>  auth()->user()->name,
            'jenis' => 'ayam',
            'transfer' => 'Y'
        ];
        DB::table('stok_ayam')->insert($data);

        return redirect()->route('dashboard_kandang.index')->with('Data Berhasil Ditambahkan');
    }
    public function penjualan_ayam(Request $r)
    {
        $max = DB::table('invoice_ayam')->latest('urutan')->first();
        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->urutan + 1;
        }
        $data = [
            'tgl' => $r->tgl,
            'debit' => 0,
            'kredit' => $r->qty,
            'id_gudang' => '1',
            'admin' =>  auth()->user()->name,
            'jenis' => 'ayam',
            'no_nota' => 'PA-' . $nota_t,
            'transfer' => 'Y'
        ];
        DB::table('stok_ayam')->insert($data);

        $max = DB::table('invoice_ayam')->latest('urutan')->first();
        if (empty($max)) {
            $nota_t = '1000';
        } else {
            $nota_t = $max->urutan + 1;
        }
        $data = [
            'tgl' => $r->tgl,
            'no_nota' => 'PA-' . $nota_t,
            'customer' => $r->customer,
            'qty' => $r->qty,
            'h_satuan' => $r->h_satuan,
            'admin' =>  auth()->user()->name,
            'urutan' =>  $nota_t
        ];
        DB::table('invoice_ayam')->insert($data);
        $nota =  'PA-' . $nota_t;
        return redirect()->route('dashboard_kandang.cek_penjualan_ayam', ['no_nota' => $nota])->with('Data Berhasil Ditambahkan');
    }

    public function cek_penjualan_ayam(Request $r)
    {
        $data = [
            'title' => 'Cek invoice ayam',
            'ayam' => DB::table('invoice_ayam')->where('no_nota', $r->no_nota)->first()
        ];
        return view('dashboard_kandang.penjualan_ayam.cek_invoice', $data);
    }
}
