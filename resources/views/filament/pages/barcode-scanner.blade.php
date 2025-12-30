<div x-data="{
    scanner: null,
    showScanner: false,
    init() {
        // Inisialisasi tanpa render otomatis agar lebih terkontrol
        this.scanner = new Html5Qrcode('reader');
    },
    async startScan() {
        this.showScanner = true;
        
        const config = { 
            fps: 30, // Menaikkan frame rate ke 30fps untuk kecepatan deteksi maksimal
            qrbox: { width: 220, height: 220 }, // Fokus area lebih kecil agar proses decoding lebih ringan
            aspectRatio: 1.0
        };

        try {
            // Menggunakan kamera belakang secara otomatis
            await this.scanner.start(
                { facingMode: 'environment' }, 
                config, 
                (decodedText) => {
                    // Berikan feedback getaran singkat (hanya di HP)
                    if (navigator.vibrate) navigator.vibrate(100);

                    if (decodedText.startsWith('http')) {
                        window.location.href = decodedText;
                    } else {
                        // Redirect instan ke detail barang
                        window.location.href = `/admin/katalog-barang/${decodedText}`;
                    }
                    this.stopScan();
                }
            );
        } catch (err) {
            console.error('Gagal akses kamera: ', err);
            this.showScanner = false;
        }
    },
    async stopScan() {
        if (this.scanner && this.scanner.isScanning) {
            await this.scanner.stop();
        }
        this.showScanner = false;
    }
}" class="flex mb-4">
    <script src="https://unpkg.com/html5-qrcode/html5-qrcode.min.js"></script>
    
    <x-filament::button  
        icon="heroicon-m-qr-code" 
        size="lg"
        x-on:click="startScan()"
        x-show="!showScanner">
        Scan QR Barang
    </x-filament::button>

    <div x-show="showScanner" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         x-transition
         style="display: none;">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl max-w-sm w-full shadow-2xl gap-y-6">
            <div id="reader" class="overflow-hidden rounded-xl bg-gray-100"></div>
            
            <x-filament::button color="danger" x-on:click="stopScan()" class="mt- w-full">
                Tutup Kamera
            </x-filament::button>
        </div>
    </div>
</div>