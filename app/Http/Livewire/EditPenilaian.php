<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Penilaian;

class EditPenilaian extends Component
{
    public $namaPenilaian;

    public $idPenilaian;

    public function mount($id)
    {
        $this->idPenilaian = $id;

        $penilaian = Penilaian::findOrFail($id);
    }

    public function simpan()
    {
    }
    public function render()
    {
        return view('livewire.edit-penilaian');
    }
}
