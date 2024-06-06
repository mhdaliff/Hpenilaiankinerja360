<x-slot name="breadcumb">
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <div class="d-flex flex-row justify-content-between position-relative">
                            <div class="container-fluid text-center">
                                <h5 class="mb-0 font-weight-bolder">Daftar Hasil Penilaian</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($daftarTimKerja as $timKerja)
                                @foreach ($timKerja->struktur as $struktur)
                                    @foreach ($struktur->penilaian as $penilaian)
                            <div class="col-md-6 px-3 pt-3 pb-3 sidenav-footer">
                                <div class="card card-background move-on-hover shadow-none card-background-mask-warning" id="sidenavCard">
                                    <div class="full-background" style="background-image: url('../assets/img/curved-images/white-curved.jpeg')">
                                    </div>
                                    <div class="card-body pt-2 w-100">
                                        <div class="row">
                                            <div class="col-md-12 d-flex flex-row justify-content-between">
                                                <span class="text-white text-uppercase text-sm font-weight-bold my-2">{{ $timKerja->nama_tim }}</span>
                                            </div>
                                            <a href="/hasil-penilaian/{{$penilaian->id}}" class="card-title h4 d-block text-white mb-3">
                                                {{ $penilaian->nama_penilaian }}
                                            </a>
                                            <a href="/hasil-penilaian/{{$penilaian->id}}" class="btn btn-white btn-sm w-100" type="button">Detail</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                    @endforeach
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>