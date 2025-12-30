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
    ];

    protected $casts = [
        'tahun' => 'date',
        'harga' => 'decimal:2',
    ];

    protected static function booted()
    {

        static::creating(function ($barang) {
            if (empty($barang->barcode)) {
                $barang->barcode = 'QR-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'barcode';
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
