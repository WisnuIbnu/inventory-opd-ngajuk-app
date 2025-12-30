<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 20px; }
        .stiker-item {
            width: 45%;
            display: inline-block;
            border: 1px solid #000;
            padding: 10px;
            margin: 5px;
            vertical-align: top;
            page-break-inside: avoid;
        }
        .container { display: table; width: 100%; }
        .grid-qr { display: table-cell; width: 30%; vertical-align: middle; }
        .grid-info { display: table-cell; width: 70%; padding-left: 10px; font-size: 10px; }
    </style>
</head>
<body>
    @foreach($barangs as $barang)
        <div class="stiker-item">
            <div class="container">
                <div class="grid-qr">
                    <img src="{{ $barang->qr_base64 }}" width="70">
                </div>
                <div class="grid-info">
                    <div style="font-weight: bold; font-size: 12px;">{{ $barang->jenisBarang->nama_jenis ?? 'ASET' }}</div>
                    <strong>ID:</strong> {{ $barang->barcode }}<br>
                    <strong>Merk:</strong> {{ $barang->merk }}<br>
                    <strong>Lokasi:</strong> {{ $barang->gudang->nama_gudang ?? '-' }}
                </div>
            </div>
        </div>
    @endforeach
</body>
</html>