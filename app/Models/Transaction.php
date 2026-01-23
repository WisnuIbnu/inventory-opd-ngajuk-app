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
        'gambar_bukti',
        'keperluan',
        'tipe_transaksi',
        'created_by',
        'updated_by',
    ];

    protected static function booted()
    {
        static::saving(function ($model) {
            if (!$model->gambar_bukti || $model->gambar_bukti === "") {
                $model->gambar_bukti = 'transaksi-bukti/transaction-default.png';
            }
        });
    }


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
