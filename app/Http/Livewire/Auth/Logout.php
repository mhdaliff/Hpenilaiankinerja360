<?php

namespace App\Http\Livewire\Auth;

use Livewire\Component;
use App\Http\Livewire\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class Logout extends Component
{
    public function logout()
    {
        auth()->logout();
        // Alert::success('Berhasil', 'Berhasil Keluar');
        // Alert::warning('Warning Title', 'Warning Message');
        toast('Berhasil Keluar', 'success');
        return redirect('/login');
    }

    public function render()
    {
        return view('livewire.auth.logout');
    }
}
