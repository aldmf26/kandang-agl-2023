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
        return view('data_kandang.data_kandang',$data);
    }
    
    public function store(Request $r)
    {
        DB::table('kandang')->insert([
            'chick_in' => $r->tgl,
            'tgl_masuk' => $r->tgl,
            'nm_kandang' => $r->nm_kandang,
            'id_strain' => $r->strain,
            'stok_awal' => $r->ayam_awal,
            'admin' => auth()->user()->name
        ]);

        return redirect()->route($r->route ?? 'data_kandang.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'd' => DB::table('kandang')->where('id_kandang', $id)->first()
        ];
        return view('data_kandang.edit',$data);
    }

    public function update(Request $r)
    {
        DB::table('kandang')->where('id_kandang', $r->id_kandang)->update([
            'chick_in' => $r->tgl,
            'nm_kandang' => $r->nm_kandang,
            'strain' => $r->strain,
            'stok_awal' => $r->ayam_awal,
            'admin' => auth()->user()->name
        ]);

        return redirect()->route('data_kandang.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }

    public function delete(Request $r)
    {
        DB::table('kandang')->where('id_kandang', $r->id_kandang)->delete();
        return redirect()->route('data_kandang.index')->with('sukses', 'Data Berhasil Ditambahkan');
    }
}
