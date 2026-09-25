<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AkunPerkiraan extends Model
{
    use HasFactory;

    protected $table = 'akun_perkiraan';

    protected $primaryKey = 'id_akun_perkiraan';

    protected $guarded = [];
}
