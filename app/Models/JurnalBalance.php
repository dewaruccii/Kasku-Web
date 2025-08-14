<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalBalance extends Model
{
    use HasFactory;
    public function Kurs()
    {
        return $this->belongsTo(Kur::class, 'kurs_id', 'uuid');
    }
}
