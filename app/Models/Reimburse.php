<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimburse extends Model
{
    use HasFactory;
    public function JurnalBalance()
    {
        return $this->belongsTo(JurnalBalance::class, 'jurnal_id', 'uuid');
    }
    public function Jurnal()
    {
        return $this->hasOne(Jurnal::class, 'uuid', 'jurnal_reference');
    }
}
