<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\TimKerja;
use App\Models\AnggotaTimKerja;
use Illuminate\Support\Facades\Auth;

class DaftarHasilPenilaian extends Component
{
    public $daftarIdTimKerja;
    public $daftarTimKerja;
    public User $user;

    public function render()
    {
        // Mendapatkan user yang sedang login
        $this->user = Auth::user();

        // Mengambil daftar ID tim kerja yang diikuti oleh user
        $this->daftarIdTimKerja = AnggotaTimKerja::where('user_id', $this->user->id)
            ->where('role', 'admin')
            ->pluck('tim_kerja_id');

        // Mengambil data tim kerja berdasarkan daftar ID tim kerja
        // menggunakan eager loading untuk mengambil relasi struktur dan penilaian
        $this->daftarTimKerja = TimKerja::whereIn('id', $this->daftarIdTimKerja)
            ->with('struktur.penilaian') // Menambahkan eager loading untuk relasi struktur dan penilaian
            ->get();

        return view('livewire.daftar-hasil-penilaian');
    }
}
