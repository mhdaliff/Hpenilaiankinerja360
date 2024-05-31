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
        $this->infoPenilaian['sebaya'] = 35;
        $this->infoPenilaian['bawahan'] = 25;
        $this->infoPenilaian['diriSendiri'] = 0;
        $this->infoPenilaian['metode'] = '';
        $this->infoPenilaian['jumlahIndikator'] = 0;

        // dd($this->infoPenilaian);
        $this->daftarPenilaian = LogPenilaian::where('penilaian_id', $this->idPenilaian)
            ->where('status', 'sudah')
            ->with(['dinilai', 'logNilai.pertanyaan.daftarPertanyaan.indikatorPenilaian'])
            ->get()
            ->groupBy('dinilai_id')
            ->mapWithKeys(function ($item, $key) {
                return [$key => $item];
            });

        // dd($this->daftarPenilaian);


        $this->daftarIndikator();
        $this->nilaiAkhir();
        // dd($this->Nilai);
    }

    public function daftarIndikator()
    {
        $this->daftarIndikator = [];

        foreach ($this->daftarPenilaian as $logPenilaian) {
            foreach ($logPenilaian['0']->logNilai as $logNilai) {
                $indikatorId = $logNilai->pertanyaan->daftarPertanyaan->indikator_penilaian_id;
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
    }

    public function nilaiAkhir()
    {
        $this->Nilai = [];

        // Variabel untuk menyimpan nilai total dan jumlah inputan untuk setiap indikator
        $totalNilaiIndikator = [];
        $totalInputanIndikator = [];

        foreach ($this->daftarPenilaian as $dinilai) {
            $dataDinilai = [
                'dinilai' => $dinilai['0']->dinilai->toArray(),
                'nilai' => []
            ];

            // Menghitung total jumlah nilai dan jumlah inputan untuk setiap indikator
            $totalNilai = [];
            $totalInputan = [];

            foreach ($dinilai as $daftarNilai) {
                foreach ($daftarNilai->logNilai as $logNilai) {
                    $indikatorId = $logNilai->pertanyaan->daftarPertanyaan->indikator_penilaian_id;
                    $indikator = IndikatorPenilaian::findOrFail($indikatorId)->indikator;

                    // Mengakumulasi nilai dan inputan untuk setiap indikator
                    if (!isset($totalNilai[$indikator])) {
                        $totalNilai[$indikator] = 0;
                        $totalInputan[$indikator] = 0;
                    }
                    $totalNilai[$indikator] += $logNilai->nilai;
                    $totalInputan[$indikator]++;

                    // Menyimpan nilai total dan jumlah inputan untuk setiap indikator
                    if (!isset($totalNilaiIndikator[$indikator])) {
                        $totalNilaiIndikator[$indikator] = 0;
                        $totalInputanIndikator[$indikator] = 0;
                    }
                    $totalNilaiIndikator[$indikator] += $logNilai->nilai;
                    $totalInputanIndikator[$indikator]++;
                }
            }

            // Menghitung rata-rata nilai untuk setiap indikator
            foreach ($totalNilai as $indikator => $nilai) {
                $rataRataNilai = $totalInputan[$indikator] > 0 ? round($nilai / $totalInputan[$indikator], 1) : 0;

                $dataDinilai['nilai'][$indikator] = [
                    'id' => $indikatorId,
                    'nilai_akhir' => $rataRataNilai,
                    'total_nilai' => $totalNilai[$indikator],
                ];
            }


            // Menghitung rata-rata total nilai untuk setiap dinilai
            $this->infoPenilaian['jumlahIndikator'] = count($totalNilai);
            // dd($this->infoPenilaian['jumlahIndikator']);
            $rataRataTotal = array_sum(array_column($dataDinilai['nilai'], 'nilai_akhir')) / $this->infoPenilaian['jumlahIndikator'];
            $dataDinilai['rata_rata_total'] = round($rataRataTotal, 1);

            // Memasukkan data dinilai ke dalam array Nilai
            $this->Nilai[] = $dataDinilai;
        }

        // Menghitung nilai rata-rata total untuk tiap-tiap indikator terhadap semua dinilai
        $rataRataTotalIndikator = [];
        foreach ($totalNilaiIndikator as $indikator => $nilai) {
            $rataRataTotalIndikator[$indikator] = $totalInputanIndikator[$indikator] > 0 ? round($nilai / $totalInputanIndikator[$indikator], 1) : 0;
        }
        $this->infoNilai['rerata_indikator'] = $rataRataTotalIndikator;

        // Menghitung rata-rata total untuk semuanya
        $totalDinilai = count($this->Nilai);
        $totalNilai = 0;
        foreach ($this->daftarIndikator as  $indikator) {
            $totalNilai = $totalNilai + $rataRataTotalIndikator[$indikator['nama']];
        }

        $rataRataTotalSemua = $totalNilai / $this->infoPenilaian['jumlahIndikator'];
        $this->infoNilai['rerata_total'] = round($rataRataTotalSemua, 1);

        // dd($this->daftarIndikator);
        // dd($this->infoNilai);
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
