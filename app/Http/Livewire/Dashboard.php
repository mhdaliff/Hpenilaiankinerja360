<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use App\Models\AnggotaTimKerja;

class Dashboard extends Component
{
    public User $user;
    public $TimKerja;
    public $daftarTimKerja;
    public $daftarPenilaian = [];

    public function mount()
    {
        $user = auth()->user();

        // Mendapatkan daftar tim kerja yang terkait dengan user berdasarkan user_id
        $this->daftarTimKerja = TimKerja::whereHas('anggotaTimKerja', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount('anggotaTimKerja')->with('struktur')->with('anggotaTimKerja')->get(); // Menghitung jumlah anggota tim kerja
        $this->user = auth()->user();

        $strukturIds = $this->daftarTimKerja->pluck('struktur.*.id')->flatten()->all();

        $this->daftarPenilaian = Penilaian::whereIn('struktur_id', $strukturIds)
            ->where('waktu_selesai', '>', now()) // Memeriksa waktu selesai lebih besar dari waktu saat ini
            ->with('struktur.timkerja')->get();

        // Mendapatkan daftar tim kerja yang terkait dengan user berdasarkan user_id
        $this->daftarTimKerja = TimKerja::whereHas('anggotaTimKerja', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount('anggotaTimKerja')->with('struktur')->with('anggotaTimKerja')->get(); // Menghitung jumlah anggota tim kerja
        $this->user = auth()->user();

        $strukturIds = $this->daftarTimKerja->pluck('struktur.*.id')->flatten()->all();

        $this->daftarPenilaian = Penilaian::whereIn('struktur_id', $strukturIds)
            ->where('waktu_selesai', '>', now()) // Memeriksa waktu selesai lebih besar dari waktu saat ini
            ->with('struktur.timkerja')->get();

        // Short daftarPenilaian secara descending berdasarkan waktu_selesai
        $this->daftarPenilaian = $this->daftarPenilaian->sortBy('waktu_selesai')->values()->all();

        // Loop through each penilaian to add dataNilaiUser and deadlinePenilaian
        foreach ($this->daftarPenilaian as $penilaian) {
            $dataPenilaian = LogPenilaian::where('penilaian_id', $penilaian->id)
                ->where('penilai_id', $this->user->id)
                ->selectRaw("COUNT(CASE WHEN status = 'sudah' THEN 1 END) as sudah, COUNT(CASE WHEN status = 'belum' THEN 1 END) as belum")
                ->first();

            $dataNilaiUser = [
                'sudah' => $dataPenilaian->sudah,
                'belum' => $dataPenilaian->belum,
                'total' => $dataPenilaian->sudah + $dataPenilaian->belum,
            ];

            // Menghindari pembagian dengan nol
            $dataNilaiUser['persen'] = $dataNilaiUser['total'] > 0
                ? round(($dataNilaiUser['sudah'] * 100) / $dataNilaiUser['total'], 1)
                : 0;

            $penilaian->dataNilaiUser = $dataNilaiUser;
            $penilaian->deadlinePenilaian = Carbon::parse($penilaian->waktu_selesai)->format('d-m-Y'); // Calculate the number of days between today and the deadline
            $penilaian->jarakHariKeDeadline = Carbon::now()->diffInDays(Carbon::parse($penilaian->waktu_selesai), true);
        }

        // dd($this->user);
    }



    public function render()
    {
        return view('livewire.dashboard', [
            'daftarTimKerja' => $this->daftarTimKerja,
            'daftarPenilaian' => $this->daftarPenilaian,
        ]);
    }
}
