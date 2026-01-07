<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;


class Barang extends Model
{
    use HasFactory;
    use \App\Traits\FilterByDinas;

    protected $fillable = [
        'jenis_barang_id',
        'merk',
        'register',
        'gambar',
        'tahun',
        'barcode',
        'penanggung_jawab_id',
        'harga',
        'gudang_id',
        'dinas_id',
        'kondisi',
        'keterangan',
        'jenis_aset',
        'created_by',
        'updated_by',
        'created_at',
        'update_at',
    ];

    protected $casts = [
        'tahun' => 'date',
        'harga' => 'decimal:2',
    ];


    public function getRouteKeyName(): string
    {
        return 'barcode';
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function jenisBarang(): BelongsTo 
    { 
        return $this->belongsTo(JenisBarang::class); 
    }

    public function penanggungJawab(): BelongsTo 
    { 
        return $this->belongsTo(PenanggungJawab::class); 
    }

    public function gudang(): BelongsTo 
    { 
        return $this->belongsTo(Gudang::class);
    }

    public function dinas(): BelongsTo 
    { 
        return $this->belongsTo(Dinas::class); 
    }
}
