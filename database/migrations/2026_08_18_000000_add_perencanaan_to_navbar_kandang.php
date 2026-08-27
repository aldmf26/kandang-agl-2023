<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('navbar_kandang')) {
            return;
        }

        DB::table('navbar_kandang')->updateOrInsert(
            ['route' => 'dashboard_kandang.perencanaan'],
            [
                'urutan' => 3,
                'nama' => 'Perencanaan',
                'isi' => "['dashboard_kandang.perencanaan']",
            ]
        );
    }

    public function down(): void
    {
        if (Schema::hasTable('navbar_kandang')) {
            DB::table('navbar_kandang')
                ->where('route', 'dashboard_kandang.perencanaan')
                ->delete();
        }
    }
};
