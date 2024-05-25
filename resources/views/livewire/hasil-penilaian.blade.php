<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('hasil-penilaian')}}">Hasil Penilaian</a>
    </li>
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4 px-4">
                <div class="card-header pb-0 mb-4">
                    <div class="container text-center">
                        <h4 class=" font-weight-bolder">{{$infoPenilaian->nama_penilaian}}</h4>
                    </div>
                </div>
                <div class="card-body px-2 pt-0 pb-2">
                    <div class="row">
                        <div class="col-12 col-md-12 mb-4">
                            <h5 class="text-center">Hasil Penilaian</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
