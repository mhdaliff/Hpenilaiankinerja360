<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use Livewire\WithPagination;
use App\Models\AnggotaStruktur;
use App\Models\AnggotaTimKerja;
use App\Models\JabatanStruktur;
use App\Models\DaftarPertanyaan;
use App\Models\IndikatorPenilaian;
use RealRashid\SweetAlert\Facades\Alert;

class DetailTimKerja extends Component
{
    use WithPagination;

    public $idTimKerja;
    public $infoTimKerja;
    public $activeTab = 'struktur-tim-kerja';
    public $userRole;
    public $daftarStruktur = [];
    public $selectedStruktur = '';
    public $jabatanStruktur = [];
    public $indikatorPenilaian = [];
    public $daftarPenilaian;

    public function mount($id)
    {
        // Identifikasi Id Tim Kerja
        $this->idTimKerja = $id;

        // Identifikasi Role User
        $this->userRole = AnggotaTimKerja::where('user_id', auth()->user()->id)
            ->where('tim_kerja_id', $this->idTimKerja)
            ->value('role');

        // Identifikasi Struktur Tim kerja
        $this->infoTimKerja = TimKerja::findOrFail($id);
        $struktur = Struktur::where('tim_kerja_id', $id)->get();

        // Mengambil hanya nama struktur dari setiap data struktur
        $this->daftarStruktur = $struktur->pluck('nama_struktur')->toArray();
        // Mendebug daftar struktur
        // dd($this->daftarStruktur);

        // Mengambil data indikatorPenilaian
        $daftarIndikator = IndikatorPenilaian::where('tim_kerja_id', $id)->get();
        foreach ($daftarIndikator as $indikator) {
            $daftarPertanyaan = DaftarPertanyaan::where('indikator_penilaian_id', $indikator->id)->get();
            $pertanyaan = $daftarPertanyaan->pluck('pertanyaan')->toArray();
            $this->indikatorPenilaian[] = [
                'indikator' => $indikator->indikator,
                'pertanyaan' => $pertanyaan,
            ];
        }
        $this->importData($id);

        $this->dataPenilaian($id);
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function importData($idTimKerja)
    {
        // Mendapatkan semua struktur tim kerja
        $struktur = Struktur::where('tim_kerja_id', $idTimKerja)->get();

        // Inisialisasi array jabatanStruktur
        $this->jabatanStruktur = [];

        // Iterasi melalui setiap struktur
        foreach ($struktur as $s) {
            // Mendapatkan jabatan dalam struktur tersebut
            $jabatan = JabatanStruktur::where('struktur_id', $s->id)->orderBy('level')->get();

            $strukturData = [];
            foreach ($jabatan as $jab) {
                $atasanNama = ''; // Inisialisasi variabel untuk menyimpan nama jabatan atasan

                // Jika jabatan memiliki atasan, tetapkan nama jabatan atasan
                if ($jab->atasan != null) {
                    $atasan = JabatanStruktur::find($jab->atasan); // Cari jabatan atasan berdasarkan ID

                    // Jika jabatan atasan ditemukan, tetapkan nama jabatannya
                    if ($atasan) {
                        $atasanNama = $atasan->nama_jabatan;
                    }
                }

                // Menambahkan data jabatan ke dalam array struktur
                $strukturData[] = [
                    'level' => $jab->level,
                    'nama_jabatan' => $jab->nama_jabatan,
                    'pejabat' => $this->getPejabat($jab->id), // Memanggil fungsi getPejabat untuk mendapatkan pejabat jabatan
                    'atasan' => $atasanNama, // Menyimpan nama jabatan atasan
                ];
            }

            // Menambahkan data struktur beserta jabatannya ke dalam array jabatanStruktur
            $this->jabatanStruktur[] = [
                'nama_struktur' => $s->nama_struktur,
                'jabatan' => $strukturData,
            ];
        }

        // Set default selectedStruktur jika tidak ada data
        $this->selectedStruktur = $this->daftarStruktur[0] ?? '';

        // Mendebug hasil jabatan struktur
        // dd($this->jabatanStruktur);
    }

    public function dataPenilaian($idTimKerja)
    {
        $strukturIds = Struktur::where('tim_kerja_id', $idTimKerja)
            ->pluck('id')
            ->all();

        // dd($strukturIds);

        $this->daftarPenilaian = Penilaian::whereIn('struktur_id', $strukturIds)
            ->where('waktu_selesai', '>', now()) // Memeriksa waktu selesai lebih besar dari waktu saat ini
            ->orderBy('waktu_selesai', 'asc')
            ->limit(4)
            ->with('struktur')->get()
            ->map(function ($penilaian) {
                $penilaian->telahDinilai = LogPenilaian::where('penilaian_id', $penilaian->id)
                    ->where('penilai_id', auth()->user()->id)
                    ->where('status', 'sudah')
                    ->count();

                $penilaian->totalDinilai = LogPenilaian::where('penilaian_id', $penilaian->id)
                    ->where('penilai_id', auth()->user()->id)
                    ->count();

                $penilaian->jarakDeadline = Carbon::now()->diffInDays(Carbon::parse($penilaian->waktu_selesai), false);

                return $penilaian;
            });

        // dd($this->daftarPenilaian);

        // $this->daftarPenilaian = Penilaian::where('struktur_id')
    }

    public function getPejabat($jabatanId)
    {
        // Mendapatkan anggota Tim Kerja ID berdasarkan jabatan ID
        $anggotaTimKerjaId = AnggotaStruktur::where('jabatan_struktur_id', $jabatanId)->pluck('anggota_tim_kerja_id')->toArray();

        // Mendapatkan user_id berdasarkan anggota_tim_kerja_id
        $userIds = AnggotaTimKerja::whereIn('id', $anggotaTimKerjaId)->pluck('user_id')->toArray();

        // Mendapatkan email pejabat berdasarkan user_id
        $emails = User::whereIn('id', $userIds)->pluck('name')->toArray();

        return $emails;
    }

    public function tambahIndikator()
    {
        $this->indikatorPenilaian[] = [
            'indikator' => '',
            'pertanyaan' => [''],
        ];
    }

    public function tambahPertanyaan($index)
    {
        $this->indikatorPenilaian[$index]['pertanyaan'][] = '';
    }

    public function hapusPertanyaan($indikatorKey, $pertanyaanIndex)
    {
        unset($this->indikatorPenilaian[$indikatorKey]['pertanyaan'][$pertanyaanIndex]);
        // Reindex array setelah penghapusan agar kunci array berurutan
        $this->indikatorPenilaian[$indikatorKey]['pertanyaan'] = array_values($this->indikatorPenilaian[$indikatorKey]['pertanyaan']);
    }

    public function hapusIndikator($indikatorKey)
    {
        unset($this->indikatorPenilaian[$indikatorKey]);
        // Reindex array setelah penghapusan agar kunci array berurutan
        $this->indikatorPenilaian = array_values($this->indikatorPenilaian);
    }

    public function simpanPerubahanIndikator()
    {
        // dd($this->indikatorPenilaian);

        toast('Perubahan berhasil disimpan!', 'success');
        $this->activeTab = 'indikator-penilaian';
        return redirect()->route('detail-tim-kerja', [
            'id' => $this->idTimKerja
        ]);
    }

    public function konfirmasiSimpan()
    {
        // Implementasi logika penyimpanan perubahan ke database
        // Misalnya, Anda dapat mengakses dan memanipulasi $this->indikatorPenilaian untuk menyimpan perubahan
        // Setelah penyimpanan berhasil, Anda dapat menampilkan pesan sukses menggunakan toast
        $this->alert('success', 'Perubahan berhasil disimpan!');
    }


    public function render()
    {
        // dd($this->jabatanStruktur);
        return view('livewire.detail-tim-kerja', [
            'infoTimKerja' => $this->infoTimKerja,
            'daftarStruktur' => $this->daftarStruktur,
            'selectedStruktur' => $this->selectedStruktur,
            'jabatanStruktur' => $this->jabatanStruktur[$this->selectedStruktur] ?? [],
            'indikatorPenilaian' => $this->indikatorPenilaian,
            'activeTab' => $this->activeTab,
        ]);
    }
}
