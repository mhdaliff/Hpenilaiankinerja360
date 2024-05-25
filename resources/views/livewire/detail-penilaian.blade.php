<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('penilaian')}}">Penilaian</a>
    </li>
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4 px-4">
                    <div class="card-header pb-0 mb-4">
                        <div class="d-flex flex-row justify-content-between position-relative">
                            <div class="container-fluid text-center">
                                <h4 class=" font-weight-bolder">{{$infoPenilaian['nama_penilaian']}}</h4>
                            </div>
                            @if ($userRole == 'admin')
                                <a href="/monitoring/{{$idPenilaian}}" class="btn bg-gradient-primary btn-sm position-absolute top-0 end-0" type="button">Monitoring</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body px-2 pt-0 pb-2">
                        <div class="row justify-content-center">
                            {{-- <div class="col-12 col-md-12">
                                <p class="text-center">Progres Penilaian</p>
                            </div> --}}
                            {{-- <div class="col-12  col-md-6 mb-4">
                                <div class="chart">
                                    <canvas id="chart-pie" class="chart-canvas" height="150px"></canvas>
                                </div>
                            </div> --}}
                            <div class="col-12 col-md-8 px-3 mb-5"> 
                                <div class="progress-wrapper">
                                    <div class="progress-info">
                                    <div class="progress-percentage mb-2">
                                        <span class="text-sm font-weight-bold">{{$dataNilaiUser['sudah']}} dari {{$dataNilaiUser['total']}} sudah dinilai ({{$dataNilaiUser['persen']}}%)</span>
                                    </div>
                                    </div>
                                    <div class="progress">
                                    <div class="progress-bar bg-gradient-primary" role="progressbar" aria-valuenow="{{$dataNilaiUser['persen']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$dataNilaiUser['persen']}}%;"></div>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-12 col-md-12 mb-4">
                                <div class="row align-center">
                                    <div class="col-12 col-md-12 px-3 mb-5"> 
                                        <div class="progress-wrapper">
                                            <div class="progress-info">
                                            <div class="progress-percentage mb-2">
                                                <span class="text-sm font-weight-bold">{{$dataNilaiUser['sudah']}} dari {{$dataNilaiUser['total']}} sudah dinilai ({{$dataNilaiUser['persen']}}%)</span>
                                            </div>
                                            </div>
                                            <div class="progress">
                                            <div class="progress-bar bg-gradient-primary" role="progressbar" aria-valuenow="{{$dataNilaiUser['persen']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$dataNilaiUser['persen']}}%;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-12 px-6 mb-4">
                                        <div class="table-responsive">
                                            <table class="table align-items-center mb-0">
                                                <thead>
                                                    <tr>
                                                        <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                            Status Nilai
                                                        </td>
                                                        <td class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                            Jumlah
                                                        </td>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="align-middle">
                                                            <p class="text-center text-xs font-weight-bold mb-0">Belum</p>
                                                        </td>
                                                        <td class="algin middle">
                                                            <p class="text-center text-xs font-weight-bold mb-0">{{ $dataNilaiUser['belum']}}</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="align-middle">
                                                            <p class="text-center text-xs font-weight-bold mb-0">Sudah</p>
                                                        </td>
                                                        <td class="algin middle">
                                                            <p class="text-center text-xs font-weight-bold mb-0">{{ $dataNilaiUser['sudah']}}</p>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div> --}}

                            {{-- @if ($userRole == 'admin')
                                <div class="nav-wrapper position-relative mb-4">
                                    <ul class="nav nav-pills nav-fill p-1" role="tablist">
                                        <li class="nav-item">
                                            <button class="nav-link mb-0 px-0 py-1 {{ $activeTab == 'monitoring' ? 'active btn btn-white h-100' : '' }}" wire:click="changeTab('monitoring')" role="tab" aria-controls="monitoring" aria-selected="{{ $activeTab == 'monitoring' ? 'true' : 'false' }}">
                                                Monitoring
                                            </button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link mb-0 px-0 py-1 {{ $activeTab == 'daftar-penilaian' ? 'active btn btn-white h-100' : '' }}" wire:click="changeTab('daftar-penilaian')" role="tab" aria-controls="daftar-penilaian" aria-selected="{{ $activeTab == 'daftar-penilaian' ? 'true' : 'false' }}">
                                                Daftar Penilaian
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            @endif --}}

                            <!-- ISI NAV STRUKTUR TIM KERJA  -->
                            <div class="col-md-12 tab-content">
                                <div class="tab-pane fade show {{ $activeTab == 'daftar-penilaian' ? 'active' : '' }}" id="daftar-penilaian" role="tabpanel" aria-labelledby="daftar-penilaian-tab">
                                    <!-- Konten Daftar Penilaian -->
                                    <div class="d-flex flex-row justify-content-between position-relative mb-4">
                                        <div class="container-fluid text-center">
                                            <h5>Daftar Penilaian</h5>
                                        </div>
                                    </div>
                                    @php
                                        $roles = ['atasan', 'sebaya', 'bawahan', 'diri sendiri'];
                                    @endphp

                                    @foreach ($roles as $role)
                                        @php
                                        // Filter berdasarkan peran
                                        if ($role == 'atasan') {
                                            $roleFilter = 'bawahan'; // Atasan mengarahkan ke bawahan
                                        } elseif ($role == 'bawahan') {
                                            $roleFilter = 'atasan'; // Bawahan mengarahkan ke atasan
                                        } else {
                                            $roleFilter = $role;
                                        }
                            
                                        $dinilaiByRole = $daftarDinilai->filter(function ($item) use ($roleFilter) {
                                            return $item['role_penilai'] == $roleFilter;
                                        });
                                        @endphp

                                        @if ($dinilaiByRole->isNotEmpty())
                                            <div class="col-md-12 mb-4">
                                                <h6 class="text-center">{{ ucfirst($role) }}</h6>
                                            </div>

                                            <div class="row justify-content-center text-center">
                                                @foreach ($dinilaiByRole as $dinilai)
                                                    <div class="col-md-2 mb-4">
                                                        <div class="card">
                                                            <div class="card-header p-0 mx-3 mt-3 position-relative z-index-1">
                                                                <a href="{{ route('nilai', ['id' => $dinilai->id]) }}" class="d-block">
                                                                    @if($dinilai->dinilai->photo_path)
                                                                        <img src="{{ Str::startsWith($dinilai->dinilai->photo_path, 'http') ? $dinilai->dinilai->photo_path : asset('storage/' . $dinilai->dinilai->photo_path) }}" alt="..." class="w-100 border-radius-lg shadow-sm">
                                                                    @else
                                                                        <img src="../assets/img/team-4.jpg" alt="Default Image" class="w-100 border-radius-lg shadow-sm">
                                                                    @endif
                                                                </a>
                                                            </div>
                                                            <div class="card-body pt-2">
                                                                <div class="row">
                                                                    <div class="col-12 col-md-12">
                                                                        @if ($dinilai->status == 'belum')
                                                                            <span class="text-gradient text-danger text-uppercase text-xs font-weight-bold my-2">belum dinilai</span>
                                                                        @else
                                                                            <span class="text-gradient text-success text-uppercase text-xs font-weight-bold my-2">sudah dinilai</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-12 col-md-12">
                                                                        <a href="javascript:;" class="card-description text-darker">
                                                                            <small> {{ $dinilai->dinilai->name }} </small>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="tab-pane fade show {{ $activeTab == 'monitoring' ? 'active' : '' }}" id="monitoring" role="tabpanel" aria-labelledby="monitoring-tab">
                                    <!-- Konten Monitoring -->
                                    <div class="d-flex flex-row justify-content-between position-relative mb-4">
                                        <div class="container-fluid text-center">
                                            <h5>Monitoring</h5>
                                        </div>
                                    </div>
                                    <div class="row">
                                        
                                        <div class="col-12 col-md-6">
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>


<!--   Core JS Files   -->
<script src="/assets/js/plugins/chartjs.min.js"></script>
<script src="/assets/js/plugins/Chart.extension.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil data dari atribut Livewire
        var jumlahBelumMenilai = @json($dataNilaiUser['belum']);
        var jumlahSudahMenilai = @json($dataNilaiUser['sudah']);

        // Inisialisasi pie chart
        var ctx = document.getElementById('chart-pie').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Belum', 'Sudah'],
                datasets: [{
                    data: [jumlahBelumMenilai, jumlahSudahMenilai],
                    backgroundColor: ['#ff6384', '#36a2eb'],
                    hoverBackgroundColor: ['#ff6384', '#36a2eb'],
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.raw;
                                return label;
                            }
                        }
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true
                }
            }
        });
    });
</script>