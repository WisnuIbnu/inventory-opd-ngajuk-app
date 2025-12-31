<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan | SIMITA</title>
    
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body { font-family: 'Figtree', sans-serif; }
        .bg-filament { background-color: #f59e0b; }
        .text-filament { color: #f59e0b; }
        .bg-dots { background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.05)'/%3E%3C/svg%3E"); }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 bg-dots">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-lg w-full text-center">
            <div class="mb-8 inline-flex items-center justify-center w-24 h-24 bg-orange-100 rounded-3xl text-filament shadow-xl shadow-orange-500/10">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                </svg>
            </div>

            <h1 class="text-9xl font-extrabold text-gray-900 mb-2">404</h1>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Halaman Tidak Ditemukan</h2>
            <p class="text-gray-600 mb-10 leading-relaxed">
                Maaf, halaman atau data aset yang Anda cari tidak tersedia atau telah dipindahkan. Pastikan URL sudah benar atau kembali ke Dashboard.
            </p>

            <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                <a href="{{ url('/') }}" class="w-full sm:w-auto px-8 py-3.5 bg-gray-900 text-white rounded-2xl font-bold hover:bg-gray-800 transition shadow-lg">
                    Kembali ke Beranda
                </a>
                <a href="{{ url('/admin/katalog-barang') }}" class="w-full sm:w-auto px-8 py-3.5 bg-white text-gray-900 border border-gray-200 rounded-2xl font-bold hover:bg-gray-50 transition shadow-sm">
                    Cek Katalog Barang
                </a>
            </div>

            <div class="mt-16 flex items-center justify-center space-x-2 opacity-50">
                <div class="w-5 h-5 bg-filament rounded flex items-center justify-center">
                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-xs font-bold tracking-widest uppercase">SIMITA NGANJUK</span>
            </div>
        </div>
    </div>

</body>
</html>