<x-slot name="breadcumb">
  <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
      {{ str_replace('-', ' ', Route::currentRouteName()) }}
  </li>
</x-slot>

 <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-0">
              <div class="row">
                <div class="col-auto mx-2">
                  <div class="avatar avatar-xl position-relative">
                    @if($user->photo_path)
                    <img src="{{ Str::startsWith($user->photo_path, 'http') ? $user->photo_path : asset('storage/' . $user->photo_path) }}" alt="..." class="w-100 border-radius-lg shadow-sm">
                    @else
                    <img src="../assets/img/team-4.jpg" alt="Default Image" class="avatar avatar-sm me-3">
                    @endif
                    {{-- <img src="{{$Penilai['penilai']->photo_path}}" class="avatar avatar-sm me-3"> --}}
                  </div>
                </div>
                <div class="col-auto my-auto">
                  <div class="h-100">
                    <h5 class="mb-1">{{$user->name}}</h5>
                    <p class="mb-0 font-weight-bold text-sm">{{$user->email}}</p>
                  </div>
              </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-4 text-start mx-0 px-3">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-paper-diploma text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
                <div class="col-5 px-0">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Penilaian Berlangsung</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="number">
                    <h3 class="font-weight-bolder text-capitalize mb-0 text-center">
                    {{count($daftarPenilaian)}}
                    </h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-4 text-start">
                  <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                    <i class="ni ni-check-bold text-lg opacity-10" aria-hidden="true"></i>
                  </div>
                </div>
                <div class="col-5 px-0">
                  <div class="numbers">
                    <p class="text-sm mb-0 text-capitalize font-weight-bold">Penilaian diikuti</p>
                  </div>
                </div>
                <div class="col-3">
                  <div class="number">
                    <h3 class="font-weight-bolder text-capitalize mb-0 text-center">
                      2
                    </h3>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-lg-4 mb-4">
          <div class="card">
            <div class="card-header pb-0 text-center">
              <h6>Hasil Penilaian</h6>
              <p class="text-sm">
                Evaluasi Pra-pencacahan
              </p>
            </div>
            <div class="card-body p-3">
              <div class="chart">
                <canvas id="chart-radar" class="chart-canvas" height="300px"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8 mb-4">
          <div class="card">
            <div class="card-header pb-0 text-center">
              <h6>Penilaian Berlangsung</h6>
            </div>
            <div class="card-body p-3">
              <div class="row">
                <div class="col-12 col-md-12 mb-4">
                  <div class="table-responsive">
                      <table class="table align-items-center mb-0">
                          <thead>
                              <tr>
                                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Penilaian</th>  
                                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Proses</th>
                                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Batas Waktu</th>
                                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"></th>
                              </tr>
                          </thead>
                          <tbody>
                            @foreach ($daftarPenilaian as $penilaian)
                              <tr>
                                <td class="align-middle">
                                    <p class="text-center text-xs font-weight-bold mb-0">{{$loop->iteration}}</p>
                                </td>
                                <td>
                                  <div class="d-flex px-2 py-1">
                                    <a href="/penilaian/{{$penilaian->id}}">
                                    <div>
                                    <div class="d-flex flex-column justify-content-center">
                                      <h6 class="mb-0 text-xs">{{$penilaian->nama_penilaian}}</h6>
                                      <p class="text-xs text-secondary mb-0">{{$penilaian->struktur->timkerja->nama_tim}}</p>
                                    </div>
                                  </div>
                                  </a>
                                </td>
                                <td class="align-middle">
                                  <div class="col-12 col-md-12"> 
                                    <div class="progress-wrapper">
                                        <div class="progress-info">
                                          <div class="progress-percentage mb-2 text-center">
                                            <p class="text-xs text-secondary mb-0">{{$penilaian->dataNilaiUser['sudah']}} dari {{$penilaian->dataNilaiUser['total']}} sudah dinilai ({{$penilaian->dataNilaiUser['persen']}}%)</p>
                                          </div>
                                        </div>
                                        <div class="progress w-100">
                                          <div class="progress-bar bg-gradient-primary" role="progressbar" aria-valuenow="{{$penilaian->dataNilaiUser['persen']}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$penilaian->dataNilaiUser['persen']}}%;">
                                          </div>
                                        </div>
                                    </div>
                                </div>
                                </td>
                                <td class="algin middle">
                                    <p class="text-center text-xs font-weight-bold mb-0">{{$penilaian->deadlinePenilaian}}</p>
                                </td>
                                <td class="d-flex justify-content-center align-items-center mb-0 py-3">
                                  <a href="/penilaian/{{$penilaian->id}}" class="btn bg-gradient-danger btn-sm disable mb-0" role="button" aria-pressed="true">Nilai</a>
                                  {{-- <a href="/penilaian/{{$penilaian->id}}">
                                    <p class="text-center text-xs font-weight-bold mb-0">Nilai</p>
                                  </a> --}}
                                </td>
                              </tr>
                            @endforeach
                          </tbody>
                      </table>
                  </div>
              </div>

                {{-- <div class="col-md-6 px-3 pt-3 pb-3 sidenav-footer">
                    <div class="card card-background shadow-none card-background-mask-warning" id="sidenavCard">
                        <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpeg')">
                        </div>
                        <div class="card-body pt-2">
                            <div class="row">
                                <div class="col-md-12 d-flex flex-row justify-content-between">
                                    <span class="text-white text-uppercase text-sm font-weight-bold my-2">{{ $penilaian->struktur->nama_struktur }}</span>
                                    <span class="text-white text-uppercase text-sm text-uppercase font-weight-bold my-2">
                                        @php
                                            $today = now();
                                            if ($today < $penilaian->waktu_mulai) {
                                                $status = "Belum Mulai";
                                            } elseif ($today >= $penilaian->waktu_mulai && $today <= $penilaian->waktu_selesai) {
                                                $status = "Berlangsung";
                                            } else {
                                                $status = "Selesai";
                                            }
                                            echo $status;
                                        @endphp
                                    </span>
                                </div>
                                <a href='/penilaian/{{$penilaian->id}}' class="card-title h4 d-block text-white mb-3">
                                  {{ $penilaian->nama_penilaian }}
                                </a>
                                <p class="text-white text-sm text-uppercase font-weight-bold">
                                  {{ count($penilaian->anggotaTimKerja) }} Orang Anggota
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
              </div> --}}
            </div>
          </div>
        </div>
        {{-- <div class="col-lg-8 mb-4">
          <div class="card">
            <div class="card-header pb-0">
              <h6>Hasil Penilaian</h6>
              <p class="text-sm">
                <i class="fa fa-arrow-up text-success"></i>
                <span class="font-weight-bold">4% more</span> in 2021
              </p>
            </div>
            <div class="card-body p-3">
              <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="300px"></canvas>
              </div>
            </div>
          </div>
        </div> --}}
      </div>
    </div>
  </main>

  <!--   Core JS Files   -->
  <script src="/assets/js/plugins/chartjs.min.js"></script>
  <script src="/assets/js/plugins/Chart.extension.js"></script>
  <script>
    // var ctx = document.getElementById("chart-bars").getContext("2d");

    // new Chart(ctx, {
    //   type: "bar",
    //   data: {
    //     labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    //     datasets: [{
    //       label: "Sales",
    //       tension: 0.4,
    //       borderWidth: 0,
    //       pointRadius: 0,
    //       backgroundColor: "#fff",
    //       data: [450, 200, 100, 220, 500, 100, 400, 230, 500],
    //       maxBarThickness: 6
    //     }, ],
    //   },
    //   options: {
    //     responsive: true,
    //     maintainAspectRatio: false,
    //     legend: {
    //       display: false,
    //     },
    //     tooltips: {
    //       enabled: true,
    //       mode: "index",
    //       intersect: false,
    //     },
    //     scales: {
    //       yAxes: [{
    //         gridLines: {
    //           display: false,
    //         },
    //         ticks: {
    //           suggestedMin: 0,
    //           suggestedMax: 500,
    //           beginAtZero: true,
    //           padding: 0,
    //           fontSize: 14,
    //           lineHeight: 3,
    //           fontColor: "#fff",
    //           fontStyle: 'normal',
    //           fontFamily: "Open Sans",
    //         },
    //       }, ],
    //       xAxes: [{
    //         gridLines: {
    //           display: false,
    //         },
    //         ticks: {
    //           display: false,
    //           padding: 20,
    //         },
    //       }, ],
    //     },
    //   },
    // });

    // var ctx2 = document.getElementById("chart-line").getContext("2d");

    // var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);

    // gradientStroke1.addColorStop(1, 'rgba(253,235,173,0.4)');
    // gradientStroke1.addColorStop(0.2, 'rgba(245,57,57,0.0)');
    // gradientStroke1.addColorStop(0, 'rgba(255,214,61,0)'); //purple colors

    // var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);

    // gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.4)');
    // gradientStroke2.addColorStop(0.2, 'rgba(245,57,57,0.0)');
    // gradientStroke2.addColorStop(0, 'rgba(255,214,61,0)'); //purple colors


    // new Chart(ctx2, {
    //   type: "line",
    //   data: {
    //     labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
    //     datasets: [{
    //         label: "Mobile apps",
    //         tension: 0.4,
    //         borderWidth: 0,
    //         pointRadius: 0,
    //         borderColor: "#fbcf33",
    //         borderWidth: 3,
    //         backgroundColor: gradientStroke1,
    //         data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
    //         maxBarThickness: 6

    //       },
    //       {
    //         label: "Websites",
    //         tension: 0.4,
    //         borderWidth: 0,
    //         pointRadius: 0,
    //         borderColor: "#f53939",
    //         borderWidth: 3,
    //         backgroundColor: gradientStroke2,
    //         data: [30, 90, 40, 140, 290, 290, 340, 230, 400],
    //         maxBarThickness: 6

    //       },
    //     ],
    //   },
    //   options: {
    //     responsive: true,
    //     maintainAspectRatio: false,
    //     legend: {
    //       display: false,
    //     },
    //     tooltips: {
    //       enabled: true,
    //       mode: "index",
    //       intersect: false,
    //     },
    //     scales: {
    //       yAxes: [{
    //         gridLines: {
    //           borderDash: [2],
    //           borderDashOffset: [2],
    //           color: '#dee2e6',
    //           zeroLineColor: '#dee2e6',
    //           zeroLineWidth: 1,
    //           zeroLineBorderDash: [2],
    //           drawBorder: false,
    //         },
    //         ticks: {
    //           suggestedMin: 0,
    //           suggestedMax: 500,
    //           beginAtZero: true,
    //           padding: 10,
    //           fontSize: 11,
    //           fontColor: '#adb5bd',
    //           lineHeight: 3,
    //           fontStyle: 'normal',
    //           fontFamily: "Open Sans",
    //         },
    //       }, ],
    //       xAxes: [{
    //         gridLines: {
    //           zeroLineColor: 'rgba(0,0,0,0)',
    //           display: false,
    //         },
    //         ticks: {
    //           padding: 10,
    //           fontSize: 11,
    //           fontColor: '#adb5bd',
    //           lineHeight: 3,
    //           fontStyle: 'normal',
    //           fontFamily: "Open Sans",
    //         },
    //       }, ],
    //     },
    //   },
    // });

    document.addEventListener("DOMContentLoaded", function() {
    var ctx3 = document.getElementById("chart-radar").getContext("2d");
    new Chart(ctx3,{
      type: 'radar',
      data: {
        labels: [
          'Kedisiplinan ',
          'Kepemimpinan',
          'Kerja Tim',
          'Manajemen Diri',
          'Komunikasi',
          'Pengalaman',
          'Adaptasi'
        ],
        datasets: [{
          label: 'Diri Sendiri' ,
          data: [8.4, 8, 8.6, 8.1, 8.6, 8.5, 0],
          fill: true,
          backgroundColor: 'rgba(37,47,64, 0.2)',
          borderColor: 'rgb(37,47,64)',
          pointBackgroundColor: 'rgb(37,47,64)',
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: 'rgb(37,47,64)'
        }, {
          label: 'Orang lain',
          data: [8.8, 8.8, 8.0, 8.9, 9.6, 8.7, 10.0],
          fill: true,
          backgroundColor: 'rgba(255,49,49, 0.2)',
          borderColor: 'rgb(255,49,49)',
          pointBackgroundColor: 'rgb(255,49,49)',
          pointBorderColor: '#fff',
          pointHoverBackgroundColor: '#fff',
          pointHoverBorderColor: 'rgb(255,49,49)'
        }]
      },
      options: {
        elements: {
            line: {
              borderWidth: 3
            }
          },
        scales: {
          r: {
              angleLines: {
                  display: false
              },
              suggestedMin: 0,
              suggestedMax: 10
          }
        }
      },
    });
  });
  </script>
