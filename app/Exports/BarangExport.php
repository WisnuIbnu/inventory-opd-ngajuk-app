<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class BarangExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Barang::query()->with(['dinas', 'gudang', 'jenisBarang']);

        $role = auth()->user()->role;
        $sessionDinasId = session('admin_dinas_id');
        $userDinasId = auth()->user()->dinas_id;

        if ($role === 'OPD') {
            $query->where('dinas_id', $userDinasId);
        } elseif ($role === 'Admin' && $sessionDinasId) {
            $query->where('dinas_id', $sessionDinasId);
        }

        if (!empty($this->filters['kategori_pakai']) && $this->filters['kategori_pakai'] !== 'semua') {
            $query->where('kategori_pakai', $this->filters['kategori_pakai']);
        }

        $kategori = $this->filters['kategori'];
        $excludedKategori = ['semua', 'gudang', 'jenis_aset'];

        if (!in_array($kategori, $excludedKategori)) {
            $kondisi = ($kategori === 'tidak_digunakan') ? 'tidak digunakan' : $kategori;
            $query->where('kondisi', $kondisi);
        }

        if ($kategori === 'jenis_aset' && !empty($this->filters['jenis_aset'])) {
            $query->where('jenis_aset', $this->filters['jenis_aset']);
        }

        if ($kategori === 'gudang' && !empty($this->filters['gudang_id'])) {
            $query->where('gudang_id', $this->filters['gudang_id']);
        }

        if ($this->filters['rentang'] === 'per_bulan' && !empty($this->filters['bulan'])) {
            $date = strtotime($this->filters['bulan']);
            $query->whereMonth('tahun', date('m', $date))
                  ->whereYear('tahun', date('Y', $date));
        } elseif ($this->filters['rentang'] === 'per_tahun' && !empty($this->filters['tahun'])) {
            $query->whereYear('tahun', $this->filters['tahun']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Kode Barang', 
            'Nama Barang', 
            'Merk', 
            'Kondisi', 
            'Lokasi Gudang', 
            'Dinas/OPD', 
            'Harga', 
            'Jenis Aset', 
            'Tahun Perolehan'
        ];
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
            date('Y', strtotime($barang->tahun)), 
        ];
    }
    
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}