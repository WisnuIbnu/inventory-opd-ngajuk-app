<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BarangExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Barang::query()->with(['dinas', 'gudang', 'jenisBarang']);

        // 1. Logika Filter Dinas (Admin Switcher vs OPD ID)
        $role = auth()->user()->role;
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        if ($role === 'OPD') {
            $query->where('dinas_id', $userDinasId);
        } elseif ($role === 'Admin' && $sessionDinasId) {
            $query->where('dinas_id', $sessionDinasId);
        }

        // 2. Filter Tipe Barang (Rusak, Tidak Digunakan, dll)
        if ($this->filters['kategori'] === 'rusak') {
            $query->where('kondisi', 'rusak');
        } elseif ($this->filters['kategori'] === 'tidak_digunakan') {
            $query->where('kondisi', 'tidak digunakan');
        }

        // 3. Filter Gudang
        if (!empty($this->filters['gudang_id'])) {
            $query->where('gudang_id', $this->filters['gudang_id']);
        }

        // 4. Filter Waktu
        if ($this->filters['rentang'] === 'per_bulan' && !empty($this->filters['bulan'])) {
            $query->whereMonth('created_at', date('m', strtotime($this->filters['bulan'])))
                  ->whereYear('created_at', date('Y', strtotime($this->filters['bulan'])));
        } elseif ($this->filters['rentang'] === 'per_tahun' && !empty($this->filters['tahun'])) {
            $query->whereYear('created_at', $this->filters['tahun']);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Barcode', 'Nama Barang', 'Merk', 'Kondisi', 'Lokasi Gudang', 'Dinas/OPD', 'Harga', 'Tanggal Masuk'];
    }

    public function map($barang): array
    {
        return [
            $barang->barcode,
            $barang->jenisBarang->nama_jenis ?? '-',
            $barang->merk,
            ucfirst($barang->kondisi),
            $barang->gudang->nama_gudang ?? '-',
            $barang->dinas->nama_opd ?? '-',
            number_format($barang->harga, 0, ',', '.'),
            $barang->created_at->format('d-m-Y'),
        ];
    }
}