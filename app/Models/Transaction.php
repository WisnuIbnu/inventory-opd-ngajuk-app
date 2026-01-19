<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'barang_id',
        'bidang_id',
        'jumlah_pakai',
        'penerima',
        'keperluan',
        'tipe_transaksi',
        'created_by',
        'updated_by',
    ];


    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

}
