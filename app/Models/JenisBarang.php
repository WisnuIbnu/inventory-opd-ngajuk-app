<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JenisBarang extends Model
{
    use HasFactory;
    use \App\Traits\FilterByDinas;

    protected $fillable = ['nama_jenis', 'dinas_id'];

    public function dinas(): BelongsTo { return $this->belongsTo(Dinas::class); }
}
