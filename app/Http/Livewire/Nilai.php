<?php

namespace App\Http\Livewire;

use App\Models\DaftarPertanyaan;
use App\Models\User;
use Livewire\Component;
use App\Models\Pertanyaan;
use App\Models\LogPenilaian;

use function Laravel\Prompts\select;

class Nilai extends Component
{
    public $currentPage = 1; // Halaman saat ini
    public $maxPage; // Jumlah maksimum halaman
    public $idLogPenilaian;
    public $idPenilaian;
    public $idDinilai;
    public $infoDinilai;
    public $daftarPertanyaan = [];
    public $indikatorPenilaian = [];

    public function mount($id)
    {
        $logPenilaian = LogPenilaian::where('id', $id)
            ->with('dinilai')
            ->first();
        $this->idLogPenilaian = $logPenilaian->id;
        $this->idDinilai = $logPenilaian->dinilai_id;
        $this->idPenilaian = $logPenilaian->penilaian_id;
        // dd($this->idDinilai);

        $this->infoDinilai = User::where('id', $this->idDinilai)->first();
        // dd($this->infoDinilai);

        // Daftar Pertanyaan
        $daftarPertanyaanId = Pertanyaan::where('penilaian_id', $this->idPenilaian)
            ->pluck('daftar_pertanyaan_id')
            ->toArray();


        // dd($daftarPertanyaanId);
        $this->daftarPertanyaan = DaftarPertanyaan::whereIn('id', $daftarPertanyaanId)
            ->with('indikatorPenilaian')
            ->select('id', 'indikator_penilaian_id', 'pertanyaan')
            ->get();

        $indikatorPenilaian = [];

        // Lakukan pengelompokan pertanyaan berdasarkan indikator penilaian
        foreach ($this->daftarPertanyaan as $pertanyaan) {
            // Ambil indikator penilaian dari setiap pertanyaan
            $indikator = $pertanyaan->indikatorPenilaian->indikator;

            // Tambahkan pertanyaan ke dalam array indikatorPenilaian berdasarkan indikatornya
            $indikatorPenilaian[$indikator][] = [
                'daftar_pertanyaan_id' => $pertanyaan->id,
                'pertanyaan' => $pertanyaan->pertanyaan,
                'nilai' => ''
            ];
        }

        // Ubah struktur data menjadi array yang sesuai
        $indikatorPenilaian = array_map(function ($indikator, $pertanyaan) {
            return [
                'indikator' => $indikator,
                $pertanyaan
            ];
        }, array_keys($indikatorPenilaian), $indikatorPenilaian);

        $this->indikatorPenilaian = $indikatorPenilaian;
        // dd($this->daftarPertanyaan);
        // dd($this->indikatorPenilaian);
        $this->maxPage = count($this->indikatorPenilaian);
        // dd($this->maxPage);
    }

    public function next()
    {
        // Validasi jika halaman saat ini bukan halaman terakhir
        if ($this->currentPage < $this->maxPage) {
            $this->currentPage++;
        }
    }

    public function back()
    {
        // Validasi jika halaman saat ini bukan halaman pertama
        if ($this->currentPage > 1) {
            $this->currentPage--;
        }
    }

    public function validateInputs()
    {
        // Loop melalui semua indikator penilaian
        foreach ($this->indikatorPenilaian as $indikator) {
            // Loop melalui semua pertanyaan dalam indikator penilaian
            foreach ($indikator[0] as $pertanyaan) {
                // Jika nilai pertanyaan kosong, kembalikan false
                if ($pertanyaan['nilai'] === '') {
                    // Kirimkan pesan flash ke browser
                    $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => 'Harap isi semua nilai sebelum melanjutkan.']);
                    return false;
                }
            }
        }
        // Semua input telah divalidasi
        return true;
    }

    public function save()
    {
        // Validasi input sebelum menyimpan
        if (!$this->validateInputs()) {
            return;
        }

        // Iterasi melalui semua indikator penilaian
        foreach ($this->indikatorPenilaian as $indikator) {
            // Iterasi melalui semua pertanyaan dalam indikator penilaian
            foreach ($indikator[0] as $pertanyaan) {
                // Buat instance LogNilai
                $logNilai = new \App\Models\LogNilai();
                // Set atribut-atribut log_nilai
                $logNilai->log_penilaian_id = $this->idLogPenilaian;
                $logNilai->pertanyaans_id = $pertanyaan['daftar_pertanyaan_id'];
                $logNilai->nilai = $pertanyaan['nilai'];
                // Simpan record log_nilai ke database
                $logNilai->save();
            }
        }

        // Ubah status log_penilaian menjadi 'sudah'
        $logPenilaian = LogPenilaian::find($this->idLogPenilaian);
        $logPenilaian->status = 'sudah';
        $logPenilaian->save();

        toast('Nilai berhasil disimpan', 'success');

        // Redirect ke halaman detail penilaian
        return redirect()->route('detail-penilaian', ['id' => $this->idPenilaian]);
    }


    public function render()
    {
        return view('livewire.nilai');
    }
}
