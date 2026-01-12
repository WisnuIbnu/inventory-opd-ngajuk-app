<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

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

        // 1. Logika Filter Dinas (Tetap)
        $role = auth()->user()->role;
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        if ($role === 'OPD') {
            $query->where('dinas_id', $userDinasId);
        } elseif ($role === 'Admin' && $sessionDinasId) {
            $query->where('dinas_id', $sessionDinasId);
        }

        // 2. Filter Kategori & JENIS ASET (Tambahan)
        if ($this->filters['kategori'] === 'rusak') {
            $query->where('kondisi', 'rusak');
        } elseif ($this->filters['kategori'] === 'tidak_digunakan') {
            $query->where('kondisi', 'tidak digunakan');
        } elseif ($this->filters['kategori'] === 'jenis_aset' && !empty($this->filters['jenis_aset'])) {
            // Ini akan memfilter kolom jenis_aset
            $query->where('jenis_aset', $this->filters['jenis_aset']);
        }

        // 3. Filter Gudang (Tetap)
        if (!empty($this->filters['gudang_id'])) {
            $query->where('gudang_id', $this->filters['gudang_id']);
        }

        // 4. Filter Waktu (Kombinasi Otomatis)
        // Logika ini akan tetap jalan meskipun kategori yang dipilih adalah 'jenis_aset'
        if ($this->filters['rentang'] === 'per_bulan' && !empty($this->filters['bulan'])) {
            $query->whereMonth('tahun', date('m', strtotime($this->filters['bulan'])))
                ->whereYear('tahun', date('Y', strtotime($this->filters['bulan'])));
        } elseif ($this->filters['rentang'] === 'per_tahun' && !empty($this->filters['tahun'])) {
            $query->whereYear('tahun', $this->filters['tahun']);
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Barcode', 'Nama Barang', 'Merk', 'Kondisi', 'Lokasi Gudang', 'Dinas/OPD', 'Harga', 'Jenis Aset', 'Tanggal Masuk'];
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
            $barang->jenis_aset,
            $barang->created_at->format('d-m-Y'),
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}