<x-slot name="breadcumb">
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="{{Route('penilaian')}}">Penilaian</a>
    </li>
    <li class="breadcrumb-item text-md">
        <a class="opacity-5 text-dark" href="/penilaian/{{$idPenilaian}}">Detail Penilaian</a>
    </li>
    <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">
        {{ str_replace('-', ' ', Route::currentRouteName()) }}
    </li>
</x-slot>

<div>
    <div class="container-fluid">
        <div class="page-header min-height-300 border-radius-xl mt-4"
            style="background-image: url('../assets/img/curved-images/curved0.jpg'); background-position-y: 50%;">
            <span class="mask bg-gradient-primary opacity-6"></span>
        </div>
        <div class="card card-body  mx-4 mt-n12">
            <div class="row gx-4">
                <div class="col-auto">
                    <div class="avatar avatar-xl position-relative">
                    @if($infoDinilai->photo_path)
                        <img src="{{ Str::startsWith($infoDinilai->photo_path, 'http') ? $infoDinilai->photo_path : asset('storage/' . $infoDinilai->photo_path) }}" alt="..." class="w-100 border-radius-lg shadow-sm">
                    @else
                        <img src="../assets/img/team-4.jpg" alt="Default Image" class="w-100 border-radius-lg shadow-sm">
                    @endif
                    </div>
                </div>
                <div class="col-auto my-auto">
                    <div class="h-100">
                        <h5 class="mb-1">{{ $infoDinilai->name }}</h5>
                        <p class="mb-0 font-weight-bold text-sm">{{ $infoDinilai->email }}</p>
                    </div>
                </div>
            </div>
        </div>
        @foreach ($indikatorPenilaian as $key => $indikator)
            @if ($key + 1 === $currentPage)
                <div class="card card-body mx-4 mt-2">
                    <div class="row gx-4">
                        <div class="col-12 col-md-12 text-center mb-2 mt-2">
                            <h5 class=" font-weight-bold">Halaman ({{ $currentPage}}/{{$maxPage}})</h5>
                        </div>
                        <div class="col-12 col-md-12 text-center mb-5">
                            <h5 class=" font-weight-bold">{{$indikator['indikator']}}</h5>
                        </div>
                        @foreach ($indikator['0'] as $index => $pertanyaan)
                            <div class="col-md-12 px-4">
                                <h6 class="text-start px-4">{{ $pertanyaan['pertanyaan'] }}</h6>
                                <div class="card card-body mb-5 flex-1 flex p-2 text-black dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                                    <div class="row">
                                        <div class="col-4 col-md-2">
                                            <p class="text-end py-3">Sangat tidak baik</p>
                                        </div>
                                        <div class="col-8 col-md-8">
                                            <div class="row justify-content-center">
                                                @for ($i = 1; $i <= 10; $i++)
                                                <div class="col-md-1">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="form-check form-check-inline">
                                                                <input wire:model="indikatorPenilaian.{{ $key }}.0.{{ $index }}.nilai" class="form-check-input" type="radio" id="nilai_{{ $key }}_{{ $index }}_{{ $i }}" value="{{ $i }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-check-label" for="nilai_{{ $loop->parent->index }}_{{ $i }}">{{ $i }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="col-4 col-md-2">
                                            <p class="text-start py-3">Sangat baik</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
        <!-- Tombol navigasi -->
        <div class="d-flex justify-content-between mt-4 px-5">
            <button wire:click="back" class="btn bg-gradient-secondary" @if($currentPage == 1) disabled @endif>Kembali</button>
            @if ($currentPage == $maxPage)
                <button wire:click="save" class="btn bg-gradient-primary">Simpan</button>
            @else
                <button wire:click="next" class="btn bg-gradient-primary">Lanjut</button>
            @endif
        </div>
    </div>
</div>

<script>
    // // Fungsi untuk menangkap nilai yang dipilih oleh pengguna
    document.addEventListener('change', function (event) {
        if (event.target.type === 'radio') {
            // Tangkap nilai dari radio button yang dipilih
            var nilai = event.target.value;
            // Tangkap ID dari radio button
            var id = event.target.id.split('_').slice(1).join('_');
            // Tampilkan nilai yang dipilih
            console.log("Question ID:", id);
            console.log("Selected value:", nilai);
            // Simpan nilai dalam variabel JavaScript, atau kirim ke server
            // Misalnya, simpan nilai dalam objek JavaScript
            var savedValues = savedValues || {};
            savedValues[id] = nilai;
            console.log("Saved values:", savedValues);
        }
    });

    document.addEventListener('livewire:load', function () {
            @this.on('showToast', (message, type) => {
                toast(message, type);
            });
        });

    document.addEventListener('DOMContentLoaded', function () {
        window.addEventListener('swal:warning', event => {
            swal("warning", event.detail.message, "warning");
        });
    });
</script>
