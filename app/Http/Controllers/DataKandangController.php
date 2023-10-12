<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataKandangController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Data Kandang',
            'kandang' => DB::table('kandang as a')->join('strain as b', 'a.id_strain', 'b.id_strain')->get()
        ];
        return view('data_kandang.data_kandang', $data);
    }

    public function store(Request $r)
    {
        // dd($r->all());
        $id_kandang = DB::table('kandang')->insertGetId([
            'chick_in' => $r->tgl_lahir,
            'tgl_masuk' => $r->tgl_masuk,
            'nm_kandang' => $r->nm_kandang,
            'id_strain' => $r->strain,
            'stok_awal' => $r->ayam_awal,
            'chick_out' => $r->chick_out,
            'rupiah' => $r->rupiah
        ]);

        // Setelah mendapatkan ID kandang, Anda dapat menggunakannya untuk operasi insert di tabel 'populasi'
        DB::table('populasi')->insert([
            'id_kandang' => $id_kandang, // Gunakan ID kandang yang baru saja dibuat
            'mati' => '0',
            'jual' => '0',
            'tgl' => date('Y-m-d')
        ]);


        return back()->with('sukses', 'Kandang Berhasil Diselesaikan');
    }

    public function edit($id)
    {
        $data = [
            'd' => DB::table('kandang')->where('id_kandang', $id)->first(),
            'strain' => DB::table('strain')->get()
        ];
        return view('data_kandang.edit', $data);
    }

    public function update(Request $r)
    {
        DB::table('kandang')->where('id_kandang', $r->id_kandang)->update([
            'chick_in' => $r->tgl_lahir,
            'tgl_masuk' => $r->tgl_masuk,
            'nm_kandang' => $r->nm_kandang,
            'id_strain' => $r->strain,
            'stok_awal' => $r->ayam_awal,
            'chick_out' => $r->chick_out,
            'rupiah' => $r->rupiah
        ]);

        return redirect()->route('dashboard_kandang.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function delete(Request $r)
    {
        DB::table('kandang')->where('id_kandang', $r->id_kandang)->delete();
        return redirect()->route('data_kandang.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }
}
