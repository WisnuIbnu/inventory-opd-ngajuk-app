<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .sticker-box {
            width: 80%; 
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .header-table {
            width: 100%;
            border-bottom: 1px solid #000;
            border-collapse: collapse;
        }

        .logo-cell {
            width: 18mm;
            padding: 2mm;
            border-right: 1px solid #000;
            text-align: center;
            vertical-align: middle;
        }

        .logo-img {
            width: 15mm;
            display: block;
            margin: 0 auto;
        }

        .qr-img {
            width: 20mm;
        }

        .text-header-cell {
            text-align: center;
            vertical-align: middle;
            padding: 1mm;
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

        .content-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qr-cell {
            width: 30mm;
            padding: 3mm;
            text-align: center;
            vertical-align: middle;
            border-right: 1px solid #000;
        }

        .detail-cell {
            padding: 2mm 3mm;
            vertical-align: top;
        }

        .inner-detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .inner-detail-table td {
            font-size: 9pt;
            text-transform: uppercase;
            vertical-align: top;
            padding-bottom: 3px;
            line-height: 1.2;
        }

        .label-col {
            width: 28mm; 
            font-weight: normal;
        }

        .separator-col {
            width: 3mm;
            text-align: center;
        }

        .value-col {
            font-weight: bold;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <table class="sticker-box">
        <tr>
            <td>
                <table class="header-table">
                    <tr>
                        <td class="qr-cell">
                            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logoStiker/nganjuk.png'))) }}" class="logo-img">
                        </td>
                        <td class="text-header-cell">
                            <div class="title-kab">PEMERINTAH KABUPATEN NGANJUK</div>
                            <div class="title-dinas">{{ $barang->dinas->nama_opd ?? 'DINAS KOMUNIKASI DAN INFORMATIKA' }}</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <td>
                <table class="content-table">
                    <tr>
                        <td class="qr-cell">
                            <img src="{{ $qrCode }}" class="qr-img">
                        </td>
                        <td class="detail-cell">
                            <table class="inner-detail-table">
                                <tr>
                                    <td class="label-col">Kode Barang</td>
                                    <td class="separator-col">:</td>
                                    <td class="value-col">{{ $barang->barcode }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Gudang</td>
                                    <td class="separator-col">:</td>
                                    <td class="value-col">{{ $barang->gudang->nama_gudang ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Tahun</td>
                                    <td class="separator-col">:</td>
                                    <td class="value-col"> {{ \Carbon\Carbon::parse($barang->tahun)->translatedFormat('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Nama Barang</td>
                                    <td class="separator-col">:</td>
                                    <td class="value-col">{{ $barang->merk }}</td>
                                </tr>
                                <tr>
                                    <td class="label-col">Harga</td>
                                    <td class="separator-col">:</td>
                                    <td class="value-col">Hasil Dari Pembelian / Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>