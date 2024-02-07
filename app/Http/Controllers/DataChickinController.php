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
            'kandang' => DB::table('kandang as a')->join('strain as b', 'a.id_strain', 'b.id_strain')->get()
        ];

        return view('datachickin.index', $data);
    }
}
