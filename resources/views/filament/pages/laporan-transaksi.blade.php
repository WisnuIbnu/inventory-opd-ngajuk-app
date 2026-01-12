<x-filament-panels::page>
    <form wire:submit.prevent="exportExcel">
        {{ $this->form }}
        
        <div class="mt-6 flex justify-start">
            @foreach($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>
</x-filament-panels::page>