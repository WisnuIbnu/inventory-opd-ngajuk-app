<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIMITA | Manajemen Inventaris Daerah</title>

    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,600,700,800&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Figtree', sans-serif; scroll-behavior: smooth; }
        .bg-filament { background-color: #f59e0b; }
        .text-filament { color: #f59e0b; }
        .border-filament { border-color: #f59e0b; }

        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-100%); }
        }
        @keyframes marquee2 {
            0% { transform: translateX(100%); }
            100% { transform: translateX(0%); }
        }
        .animate-marquee { animation: marquee 90s linear infinite; }
        .animate-marquee2 { animation: marquee2 90s linear infinite; }
        .bg-dots { background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.05)'/%3E%3C/svg%3E"); }
    </style>
</head>
<body class="antialiased bg-gray-50 text-gray-900 selection:bg-orange-500 selection:text-white">

    <nav class="fixed w-full z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-filament rounded-xl flex items-center justify-center shadow-lg shadow-orange-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <span class="text-2xl font-extrabold tracking-tight text-gray-900">SIMITA<span class="text-filament">.</span></span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#fitur" class="text-sm font-semibold hover:text-filament transition">Fitur</a>
                    <a href="#statistik" class="text-sm font-semibold hover:text-filament transition">Statistik</a>
                    <a href="{{ url('/admin/katalog-barang') }}" class="text-sm font-semibold hover:text-filament transition">Katalog</a>
                    @auth
                        <a href="{{ url('/admin') }}" class="bg-filament text-white px-6 py-2.5 rounded-full font-bold shadow-lg shadow-orange-500/30 hover:scale-105 transition transform">Dashboard</a>
                    @else
                        <a href="{{ route('filament.admin.auth.login') }}" class="bg-filament text-white px-6 py-2.5 rounded-full font-bold shadow-lg shadow-orange-500/30 hover:scale-105 transition transform">Log In</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-dots">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6">
                    Kelola Aset Daerah <br>
                    <span class="text-filament">Tanpa Ribet.</span>
                </h1>
                <p class="max-w-2xl mx-auto text-lg text-gray-600 mb-10 leading-relaxed">
                    Sistem Informasi Manajemen Inventaris Terpadu (SIMITA) untuk OPD Kab. Nganjuk. Pantau keberadaan, kondisi, dan nilai aset secara akurat dalam satu genggaman.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center space-y-4 sm:space-y-0 sm:space-x-4">
                    <a href="{{ route('filament.admin.auth.login') }}" class="w-full sm:w-auto px-8 py-4 bg-gray-900 text-white rounded-2xl font-bold hover:bg-gray-800 transition shadow-xl">
                        Mulai Sekarang
                    </a>
                    <a href="{{ url('/admin/katalog-barang') }}" class="w-full sm:w-auto px-8 py-4 bg-white text-gray-900 border border-gray-200 rounded-2xl font-bold hover:bg-gray-50 transition shadow-sm">
                        Lihat Katalog Aset
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section id="statistik" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($totalAset, 0, ',', '.') }}++</div>
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-widest">Total Aset</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900 mb-1">{{ $totalDinas }}</div>
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-widest">Unit Kerja OPD</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900 mb-1">100%</div>
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-widest">Transparan</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-gray-900 mb-1">Realtime</div>
                    <div class="text-sm text-gray-500 font-medium uppercase tracking-widest">Monitoring</div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="text-3xl font-bold mb-4">Fitur Unggulan</h2>
                <div class="w-20 h-1.5 bg-filament mx-auto rounded-full"></div>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center text-filament mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Sistem QR Code</h3>
                    <p class="text-gray-500 leading-relaxed">Labeli setiap aset dengan QR Code unik. Cukup scan untuk melihat informasi detail barang secara instan.</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center text-filament mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Laporan Kondisi</h3>
                    <p class="text-gray-500 leading-relaxed">Pantau barang yang baik, rusak, atau perlu perbaikan dengan dashboard statistik yang mudah dipahami.</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 hover:shadow-xl transition duration-300">
                    <div class="w-12 h-12 bg-orange-100 rounded-2xl flex items-center justify-center text-filament mb-6">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Pelacakan Lokasi</h3>
                    <p class="text-gray-500 leading-relaxed">Ketahui tepat di gudang mana atau di ruangan mana aset Anda berada tanpa harus mencari secara manual.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="alur" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-16 text-center">
                <h2 class="text-3xl font-bold mb-4">Cara Kerja Sistem</h2>
                <p class="text-gray-500">Proses digitalisasi aset yang sederhana dan efisien.</p>
            </div>
            <div class="relative">
                <div class="hidden md:block absolute top-1/2 left-0 w-full h-0.5 bg-orange-100 -translate-y-1/2"></div>
                
                <div class="grid md:grid-cols-4 gap-8 relative z-10">
                    <div class="bg-white p-6 text-center">
                        <div class="w-12 h-12 bg-filament text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl shadow-lg shadow-orange-500/40">1</div>
                        <h4 class="font-bold mb-2">Input Data</h4>
                        <p class="text-sm text-gray-500">Admin OPD memasukkan detail aset ke sistem.</p>
                    </div>
                    <div class="bg-white p-6 text-center">
                        <div class="w-12 h-12 bg-filament text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl shadow-lg shadow-orange-500/40">2</div>
                        <h4 class="font-bold mb-2">Generate QR</h4>
                        <p class="text-sm text-gray-500">Sistem otomatis membuat label QR Code unik.</p>
                    </div>
                    <div class="bg-white p-6 text-center">
                        <div class="w-12 h-12 bg-filament text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl shadow-lg shadow-orange-500/40">3</div>
                        <h4 class="font-bold mb-2">Labeling</h4>
                        <p class="text-sm text-gray-500">Cetak dan tempel stiker QR pada fisik barang.</p>
                    </div>
                    <div class="bg-white p-6 text-center">
                        <div class="w-12 h-12 bg-filament text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold text-xl shadow-lg shadow-orange-500/40">4</div>
                        <h4 class="font-bold mb-2">Monitoring</h4>
                        <p class="text-sm text-gray-500">Pantau kondisi aset kapan saja lewat dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-gray-50" x-data="{ active: 1 }" x-cloak>
        <div class="max-w-3xl mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold mb-4">Pertanyaan Umum</h2>
                <div class="w-20 h-1.5 bg-filament mx-auto rounded-full"></div>
            </div>
            
            <div class="space-y-4">
                <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                    <button @click="active = active === 1 ? 0 : 1" class="w-full flex justify-between items-center p-6 bg-white text-left font-bold text-gray-900 focus:outline-none">
                        <span>Siapa yang bisa mengakses sistem ini?</span>
                        <svg class="w-5 h-5 transition-transform duration-300" :class="active === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="active === 1" x-collapse class="px-6 pb-6 text-gray-600 text-sm leading-relaxed">
                        Hanya akun resmi Admin OPD yang terdaftar melalui Dinas Komunikasi dan Informatika yang dapat mengakses dashboard manajemen aset sesuai dengan wewenang instansi masing-masing.
                    </div>
                </div>

                <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                    <button @click="active = active === 2 ? 0 : 2" class="w-full flex justify-between items-center p-6 bg-white text-left font-bold text-gray-900 focus:outline-none">
                        <span>Apakah data aset aman jika server down?</span>
                        <svg class="w-5 h-5 transition-transform duration-300" :class="active === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="active === 2" x-collapse class="px-6 pb-6 text-gray-600 text-sm leading-relaxed">
                        Sistem kami menggunakan backup harian terotomatisasi serta infrastruktur server yang aman untuk memastikan data aset daerah Anda selalu tersedia dan terlindungi dari kehilangan data.
                    </div>
                </div>

                <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                    <button @click="active = active === 3 ? 0 : 3" class="w-full flex justify-between items-center p-6 bg-white text-left font-bold text-gray-900 focus:outline-none">
                        <span>Bagaimana jika stiker QR Code rusak?</span>
                        <svg class="w-5 h-5 transition-transform duration-300" :class="active === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="active === 3" x-collapse class="px-6 pb-6 text-gray-600 text-sm leading-relaxed">
                        Anda dapat mencetak ulang label QR Code kapan saja melalui dashboard admin tanpa mengubah data aset. Sangat disarankan untuk menggunakan stiker berbahan vinyl atau laminasi agar lebih tahan lama.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-filament">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-4xl font-extrabold text-white mb-6">Siap Digitalisasi Aset Anda?</h2>
            <p class="text-orange-100 text-lg mb-10">Gabung dengan puluhan unit kerja lainnya yang telah beralih ke manajemen aset digital yang modern.</p>
            <a href="{{ route('filament.admin.auth.login') }}" class="inline-block bg-white text-filament px-10 py-4 rounded-2xl font-bold text-lg hover:bg-gray-50 transition shadow-2xl">
                Login Dashboard Admin
            </a>
        </div>
    </section>

    @php
    $dinasList = [
        'Dinas Pendidikan', 'Dinas Kesehatan', 'Dinas Pekerjaan Umum dan Penataan Ruang',
        'Dinas Perumahan dan Kawasan Permukiman', 'Dinas Sosial', 'Dinas Tenaga Kerja',
        'Dinas Lingkungan Hidup', 'Dinas Kependudukan dan Pencatatan Sipil',
        'Dinas Perhubungan', 'Dinas Komunikasi dan Informatika', 'Dinas Koperasi dan Usaha Mikro',
        'Dinas Perindustrian', 'Dinas Perdagangan', 'Dinas Pertanian',
        'Dinas Ketahanan Pangan dan Perikanan', 'Dinas Pariwisata', 'Dinas Pemuda dan Olahraga',
        'Dinas Kebudayaan','RSUD Kabupaten Nganjuk', 'RSUD Kertosono'
    ];
    @endphp

    <section class="py-16 bg-gray-50 border-y border-gray-100 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <p class="text-center text-sm font-semibold text-gray-400 uppercase tracking-widest mb-10">Unit Kerja Terintegrasi</p>
        </div>
        
        <div class="relative flex overflow-x-hidden">
            <div class="py-4 animate-marquee whitespace-nowrap flex items-center gap-16">
                @foreach($dinasList as $dinas)
                <span class="text-xl font-bold text-gray-400 uppercase tracking-wide">{{ $dinas }}</span>
            @endforeach
            </div>

            <div class="absolute top-0 py-4 animate-marquee2 whitespace-nowrap flex items-center gap-16">
                @foreach($dinasList as $dinas)
                <span class="text-xl font-bold text-gray-400 uppercase tracking-wide">{{ $dinas }}</span>
            @endforeach
            </div>
        </div>
    </section>

    <footer class="bg-white py-12 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center space-x-3 mb-6">
                <div class="w-8 h-8 bg-filament rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-tight">SIMITA NGANJUK </span>
            </div>
            <p class="text-gray-500 text-sm italic mb-4">"Membangun Transparansi Aset Daerah melalui Teknologi"</p>
            <p class="text-gray-400 text-xs uppercase tracking-widest font-bold">
                &copy; {{ date('Y') }} PEMERINTAH DAERAH . DINAS KOMINFO . ALL RIGHTS RESERVED
            </p>
        </div>
    </footer>

</body>
</html>