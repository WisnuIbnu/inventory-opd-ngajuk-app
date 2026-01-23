<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filter;

    public function __construct($filter)
    {
        $this->filter = $filter;
    }


    public function query()
    {
        $query = Transaction::query()->with(['barang.dinas', 'creator', 'bidang']);

        // 1. Filter Keamanan (Hanya data milik dinas terkait)
        $role = auth()->user()->role;
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        if ($role === 'OPD') {
            $query->whereHas('barang', fn($q) => $q->where('dinas_id', $userDinasId));
        } elseif ($role === 'Admin' && $sessionDinasId) {
            $query->whereHas('barang', fn($q) => $q->where('dinas_id', $sessionDinasId));
        }


        if (isset($this->filter['rentang'])) {
            if ($this->filter['rentang'] === 'per_tahun') {
                $query->whereYear('created_at', $this->filter['tahun']);
            } elseif ($this->filter['rentang'] === 'per_bulan') {
                $query->whereYear('created_at', $this->filter['tahun'])
                      ->whereMonth('created_at', $this->filter['bulan']);
            }
        }

        return $query->latest();
    }

    /**
     * Judul Kolom di Excel
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal & Waktu',
            'Jenis Transaksi',
            'Kode Barang',
            'Nama Barang/Merk',
            'Jumlah Keluar/Masuk',
            'Penerima/pegawai',
            'Keperluan',
            'Dinas / OPD',
            'Bidang',
            'Petugas Input'
        ];
    }

    /**
     * Pemetaan data ke kolom Excel
     * * @var Transaction $transaction
     */
    private $rowNumber = 0;
    public function map($transaction): array
    {
        return [
            ++$this->rowNumber,
            $transaction->created_at->format('d/m/Y H:i'),
            ucfirst($transaction->tipe_transaksi),
            $transaction->barang->barcode ?? '-',
            $transaction->barang->merk ?? '-',
            $transaction->jumlah_pakai . ' Unit',
            $transaction->penerima,
            $transaction->keperluan,
            $transaction->barang->dinas->nama_opd ?? '-',
            $transaction->bidang->nama_bidang ?? '-',
            $transaction->creator->name ?? 'Sistem',
        ];
    }

    /**
     * Memberikan styling dasar pada header Excel
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}