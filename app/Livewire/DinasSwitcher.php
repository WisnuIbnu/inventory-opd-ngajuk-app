<?php

namespace App\Livewire;

use App\Models\Dinas;
use Livewire\Component;

class DinasSwitcher extends Component
{
    public $selectedDinas;

    public function mount()
    {
        $this->selectedDinas = session('admin_dinas_id');
    }

    public function updatedSelectedDinas($value)
    {
        session(['admin_dinas_id' => $value]);
        
        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        return view('livewire.dinas-switcher', [
            'semuaDinas' => Dinas::all()
        ]);
    }
}
