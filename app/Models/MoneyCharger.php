<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoneyCharger extends Model
{
    use HasFactory;
    public function From()
    {
        return $this->belongsTo(JurnalBalance::class, 'from', 'uuid');
    }
    public function To()
    {
        return $this->belongsTo(JurnalBalance::class, 'to', 'uuid');
    }
    public function JurnalReference()
    {
        $this->hasOne(Jurnal::class, 'uuid', 'from');
    }
}
