<?php

namespace App\Http\Controllers;

use App\Models\FakturModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransaksiPenerimaanController extends Controller
{
    public function gudang(): View
    {
        return view('gudang.index', [
            'title' => 'Gudang',
        ]);
    }

    public function penerimaanIndex(Request $request): View
    {
        $tanggalAwal = $request->input('tanggal_awal', now()->startOfMonth()->toDateString());
        $tanggalAkhir = $request->input('tanggal_akhir', now()->toDateString());
        $cari = $request->input('cari');
        $statusPenerimaan = $request->input('status', 'belum') === 'selesai' ? 'selesai' : 'belum';

        $semuaFaktur = FakturModel::with('supplier')
            ->whereBetween('tanggal_faktur', [$tanggalAwal, $tanggalAkhir])
            ->when($cari, function ($query) use ($cari) {
                $query->where(function ($q) use ($cari) {
                    $q->where('no_faktur', 'like', "%{$cari}%")
                        ->orWhereHas('supplier', function ($sq) use ($cari) {
                            $sq->where('nm_suplier', 'like', "%{$cari}%");
                        });
                });
            })
            ->orderByDesc('tanggal_faktur')
            ->get();

        $penerimaanFaktur = $this->qtyDiterimaFaktur($semuaFaktur);
        $belumHabis = $semuaFaktur->filter(function ($item) use ($penerimaanFaktur) {
            return (float) ($penerimaanFaktur[$item->no_faktur] ?? 0) < (float) $item->total_qty;
        })->values();
        $sudahHabis = $semuaFaktur->filter(function ($item) use ($penerimaanFaktur) {
            return (float) ($penerimaanFaktur[$item->no_faktur] ?? 0) >= (float) $item->total_qty;
        })->values();

        $dataFaktur = $statusPenerimaan === 'selesai' ? $sudahHabis : $belumHabis;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 15;
        $faktur = new LengthAwarePaginator(
            $dataFaktur->forPage($page, $perPage)->values(),
            $dataFaktur->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('transaksi.penerimaan.index', [
            'title' => 'Penerimaan Stok',
            'tanggalAwal' => $tanggalAwal,
            'tanggalAkhir' => $tanggalAkhir,
            'faktur' => $faktur,
            'penerimaanFaktur' => $penerimaanFaktur,
            'statusPenerimaan' => $statusPenerimaan,
            'jumlahBelumHabis' => $belumHabis->count(),
            'jumlahSudahHabis' => $sudahHabis->count(),
            'btnTerima' => \SettingHal::btnHal(176, auth()->id()),
            'btnBatalkan' => \SettingHal::btnHal(177, auth()->id()),
        ]);
    }

    public function detail(FakturModel $faktur_pembelian): View
    {
        $faktur_pembelian->load(['supplier', 'detail.produk', 'detail.produkUmum', 'detail.akunPembayaran']);
        $hargaHppByDetail = $this->hargaHppByDetail(collect([$faktur_pembelian]));
        $biayaLain = collect($faktur_pembelian->biaya_lain ?? []);
        $akunBiaya = DB::table('akun_perkiraan')
            ->whereIn('id_akun_perkiraan', $biayaLain->pluck('id_akun')->filter()->unique())
            ->get(['id_akun_perkiraan', 'kode_perkiraan', 'nama'])
            ->keyBy('id_akun_perkiraan');

        if ($faktur_pembelian->jenis_faktur === 'barang_umum') {
            $qtyDiterimaByProduk = DB::table('pembukuan_baru_stok')->where('nomor_transaksi', $faktur_pembelian->no_faktur)
                ->groupBy('id_produk')->select('id_produk')->selectRaw('SUM(qty) as qty')->pluck('qty', 'id_produk');
        } else {
            $qtyDiterimaByProduk = DB::table('stok_produk_perencanaan')->where('no_nota', $faktur_pembelian->no_faktur)
                ->groupBy('id_pakan')->select('id_pakan')->selectRaw($faktur_pembelian->jenis_faktur === 'pakan' ? 'SUM(pcs / 50000) as qty' : 'SUM(pcs) as qty')->pluck('qty', 'id_pakan');
        }

        $jurnal = DB::table('jurnal_perkiraan as j')
            ->leftJoin('akun_perkiraan as a', 'a.id_akun_perkiraan', '=', 'j.id_akun_perkiraan')
            ->where('j.nomor_transaksi', $faktur_pembelian->no_faktur)
            ->orderBy('j.urutan_detail')
            ->get(['j.*', 'a.kode_perkiraan', 'a.nama']);

        return view('transaksi.faktur_pembelian.detail', [
            'title' => 'Detail Faktur Pembelian',
            'faktur' => $faktur_pembelian,
            'qtyDiterimaByProduk' => $qtyDiterimaByProduk,
            'jurnal' => $jurnal,
            'akunBiaya' => $akunBiaya,
            'hargaHppByDetail' => $hargaHppByDetail,
            'sudahAdaPenerimaan' => $this->fakturSudahAdaPenerimaan($faktur_pembelian),
        ]);
    }

    public function terimaBatch(Request $request): View|RedirectResponse
    {
        $ids = collect($request->input('faktur', []))
            ->filter()
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return redirect()
                ->route('transaksi.penerimaan.index')
                ->with('error', 'Pilih minimal 1 faktur untuk penerimaan stok.');
        }

        $fakturs = FakturModel::with(['supplier', 'detail.produk', 'detail.produkUmum'])
            ->whereIn('id', $ids)
            ->orderBy('tanggal_faktur')
            ->orderBy('no_faktur')
            ->get();

        $qtyDiterimaByNota = $this->qtyDiterimaByNota($fakturs);

        return view('transaksi.penerimaan.terima_batch', [
            'title' => 'Penerimaan Stok Beberapa Nota',
            'fakturs' => $fakturs,
            'qtyDiterimaByNota' => $qtyDiterimaByNota,
            'hargaHppByDetail' => $this->hargaHppByDetail($fakturs),
        ]);
    }

    public function storeTerimaBatch(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_terima' => ['required', 'date'],
            'faktur' => ['required', 'array', 'min:1'],
            'faktur.*' => ['required', 'integer', 'exists:faktur_pembelian,id'],
            'detail' => ['required', 'array', 'min:1'],
            'detail.*.qty_diterima' => ['required', 'numeric', 'min:0.01'],
        ]);

        $fakturs = FakturModel::with(['detail.produk', 'detail.produkUmum'])
            ->whereIn('id', $validated['faktur'])
            ->get();

        $qtyDiterimaByNota = $this->qtyDiterimaByNota($fakturs);
        $hargaHppByDetail = $this->hargaHppByDetail($fakturs);

        DB::transaction(function () use ($validated, $fakturs, $qtyDiterimaByNota, $hargaHppByDetail) {
            $admin = auth()->user()->name ?? 'system';
            $rows = [];
            $jumlahDiterima = 0;

            foreach ($fakturs as $faktur) {
                foreach ($faktur->detail as $detail) {
                    $qtyDiterima = (float) data_get($validated, 'detail.' . $detail->id . '.qty_diterima', 0);
                    $qtySebelumnya = (float) data_get($qtyDiterimaByNota, $faktur->no_faktur . '.' . $detail->pakan_id, 0);
                    $qtySisa = max((float) $detail->qty - $qtySebelumnya, 0);

                    if ($qtyDiterima <= 0) {
                        continue;
                    }
                    $jumlahDiterima++;

                    abort_if(
                        $qtyDiterima > $qtySisa,
                        422,
                        'Qty diterima ' . $faktur->no_faktur . ' - ' . (($detail->sumber_produk ?? 'perencanaan') === 'barang_umum' ? ($detail->produkUmum->nm_produk ?? 'produk') : ($detail->produk->nm_produk ?? 'produk')) . ' melebihi sisa faktur.'
                    );

                    $qtyStok = $faktur->jenis_faktur === 'pakan'
                        ? $qtyDiterima * 50000
                        : $qtyDiterima;
                    $hargaHpp = (float) ($hargaHppByDetail[$detail->id] ?? $detail->harga_satuan);
                    $biayaTambahanPerUnit = max($hargaHpp - (float) $detail->harga_satuan, 0);

                    if ($faktur->jenis_faktur === 'barang_umum') {
                        DB::table('pembukuan_baru_stok')->insert([
                            'id_produk' => $detail->pakan_id,
                            'nama_produk' => $detail->produkUmum->nm_produk ?? 'Barang Umum',
                            'satuan' => $detail->satuan,
                            'qty' => $qtyDiterima,
                            'harga_satuan' => $hargaHpp,
                            'tanggal' => $validated['tanggal_terima'],
                            'nomor_transaksi' => $faktur->no_faktur,
                            'created_at' => now(), 'updated_at' => now(),
                        ]);
                        continue;
                    }

                    $rows[] = [
                        'id_kandang' => 0,
                        'id_pakan' => $detail->pakan_id,
                        'tgl' => $validated['tanggal_terima'],
                        'pcs' => $qtyStok,
                        'pcs_kredit' => 0,
                        'pcs_selisih' => null,
                        'admin' => $admin,
                        'check' => 'Y',
                        'cek_admin' => $admin,
                        'opname' => 'T',
                        'total_rp' => round($qtyDiterima * $hargaHpp, 2),
                        'biaya_dll' => round($qtyDiterima * $biayaTambahanPerUnit, 2),
                        'no_nota' => $faktur->no_faktur,
                        'h_opname' => 'T',
                        'penyesuaian' => 'T',
                    ];
                }
            }

            abort_if($jumlahDiterima === 0, 422, 'Qty diterima harus diisi minimal 1 item.');

            if ($rows) DB::table('stok_produk_perencanaan')->insert($rows);
        });

        return redirect()
            ->route('transaksi.penerimaan.index')
            ->with('sukses', 'Stok beberapa faktur berhasil diterima.');
    }

    public function batalkanPenerimaan(FakturModel $faktur_pembelian): RedirectResponse
    {
        $faktur = $faktur_pembelian;

        try {
            DB::transaction(function () use ($faktur) {
                if ($faktur->jenis_faktur === 'barang_umum') {
                    $dihapus = DB::table('pembukuan_baru_stok')->where('nomor_transaksi', $faktur->no_faktur)->delete();
                    throw_if($dihapus === 0, \RuntimeException::class, 'Penerimaan stok faktur ini sudah tidak ditemukan.');
                    return;
                }

                $dihapus = DB::table('stok_produk_perencanaan')->where('no_nota', $faktur->no_faktur)->delete();
                throw_if($dihapus === 0, \RuntimeException::class, 'Penerimaan stok faktur ini sudah tidak ditemukan.');
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('transaksi.penerimaan.index', ['status' => 'selesai'])
                ->with('error', $e->getMessage());
        }

        return redirect()->route('transaksi.penerimaan.index', ['status' => 'selesai'])
            ->with('sukses', 'Penerimaan stok ' . $faktur->no_faktur . ' berhasil dibatalkan.');
    }

    private function qtyDiterimaFaktur($fakturs)
    {
        $fakturs = collect($fakturs);

        if ($fakturs->isEmpty()) {
            return collect();
        }

        $hasil = DB::table('faktur_pembelian as f')
            ->leftJoin('stok_produk_perencanaan as s', 's.no_nota', '=', 'f.no_faktur')
            ->whereIn('f.no_faktur', $fakturs->pluck('no_faktur'))
            ->groupBy('f.no_faktur', 'f.jenis_faktur')
            ->select('f.no_faktur')
            ->selectRaw("COALESCE(SUM(CASE WHEN f.jenis_faktur = 'pakan' THEN s.pcs / 50000 ELSE s.pcs END), 0) as qty_diterima")
            ->pluck('qty_diterima', 'no_faktur');
        $umum = DB::table('pembukuan_baru_stok')->whereIn('nomor_transaksi', $fakturs->pluck('no_faktur'))
            ->groupBy('nomor_transaksi')->select('nomor_transaksi')->selectRaw('SUM(qty) as qty_diterima')->pluck('qty_diterima', 'nomor_transaksi');
        foreach ($umum as $nota => $qty) $hasil[$nota] = $qty;
        return $hasil;
    }

    private function qtyDiterimaByNota($fakturs): array
    {
        $fakturs = collect($fakturs);

        if ($fakturs->isEmpty()) {
            return [];
        }

        $hasil = DB::table('stok_produk_perencanaan as s')
            ->join('faktur_pembelian as f', 'f.no_faktur', '=', 's.no_nota')
            ->whereIn('f.no_faktur', $fakturs->pluck('no_faktur'))
            ->groupBy('f.no_faktur', 'f.jenis_faktur', 's.id_pakan')
            ->select('f.no_faktur', 's.id_pakan')
            ->selectRaw("SUM(CASE WHEN f.jenis_faktur = 'pakan' THEN s.pcs / 50000 ELSE s.pcs END) as qty")
            ->get()
            ->groupBy('no_faktur')
            ->map(fn($rows) => $rows->pluck('qty', 'id_pakan')->all())
            ->all();
        $umum = DB::table('pembukuan_baru_stok as s')->join('faktur_pembelian as f', 'f.no_faktur', '=', 's.nomor_transaksi')
            ->where('f.jenis_faktur', 'barang_umum')->whereIn('f.no_faktur', $fakturs->pluck('no_faktur'))
            ->groupBy('f.no_faktur', 's.id_produk')->select('f.no_faktur', 's.id_produk')->selectRaw('SUM(s.qty) as qty')->get()
            ->groupBy('no_faktur')->map(fn($rows) => $rows->pluck('qty', 'id_produk')->all())->all();
        return array_replace($hasil, $umum);
    }

    private function fakturSudahAdaPenerimaan(FakturModel $faktur): bool
    {
        return $faktur->jenis_faktur === 'barang_umum'
            ? DB::table('pembukuan_baru_stok')->where('nomor_transaksi', $faktur->no_faktur)->exists()
            : DB::table('stok_produk_perencanaan')->where('no_nota', $faktur->no_faktur)->exists();
    }

    private function hargaHppByDetail($fakturs)
    {
        $hasil = collect();

        foreach (collect($fakturs) as $faktur) {
            $details = collect($faktur->detail)->values();
            $totalItem = (float) $details->sum(fn($detail) => (float) $detail->subtotal);
            $totalBiayaTambahan = (float) collect($faktur->biaya_lain ?? [])->sum('nominal');
            $sisaBiaya = round($totalBiayaTambahan, 2);

            foreach ($details as $index => $detail) {
                $subtotal = (float) $detail->subtotal;
                $qty = (float) $detail->qty;
                $alokasi = $index === $details->count() - 1
                    ? $sisaBiaya
                    : round($totalItem > 0
                        ? $totalBiayaTambahan * $subtotal / $totalItem
                        : $totalBiayaTambahan / max($details->count(), 1), 2);
                $sisaBiaya = round($sisaBiaya - $alokasi, 2);

                $hasil->put($detail->id, $qty > 0
                    ? round(($subtotal + $alokasi) / $qty, 6)
                    : (float) $detail->harga_satuan);
            }
        }

        return $hasil;
    }
}
