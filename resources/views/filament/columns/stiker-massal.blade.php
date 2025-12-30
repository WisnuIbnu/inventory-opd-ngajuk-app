<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
        }

        /* ===== CONTAINER STIKER ===== */
        .stiker-item {
            width: 80%;
            display: inline-block;
            vertical-align: top;
            margin: 5px 1%;
            border: 1px solid #000;
            page-break-inside: avoid;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* ===== HEADER ===== */
        .header-table {
            border-bottom: 1px solid #000;
        }

        .logo-cell {
            width: 30mm;
            padding: 2mm;
            border-right: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .qr-cell {
            width: 30mm;
            padding: 2mm;
            border-right: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .logo-img {
            width: 15mm;
        }

        .text-header-cell {
            text-align: center;
            padding: 2mm;
        }

        .title-kab {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
        }

        .title-dinas {
            font-size: 12pt;
            text-transform: uppercase;
            margin-top: 2px;
        }

        /* ===== CONTENT ===== */

        .qr-img {
            width: 25mm;
        }

        .detail-cell {
            padding: 3mm;
        }

        .detail-table td {
            font-size: 8.5pt;
            text-transform: uppercase;
            padding-bottom: 3px;
            line-height: 1.2;
        }

        .label {
            width: 28mm;
        }

        .separator {
            width: 3mm;
            text-align: center;
        }

        .value {
            font-weight: bold;
            word-break: break-word;
        }
    </style>
</head>
<body>

@foreach($barangs as $barang)
    <div class="stiker-item">
        <table>
            <!-- ===== HEADER ===== -->
            <tr>
                <td>
                    <table class="header-table">
                        <tr>
                            <td class="logo-cell">
                                <img
                                    src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logoStiker/nganjuk.png'))) }}"
                                    class="logo-img"
                                >
                            </td>
                            <td class="text-header-cell">
                                <div class="title-kab">
                                    PEMERINTAH KABUPATEN NGANJUK
                                </div>
                                <div class="title-dinas">
                                    {{ $barang->dinas->nama_opd ?? 'DINAS KOMUNIKASI DAN INFORMATIKA' }}
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <tr>
                <td>
                    <table>
                        <tr>
                            <td class="qr-cell">
                                <img src="{{ $barang->qr_base64 }}" class="qr-img">
                            </td>
                            <td class="detail-cell">
                                <table class="detail-table">
                                    <tr>
                                        <td class="label">Kode Barang</td>
                                        <td class="separator">:</td>
                                        <td class="value">{{ $barang->barcode }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Gudang</td>
                                        <td class="separator">:</td>
                                        <td class="value">{{ $barang->gudang->nama_gudang ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Tahun</td>
                                        <td class="separator">:</td>
                                        <td class="value"> {{ \Carbon\Carbon::parse($barang->tahun)->translatedFormat('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Nama Barang</td>
                                        <td class="separator">:</td>
                                        <td class="value">{{ $barang->merk }}</td>
                                    </tr>
                                    <tr>
                                        <td class="label">Harga</td>
                                        <td class="separator">:</td>
                                        <td class="value">
                                            Hasil Dari Pembelian / Rp {{ number_format($barang->harga, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endforeach

</body>
</html>
