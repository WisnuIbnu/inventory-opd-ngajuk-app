<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gudang extends Model
{
    use HasFactory;
    use \App\Traits\FilterByDinas;
    
    protected $fillable = ['nama_gudang', 'dinas_id'];

    public function dinas(): BelongsTo 
    { 
        return $this->belongsTo(Dinas::class); 
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'gudang_id');
    }
}
