<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\Pertanyaan;
use App\Models\LogPenilaian;
use App\Models\BobotPenilaian;
use App\Models\AnggotaStruktur;
use App\Models\AnggotaTimKerja;
use App\Models\JabatanStruktur;
use App\Models\DaftarPertanyaan;
use App\Models\IndikatorPenilaian;
use RealRashid\SweetAlert\Facades\Alert;

class BuatPenilaian extends Component
{
    public User $user;
    public $showSuccesNotification = false;
    public $daftarTimKerja;
    public $daftarIdTimKerja;
    public $penilaian = [
        'timKerja' => '',
        'namaPenilaian' => '',
        'deskripsiPenilaian' => '',
        'maks-responden' => '10',
        'metode' => '',
        'atasan' => '40',
        'sebaya' => '35',
        'bawahan' => '25',
        'diriSendiri' => '0',
        'mulai' => '',
        'selesai' => '',
    ];
    public $formStep = 1;

    // Step 2
    public $indikatorPenilaian = [];

    public function mount()
    {
        $this->user = auth()->user();

        // Mengambil daftar ID tim kerja yang diikuti oleh user
        $this->daftarIdTimKerja = AnggotaTimKerja::where('user_id', $this->user->id)
            ->where('role', 'admin')
            ->pluck('tim_kerja_id');
        // Mengambil data tim kerja berdasarkan daftar ID tim kerja
        $this->daftarTimKerja = TimKerja::whereIn('id', $this->daftarIdTimKerja)->with('struktur')->get();
        // dd($this->daftarTimKerja);
    }

    public function getIndikatorPenilaian($idTimKerja)
    {
        $indikatorPenilaian = IndikatorPenilaian::where('tim_kerja_id', $idTimKerja)->get();
        foreach ($indikatorPenilaian as $indikator) {
            $existingIndex = $this->findIndikatorIndex($indikator->indikator);

            if ($existingIndex === null) {
                $pertanyaans = DaftarPertanyaan::where('indikator_penilaian_id', $indikator->id)->get();

                $pertanyaanArray = [];

                foreach ($pertanyaans as $pertanyaan) {
                    $pertanyaanArray[] = [
                        'id_pertanyaan' => $pertanyaan->id,
                        'pertanyaan' => $pertanyaan->pertanyaan,
                        'status_check' => 'checked',
                    ];
                }

                // Tambahkan data indikator dan pertanyaan ke dalam array
                $this->indikatorPenilaian[] = [
                    'indikator' => $indikator->indikator,
                    'pertanyaans' => $pertanyaanArray,
                ];
            }
        }
        // dd($this->indikatorPenilaian);
    }

    private function findIndikatorIndex($indikatorName)
    {
        foreach ($this->indikatorPenilaian as $index => $indikator) {
            if ($indikator['indikator'] === $indikatorName) {
                return $index;
            }
        }
        return null;
    }

    public function stepSelanjutnya()
    {
        // dd($this->penilaian);
        if ($this->formStep == 1) {
            $this->validateStep1();
            $this->getIndikatorPenilaian($this->penilaian['timKerja']);
        }
        $this->formStep++;
    }

    public function stepSebelumnya()
    {
        $this->formStep--;
    }

    public function validateStep1()
    {
        $this->showSuccesNotification = true;

        // Validasi total bobot penilaian
        $totalBobot = $this->penilaian['atasan'] + $this->penilaian['sebaya'] + $this->penilaian['bawahan'] + $this->penilaian['diriSendiri'];
        if ($totalBobot != 100) {
            $this->addError('total_bobot_error', 'Total bobot penilaian harus 100 %.');
        }

        $this->validate([
            'penilaian.timKerja' => 'required', // Menjamin pemilihan tim kerja
            'penilaian.namaPenilaian' => 'required', // Menjamin nama penilaian diisi
            'penilaian.struktur' => 'required', // Menjamin deskripsi penilaian diisi
            'penilaian.atasan' => 'required|numeric|min:0', // Menjamin bobot atasan adalah angka dan tidak negatif
            'penilaian.sebaya' => 'required|numeric|min:0', // Menjamin bobot sebaya adalah angka dan tidak negatif
            'penilaian.bawahan' => 'required|numeric|min:0', // Menjamin bobot bawahan adalah angka dan tidak negatif
            'penilaian.diriSendiri' => 'required|numeric|min:0', // Menjamin bobot diri sendiri adalah angka dan tidak negatif
            'penilaian.mulai' => 'required|date', // Menjamin tanggal mulai diisi dan merupakan format tanggal yang valid
            'penilaian.selesai' => 'required|date|after:penilaian.mulai', // Menjamin tanggal selesai diisi, merupakan format tanggal yang valid, dan setelah tanggal mulai
            'penilaian.metode' => 'required',
        ], [
            'penilaian.timKerja.required' => 'Tim Kerja harus dipilih.',
            'penilaian.namaPenilaian.required' => 'Nama Penilaian harus diisi.',
            'penilaian.struktur.required' => 'Deskripsi Penilaian harus diisi.',
            'penilaian.*.required' => 'Field :attribute harus diisi.',
            'penilaian.*.numeric' => 'Field :attribute harus berupa angka.',
            'penilaian.*.min' => 'Field :attribute tidak boleh kurang dari 0.',
            'penilaian.mulai.required' => 'Tanggal Mulai harus diisi.',
            'penilaian.mulai.date' => 'Format Tanggal Mulai tidak valid.',
            'penilaian.selesai.required' => 'Tanggal Selesai harus diisi.',
            'penilaian.selesai.date' => 'Format Tanggal Selesai tidak valid.',
            'penilaian.selesai.after' => 'Tanggal Selesai harus setelah Tanggal Mulai.',
        ]);
    }

    public function validateStep2()
    {
        // validasi jika tidak ada satupun yang memiliki status checked !
    }

    public function save()
    {
        // dd($this->indikatorPenilaian);

        // Validasi step 2
        $this->validateStep2();

        // Simpan data ke database
        $penilaian = Penilaian::create([
            'nama_penilaian' => $this->penilaian['namaPenilaian'],
            'struktur_id' => $this->penilaian['struktur'],
            'jumlah_responden' => $this->penilaian['maks-responden'],
            'metode' => $this->penilaian['metode'],
            'waktu_mulai' => $this->penilaian['mulai'],
            'waktu_selesai' => $this->penilaian['selesai'],
        ]);

        // dd($penilaian->id);
        // $dataPenilaian = Penilaian::where('nama_penilaian', $penilaian['nama_penilaian'])->where('struktur_id', $penilaian['struktur_id'])->get();
        // dd($dataPenilaian);
        if ($this->penilaian['metode'] == 'proporsional') {
            $bobotPenilaian = BobotPenilaian::create([
                'penilaian_id' => $penilaian->id,
                'atasan' => $this->penilaian['atasan'],
                'sebaya' => $this->penilaian['sebaya'],
                'bawahan' => $this->penilaian['bawahan'],
                'diri_sendiri' => $this->penilaian['diriSendiri'],
            ]);
        }

        foreach ($this->indikatorPenilaian as $indikator) {
            foreach ($indikator['pertanyaans'] as $pertanyaan) {
                if ($pertanyaan['status_check'] == 'checked') {
                    Pertanyaan::create([
                        'penilaian_id' => $penilaian->id,
                        'daftar_pertanyaan_id' => $pertanyaan['id_pertanyaan'],
                    ]);
                }
            }
        }

        // Tambahkan log penilaian
        $this->addLogPenilaian($penilaian->id);
        Alert::success('Berhasil', 'Berhasil Membuat Penilaian');

        // Reset data penilaian
        $this->resetPenilaianData();

        // Redirect atau tampilkan pesan sukses
    }

    private function addLogPenilaian($idPenilaian)
    {
        $jabatanStruktur = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])->get();
        // dd($jabatanStruktur);

        foreach ($jabatanStruktur as $key => $jabatan) {
            $anggotaStruktur = AnggotaStruktur::where('jabatan_struktur_id', $jabatan->id)->with('anggotaTimKerja')->get();
            // dd($anggotaStruktur);

            foreach ($anggotaStruktur as $a => $anggota) {
                // Melakukan query untuk mencari penilai
                $penilai_id = $anggota->anggotaTimKerja->user_id;
                $penilai = User::where('id', $penilai_id)->get();

                // BAWAHAN KE ATASAN
                // Melaukan query untuk mencari jabatan penilai
                $jabatanPenilai = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])->where('id', $anggota->jabatan_struktur_id)->get();

                foreach ($jabatanPenilai as $f => $jabPenilai) {
                    // dd($jabPenilai->atasan);
                    if ($jabPenilai->atasan != null) {
                        // Melakukan query untuk mencari jabatan yang merupakan atasan dari penilai
                        $jabatanDinilai = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])->where('id', $jabPenilai->atasan)->get();
                        // dd($jabatanDinilai);
                        foreach ($jabatanDinilai as $g => $jabDinilai) {
                            // dd($jabDinilai);
                            $daftarDinilai = AnggotaStruktur::where('jabatan_struktur_id', $jabDinilai->id)->with('anggotaTimKerja')->get();
                            // dd($daftarDinilai);
                            foreach ($daftarDinilai as $e => $dinilai) {
                                // dd($dinilai->anggotaTimKerja);
                                $LogPenilaian = LogPenilaian::create([
                                    'penilaian_id' => $idPenilaian,
                                    'penilai_id' => $penilai_id,
                                    'role_penilai' => 'bawahan',
                                    'dinilai_id' => $dinilai->anggotaTimKerja->user_id,
                                    'status' => 'belum',
                                ]);
                            }
                        }
                    }
                }

                // SEBAYA
                // Melakukan query untuk mencari daftar nama yang memiliki jabatan yang sama

                // $atasan = JabatanStruktur::where('struktur_id',)
                $jabatanPenilai = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])
                    ->where('id', $anggota->jabatan_struktur_id)
                    ->first();

                $jabatanSebaya = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])
                    ->where('atasan', $jabatanPenilai->atasan)
                    ->get();
                // dd($jabatanSebaya);
                foreach ($jabatanSebaya as $d => $sebaya) {
                    $daftarDinilai = AnggotaStruktur::where('jabatan_struktur_id', $sebaya->id)->with('anggotaTimKerja')->get();
                    // dd($daftarDinilai);
                    foreach ($daftarDinilai as $e => $dinilai) {
                        // dd($dinilai->anggotaTimKerja);
                        $status_penilai = ($penilai_id == $dinilai->anggotaTimKerja->user_id) ? 'diri sendiri' : 'sebaya';
                        // dd($status_penilai);
                        $LogPenilaian = LogPenilaian::create([
                            'penilaian_id' => $idPenilaian,
                            'penilai_id' => $penilai_id,
                            'role_penilai' => $status_penilai,
                            'dinilai_id' => $dinilai->anggotaTimKerja->user_id,
                            'status' => 'belum',
                        ]);
                    }
                }

                // ATASAN KE BAWAHAN
                // Melakukan query untuk mencari daftar nama bawahan yang dinilai
                $daftarJabatanBawahan = JabatanStruktur::where('struktur_id', $this->penilaian['struktur'])->where('atasan', $anggota->jabatan_struktur_id)->get();
                // dd($anggota->jabatan_struktur_id);
                // dd($daftarJabatanBawahan);
                foreach ($daftarJabatanBawahan as $b => $bawahan) {
                    $daftarDinilai = AnggotaStruktur::where('jabatan_struktur_id', $bawahan->id)->with('anggotaTimKerja')->get();
                    foreach ($daftarDinilai as $c => $dinilai) {

                        // dd($dinilai->anggotaTimKerja->user_id);
                        $logPenilaian = LogPenilaian::create([
                            'penilaian_id' => $idPenilaian,
                            'penilai_id' => $penilai_id,
                            'dinilai_id' => $dinilai->anggotaTimKerja->user_id,
                            'role_penilai' => 'atasan',
                            'status' => 'belum',
                        ]);
                    }
                    // dd($daftarDinilai);
                }
            };
        };

        // dd($anggotaStruktur);
        // $anggotaStruktur = AnggotaStruktur::where()
        // // Ambil semua user yang terlibat dalam penilaian
        // $users = AnggotaTimKerja::where('tim_kerja_id', $this->penilaian['timKerja'])
        //     ->whereIn('role', ['atasan', 'sebaya', 'bawahan'])
        //     ->pluck('user_id');

        // // Buat log penilaian untuk setiap user
        // foreach ($users as $user) {
        //     LogPenilaian::create([
        //         'penilai_id' => $this->user->id,
        //         'dinilai_id' => $user,
        //         'status_penilai' => $this->getRole($user),
        //         'status' => 'belum', // Set status default menjadi belum
        //     ]);
        // }
    }

    private function getRole($userId)
    {
        // Ambil peran pengguna berdasarkan id pengguna
        // Misalnya, Anda memiliki logika untuk menentukan peran pengguna
        // di sini dan kembalikan peran pengguna.
    }

    private function resetPenilaianData()
    {
        // Reset semua data penilaian
        $this->penilaian = [
            'timKerja' => '',
            'namaPenilaian' => '',
            'deskripsiPenilaian' => '',
            'atasan' => '40',
            'sebaya' => '30',
            'bawahan' => '20',
            'diriSendiri' => '10',
            'mulai' => '',
            'selesai' => '',
        ];

        $this->indikatorPenilaian = [];
        $this->formStep = 1;


        return redirect()->route('penilaian');
    }

    public function render()
    {
        return view('livewire.buat-penilaian', [
            'daftarTimKerja' => $this->daftarTimKerja,
        ]);
    }
}
