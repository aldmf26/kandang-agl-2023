<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataChickinController extends Controller
{
    public function index(Request $r)
    {
        $data = [
            'title' => 'Data chick in/out',
            'kandang' => DB::select("SELECT a.chick_in, a.nm_kandang, a.stok_awal, b.mati, b.jual, b.afkir, a.selesai
            FROM kandang as a 
            left join (
                SELECT b.id_kandang, sum(b.mati) as mati, sum(b.jual) as jual, sum(b.afkir) as afkir
                FROM populasi as b
                group by b.id_kandang
            ) as b on b.id_kandang = a.id_kandang
            order by a.nm_kandang ASC
            ")
        ];

        return view('datachickin.index', $data);
    }
}
