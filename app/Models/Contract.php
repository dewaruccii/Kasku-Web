<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id', 'uuid');
    }
    public function List()
    {
        return $this->hasMany(ContractList::class, 'contract_id', 'uuid');
    }
}
