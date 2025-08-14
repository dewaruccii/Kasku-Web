<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jurnal extends Model
{
    use HasFactory;
    protected $casts = [
        'date' => 'datetime',
    ];
    public function Category()
    {
        return $this->belongsTo(JurnalCategory::class, 'jurnal_category_id', 'uuid');
    }
    public function Kurs()
    {
        return $this->belongsTo(Kur::class, 'kurs_id', 'uuid');
    }
    public function Attachments()
    {
        return $this->hasMany(JurnalAttachment::class, 'jurnal_id', 'uuid');
    }
    public function User()
    {
        return $this->belongsTo(User::class, 'users_id', 'uuid');
    }
    public function JurnalBalance()
    {
        return $this->hasOne(JurnalBalance::class,  'uuid', 'jurnal_balance_id');
    }
}
