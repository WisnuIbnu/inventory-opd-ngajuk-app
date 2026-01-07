<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenanggungJawab extends Model
{
    use HasFactory;
    use \App\Traits\FilterByDinas;

    protected $fillable = ['nama', 'dinas_id', 'jabatan'];

    protected $appends = ['nama_jabatan'];

    public function dinas(): BelongsTo 
    { 
        return $this->belongsTo(Dinas::class); 
    }

    public function barangs(): HasMany
    {
        return $this->hasMany(Barang::class, 'penanggung_jawab_id');
    }

     public function getNamaJabatanAttribute(): string
    {
        return "{$this->nama} - {$this->jabatan}";
    }
}
