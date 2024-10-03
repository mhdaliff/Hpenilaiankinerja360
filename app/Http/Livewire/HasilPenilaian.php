<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\LogNilai;
use App\Models\Struktur;
use App\Models\TimKerja;
use App\Models\Penilaian;
use App\Models\LogPenilaian;
use App\Models\AnggotaTimKerja;
use App\Models\IndikatorPenilaian;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HasilPenilaianExport;
use RealRashid\SweetAlert\Facades\Alert;

class HasilPenilaian extends Component
{
    public $idPenilaian;
    public $infoPenilaian;
    public $daftarPenilaian;
    public $daftarIndikator;
    public $Nilai = [
        [
            'dinilai' => '',
            'nilai' => [
                'indikator 1' => [
                    'id' => '',
                    'nilai_akhir' => '',
                ]
            ]
        ]
    ];
    public $userRole;

    public $infoNilai = [];

    public function mount($id)
    {
        $this->idPenilaian = $id;
        $this->infoPenilaian = Penilaian::findOrFail($id);

        // dd($this->infoPenilaian);
        $getStruktur = Struktur::where('id', $this->infoPenilaian->struktur_id)->first();
        $getTimKerja = TimKerja::where('id', $getStruktur->tim_kerja_id)->first();

        // Identifikasi Role User
        $this->userRole = AnggotaTimKerja::where('user_id', auth()->user()->id)
            ->where('tim_kerja_id', $getTimKerja->id)
            ->value('role');

        $this->idPenilaian = $id;
        $this->infoPenilaian = Penilaian::findOrFail($id)
            ->toArray();

        $this->infoPenilaian['atasan'] = 40;
        $this->infoPenilaian['sebaya'] = 30;
        $this->infoPenilaian['bawahan'] = 20;
        $this->infoPenilaian['diriSendiri'] = 10;
        $this->infoPenilaian['metode'] = 'aritmatika';
        $this->infoPenilaian['jumlahIndikator'] = 0;
        $this->infoPenilaian['filter'] = 'semua';

        // dd($this->infoPenilaian);
        $this->daftarPenilaian = LogPenilaian::where('penilaian_id', $this->idPenilaian)
            ->where('status', 'sudah')
            ->with(['dinilai', 'logNilai.pertanyaan.daftarPertanyaan.indikatorPenilaian'])
            ->get()
            ->groupBy('dinilai_id')
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item];
            })->toArray();

        // dd($this->daftarPenilaian);
        $this->daftarIndikator();
        $this->nilaiAkhir();
        // dd($this->Nilai);
    }

    public function daftarIndikator()
    {
        $this->daftarIndikator = [];

        foreach ($this->daftarPenilaian as $logPenilaian) {
            foreach ($logPenilaian[0]['log_nilai'] as $logNilai) {
                $indikatorId = $logNilai['pertanyaan']['daftar_pertanyaan']['indikator_penilaian_id'];
                $indikator = IndikatorPenilaian::findOrFail($indikatorId)->indikator;

                // Memastikan indikator tidak duplikat sebelum menambahkannya ke daftar
                if (!in_array($indikator, array_column($this->daftarIndikator, 'nama'))) {
                    $this->daftarIndikator[] = [
                        'id' => $indikatorId,
                        'nama' => $indikator
                    ];
                }
            }
        }
        $this->infoPenilaian['jumlahIndikator'] = count($this->daftarIndikator);
    }

    public function nilaiAkhir()
    {
        if ($this->infoPenilaian['metode'] == 'aritmatika') {
            $this->aritmatika();
        } else {
            $totalBobot = $this->infoPenilaian['atasan'] + $this->infoPenilaian['sebaya'] + $this->infoPenilaian['bawahan'] + $this->infoPenilaian['diriSendiri'];

            if ($totalBobot != 100) {
                $this->dispatchBrowserEvent('swal:warning', ['message' => 'Total bobot harus 100!']);
                return;
            }
            $this->proporsional();
        }

        // Validasi
        if (empty($this->Nilai)) {
            Alert::error('Error', 'Data hasil penilaian tidak ada!');
            return redirect()->route('hasil-penilaian');
        }

        // Menghitung rata-rata total tiap indikator
        foreach ($this->daftarIndikator as $key => $indikator) {
            $namaIndikator = $indikator['nama'];
            $akumulasi = 0;
            $jumlahRow = 0;
            foreach ($this->Nilai as $key => $item) {
                $nilai = $item['nilai'];
                $akumulasi = $akumulasi + $nilai[$namaIndikator]['nilai_akhir'];
                $jumlahRow = $key + 1;
            }
            $rataRataIndikator = round($akumulasi / $jumlahRow, 1);
            $this->infoNilai['rerata_indikator'][$namaIndikator] = $rataRataIndikator;
        }

        // Menghitung rata-rata total
        $jumlah_indikator = count($this->infoNilai['rerata_indikator']);
        if ($jumlah_indikator > 0) {
            $total_rerata_indikator = array_sum($this->infoNilai['rerata_indikator']);
            $this->infoNilai['rerata_total'] = round($total_rerata_indikator / $jumlah_indikator, 1);
        }

        $this->dispatchBrowserEvent('swal:success', ['message' => 'Berhasil menerapkan metode!']);
    }

    public function proporsional()
    {

        $this->Nilai = [];

        // dd($this->daftarPenilaian);
        foreach ($this->daftarPenilaian as $dinilai) {
            $dataDinilai = [
                'dinilai' => $dinilai[0]['dinilai'],
                'nilai' => []
            ];

            $indikatorData = [];

            // Menyiapkan variabel untuk menghitung total nilai dan jumlah penilai berdasarkan role
            foreach ($dinilai as $daftarNilai) {
                foreach ($daftarNilai['log_nilai'] as $logNilai) {
                    $indikatorId = $logNilai['pertanyaan']['daftar_pertanyaan']['indikator_penilaian_id'];
                    $indikator = IndikatorPenilaian::findOrFail($indikatorId)->indikator;
                    $role = $daftarNilai['role_penilai'];
                    $nilai = $logNilai['nilai'];

                    if (!isset($indikatorData[$indikator])) {
                        $indikatorData[$indikator] = [
                            'id' => $indikatorId,
                            'total_nilai' => 0,
                            'total_inputan' => 0,
                            'rata_rata' => 0,
                            'atasan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'sebaya' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'bawahan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'diri sendiri' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0]
                        ];
                    }

                    $indikatorData[$indikator][$role]['total_nilai'] += $nilai;
                    $indikatorData[$indikator][$role]['jumlah_penilai']++;
                }
            }

            // Menghitung rata-rata nilai untuk tiap role dan indikator
            foreach ($indikatorData as $indikator => &$data) {
                foreach (['atasan', 'sebaya', 'bawahan', 'diri sendiri'] as $role) {
                    if ($data[$role]['jumlah_penilai'] > 0) {
                        $data[$role]['rata_rata'] = $data[$role]['total_nilai'] / $data[$role]['jumlah_penilai'];
                    }
                }

                // Menghitung nilai akhir proporsional untuk indikator tertentu
                $bobotAtasan = $this->infoPenilaian['atasan'];
                $bobotSebaya = $this->infoPenilaian['sebaya'];
                $bobotBawahan = $this->infoPenilaian['bawahan'];
                $bobotDiriSendiri = $this->infoPenilaian['diriSendiri'];

                // Penanganan jika jumlah_inputan untuk masing-masing role adalah 0
                $jumlahInputanAtasan = $data['atasan']['jumlah_penilai'];
                $jumlahInputanSebaya = $data['sebaya']['jumlah_penilai'];
                $jumlahInputanBawahan = $data['bawahan']['jumlah_penilai'];
                $jumlahInputanDiriSendiri = $data['diri sendiri']['jumlah_penilai'];


                // Hitung nilai akhir berdasarkan rata-rata dan bobot
                $nilaiAkhir = 0;
                $totalBobot = 0;

                if ($jumlahInputanAtasan > 0) {
                    $nilaiAkhir += $data['atasan']['rata_rata'] * $bobotAtasan;
                    $totalBobot += $bobotAtasan;
                }

                if ($jumlahInputanSebaya > 0) {
                    $nilaiAkhir += $data['sebaya']['rata_rata'] * $bobotSebaya;
                    $totalBobot += $bobotSebaya;
                }

                if ($jumlahInputanBawahan > 0) {
                    $nilaiAkhir += $data['bawahan']['rata_rata'] * $bobotBawahan;
                    $totalBobot += $bobotBawahan;
                }

                if ($jumlahInputanDiriSendiri > 0) {
                    $nilaiAkhir += $data['diri sendiri']['rata_rata'] * $bobotDiriSendiri;
                    $totalBobot += $bobotDiriSendiri;
                }

                // Jika totalBobot tidak sama dengan 0, hitung nilai akhir
                if ($totalBobot > 0) {
                    $nilaiAkhir /= $totalBobot;
                } else {
                    $nilaiAkhir = 0; // Handle jika tidak ada bobot yang digunakan
                }

                $data['nilai_akhir'] = round($nilaiAkhir, 1);
                $data['total_nilai'] = array_sum(array_column($data, 'total_nilai'));
                $data['total_inputan'] = array_sum(array_column($data, 'jumlah_penilai'));
                $data['rata_rata'] = $data['total_inputan'] > 0 ? $data['total_nilai'] / $data['total_inputan'] : 0;

                $dataDinilai['nilai'][$indikator] = $data;
            }
            if ($this->infoPenilaian['jumlahIndikator'] > 0) {
                $rataRataTotal = array_sum(array_column($dataDinilai['nilai'], 'nilai_akhir')) / $this->infoPenilaian['jumlahIndikator'];
            } else {
                $rataRataTotal = 0; // Handle division by zero scenario
            }
            $dataDinilai['rata_rata_total'] = round($rataRataTotal, 1);

            if (!($dataDinilai['nilai'] == null)) {
                // Memasukkan data dinilai ke dalam array Nilai
                $this->Nilai[] = $dataDinilai;
            }
        }
        // dd($this->Nilai);
    }

    public function aritmatika()
    {
        $this->Nilai = [];

        foreach ($this->daftarPenilaian as $dinilai) {
            $dataDinilai = [
                'dinilai' => $dinilai[0]['dinilai'],
                'nilai' => []
            ];

            $indikatorData = [];

            foreach ($dinilai as $daftarNilai) {
                foreach ($daftarNilai['log_nilai'] as $logNilai) {
                    $indikatorId = $logNilai['pertanyaan']['daftar_pertanyaan']['indikator_penilaian_id'];
                    $indikator = IndikatorPenilaian::findOrFail($indikatorId)->indikator;
                    $role = $daftarNilai['role_penilai'];
                    $nilai = $logNilai['nilai'];

                    if (!isset($indikatorData[$indikator])) {
                        $indikatorData[$indikator] = [
                            'id' => $indikatorId,
                            'total_nilai' => 0,
                            'total_inputan' => 0,
                            'rata_rata' => 0,
                            'atasan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'sebaya' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'bawahan' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0],
                            'diri sendiri' => ['total_nilai' => 0, 'jumlah_penilai' => 0, 'rata_rata' => 0]
                        ];
                    }

                    if ($role !== 'diri sendiri') {
                        $indikatorData[$indikator]['total_nilai'] += $nilai;
                        $indikatorData[$indikator]['total_inputan']++;
                    }

                    $indikatorData[$indikator][$role]['total_nilai'] += $nilai;
                    $indikatorData[$indikator][$role]['jumlah_penilai']++;
                }
            }

            foreach ($indikatorData as $indikator => &$data) {
                foreach (['atasan', 'sebaya', 'bawahan', 'diri sendiri'] as $role) {
                    if ($data[$role]['jumlah_penilai'] > 0) {
                        $data[$role]['rata_rata'] = round($data[$role]['total_nilai'] / $data[$role]['jumlah_penilai'], 1);
                    }
                }

                $data['rata_rata'] = $data['total_inputan'] > 0 ? $data['total_nilai'] / $data['total_inputan'] : 0;

                $dataDinilai['nilai'][$indikator] = [
                    'id' => $data['id'],
                    'nilai_akhir' => round($data['rata_rata'], 1),
                    'total_nilai' => $data['total_nilai'],
                    'jumlah_penilai' => $data['total_inputan'],
                    'atasan' => $data['atasan'],
                    'sebaya' => $data['sebaya'],
                    'bawahan' => $data['bawahan'],
                    'diri sendiri' => $data['diri sendiri']
                ];
            }

            if ($this->infoPenilaian['jumlahIndikator'] > 0) {
                $rataRataTotal = array_sum(array_column($dataDinilai['nilai'], 'nilai_akhir')) / $this->infoPenilaian['jumlahIndikator'];
            } else {
                $rataRataTotal = 0;
            }
            $dataDinilai['rata_rata_total'] = round($rataRataTotal, 1);

            if (!($dataDinilai['nilai'] == null)) {
                $this->Nilai[] = $dataDinilai;
            }
        }
        // dd($this->Nilai);
    }

    public function exportExcel()
    {
        return Excel::download(new HasilPenilaianExport($this->Nilai, $this->daftarIndikator, $this->infoNilai), 'hasil_penilaian.xlsx');
    }


    public function render()
    {
        if ($this->userRole == 'admin') {
            return view('livewire.hasil-penilaian');
        } else {
            Alert::error('Error', 'Halaman tidak ditemukan!');
            return view('components.error-page');
        }
    }
}
