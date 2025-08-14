<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractList extends Model
{
    use HasFactory;
    public function Detail()
    {
        return $this->hasMany(ContractDetail::class, 'contract_list_id', 'uuid');
    }
}
