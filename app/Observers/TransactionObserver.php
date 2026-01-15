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

        if ($barang && $barang->jenis_aset === 'habis pakai') {
            if ($transaction->tipe_transaksi === 'masuk') {
                // Jika stok masuk: tambah kuota dan sisa stok
                $barang->increment('total_quota', $transaction->jumlah_pakai);
                $barang->increment('stock_remaining', $transaction->jumlah_pakai);
            } else {
                // Jika stok keluar: tambah pemakaian dan kurangi sisa stok
                $barang->increment('total_use', $transaction->jumlah_pakai);
                $barang->decrement('stock_remaining', $transaction->jumlah_pakai);
            }
        }
    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        $barang = $transaction->barang;

        if ($barang && $barang->jenis_aset === 'habis pakai') {
            
            $oldJumlah = $transaction->getOriginal('jumlah_pakai');
            $oldTipe = $transaction->getOriginal('tipe_transaksi');

            $newJumlah = $transaction->jumlah_pakai;
            $newTipe = $transaction->tipe_transaksi;

            // --- LANGKAH A: BATALKAN EFEK DATA LAMA ---
            if ($oldTipe === 'masuk') {
                $barang->total_quota -= $oldJumlah;
                $barang->stock_remaining -= $oldJumlah;
            } else {
                $barang->total_use -= $oldJumlah;
                $barang->stock_remaining += $oldJumlah;
            }

            // --- LANGKAH B: TERAPKAN EFEK DATA BARU ---
            if ($newTipe === 'masuk') {
                $barang->total_quota += $newJumlah;
                $barang->stock_remaining += $newJumlah;
            } else {
                $barang->total_use += $newJumlah;
                $barang->stock_remaining -= $newJumlah;
            }

            $barang->save();
        }
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        $barang = $transaction->barang;

        if ($barang && $barang->jenis_aset === 'habis pakai') {
            if ($transaction->tipe_transaksi === 'masuk') {
                $barang->decrement('total_quota', $transaction->jumlah_pakai);
                $barang->decrement('stock_remaining', $transaction->jumlah_pakai);
            } else {
                $barang->decrement('total_use', $transaction->jumlah_pakai);
                $barang->increment('stock_remaining', $transaction->jumlah_pakai);
            }
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
