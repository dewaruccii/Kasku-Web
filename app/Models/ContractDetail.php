<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractDetail extends Model
{
    use HasFactory;
    public function JurnalBalance()
    {
        return $this->belongsTo(JurnalBalance::class, 'jurnal_balance_id', 'uuid');
    }
}
