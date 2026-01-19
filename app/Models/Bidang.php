<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_bidang',
        'dinas_id',
    ];

    public function dinas()
    {
        return $this->belongsTo(Dinas::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
