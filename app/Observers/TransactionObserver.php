<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $barang = $transaction->barang;

        if ($barang->kategori_pakai === 'habis pakai') {

            $barang->total_use += $transaction->jumlah_pakai;
            
            $barang->stock_remaining = $barang->total_quota - $barang->total_use;
            
            $barang->save();
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $barang = $transaction->barang;

        if ($barang && $barang->kategori_pakai === 'habis pakai') {
            
            $selisih = $transaction->jumlah_pakai - $transaction->getOriginal('jumlah_pakai');

            $barang->total_use += $selisih;
            $barang->stock_remaining = $barang->total_quota - $barang->total_use;
            $barang->save();
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $barang = $transaction->barang;

        if ($barang && $barang->kategori_pakai === 'habis pakai') {
            $barang->total_use -= $transaction->jumlah_pakai;

            $barang->stock_remaining = $barang->total_quota - $barang->total_use;
            $barang->save();
        }
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
