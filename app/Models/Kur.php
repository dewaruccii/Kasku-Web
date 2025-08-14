<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kur extends Model
{
    use HasFactory;
    public function KursExhcange()
    {
        return $this->hasOne(KurExchange::class,  'kurs_id', 'uuid');
    }
}
