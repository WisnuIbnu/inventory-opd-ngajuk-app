<div class="pb-4 border-b border-gray-200 dark:border-gray-800">
    <label class="text-xs font-bold uppercase text-gray-500">Filter Wilayah OPD</label>
    <select 
        wire:model.live="selectedDinas" 
        class="mt-1 block w-full rounded-lg border-gray-300 dark:bg-gray-800 dark:border-gray-700 text-sm"
    >
        <option value="">Semua Dinas</option>
        @foreach($semuaDinas as $dinas)
            <option class="w-full rounded-lg" value="{{ $dinas->id }}">{{ $dinas->nama_opd }}</option>
        @endforeach
    </select>
</div>