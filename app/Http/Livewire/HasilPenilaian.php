<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Penilaian;

class HasilPenilaian extends Component
{
    public $idPenilaian;
    public $infoPenilaian;
    public $daftarPenilaian;

    public function mount($id)
    {
        $this->idPenilaian = $id;
        $this->infoPenilaian = Penilaian::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.hasil-penilaian');
    }
}
