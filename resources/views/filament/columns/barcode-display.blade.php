<div x-data="{
    scanner: null,
    showScanner: false,
    init() {
        this.scanner = new Html5QrcodeScanner('reader', { 
            fps: 15, 
            qrbox: {width: 250, height: 250},
            aspectRatio: 1.0
        });
    },
    startScan() {
        this.showScanner = true;
        this.scanner.render((decodedText) => {
            // Jika hasil scan adalah URL (Akses langsung ke detail)
            if (decodedText.startsWith('http')) {
                window.location.href = decodedText;
            } else {
                // Jika hasil scan adalah ID Barcode (Filter katalog)
                const searchInput = document.querySelector('input[type=search]');
                if (searchInput) {
                    searchInput.value = decodedText;
                    searchInput.dispatchEvent(new Event('input'));
                }
                this.stopScan();
            }
        });
    },
    stopScan() {
        this.scanner.clear();
        this.showScanner = false;
    }
}" class="flex justify-center mb-6">
    <script src="https://unpkg.com/html5-qrcode"></script>
    
    <x-filament::button 
        color="warning" 
        icon="heroicon-m-qr-code" 
        size="lg"
        x-on:click="startScan()"
        x-show="!showScanner">
        Scan QR Barang
    </x-filament::button>

    <div x-show="showScanner" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         x-transition>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl max-w-lg w-full shadow-2xl">
            <div id="reader" class="overflow-hidden rounded-lg"></div>
            <x-filament::button 
                color="danger" 
                x-on:click="stopScan()" 
                class="mt-4 w-full">
                Batalkan Scan
            </x-filament::button>
        </div>
    </div>
</div>