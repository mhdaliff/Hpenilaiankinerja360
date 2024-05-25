
<div class="container mb-3">
    <div class="row mb-4">
      <h6 class="text-center">Deskripsi Penilaian</h6>
        <div class="col-md-12 mb-2">
          <label for="pilihTimKerja" class="form-label">Tim Kerja</label>
          <select wire:model="penilaian.timKerja" class="form-select" id="pilihTimKerja">
            <option value="">Pilih tim kerja...</option>
            @foreach ($daftarTimKerja as $timKerja)
                <option value="{{$timKerja->id}}">{{$timKerja->nama_tim}}</option>
            @endforeach
          </select>
        </div>
      <div class="col-md-12 mb-2">
        <label for="pilihStruktur" class="form-label">Struktur</label>
        <select wire:model="penilaian.struktur" class="form-select" id="pilihStruktur">
            <option value="">Pilih struktur...</option>
            @foreach ($daftarTimKerja as $timKerja)
                @if($timKerja->id == $penilaian['timKerja'])
                    @foreach ($timKerja->struktur as $struktur)
                        <option value="{{$struktur->id}}">{{$struktur->nama_struktur}}</option>
                    @endforeach
                @endif
            @endforeach 
        </select>
      </div>
    <div class="col-md-12 mb-4">
        <label for="namaTimKerja" class="form-label">Nama Penilaian</label>
        <input wire:model="penilaian.namaPenilaian" type="text" class="form-control" id="deskripsiTimKerja" ></input>
    </div>
    <div class="col-6 col-md-6 px-3 mb-4">
      <h6 class="text-center">Waktu Penilaian</h6>
      <div class="row">
        <div class="col-md-6">
            <label for="mulai" class="form-label">Mulai</label>
            <input wire:model="penilaian.mulai" type="date" class="form-control" id="mulai">
        </div>
        <div class="col-md-6">
            <label for="selesai" class="form-label">Selesai</label>
            <input wire:model="penilaian.selesai" type="date" class="form-control" id="selesai">
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 px-3 mb-4">
      <h6 class="text-center">Beban Responden</h6>
      <div class="row">
        <div class="col-md-6">
          <label for="beban-responden" class="form-label">Beban responden maksimal</label>
          <div class="row">
            <div class="col-12 col-md-6">
              <input wire:model="penilaian.maks-responden" type="number" min="0" class="form-control" id="beban-responden">
            </div>
            <div class="col-12 col-md-6">
              <p><small>responden </small></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-12 px-3">
      <h6 class="text-center">Metode Penentuan Nilai Akhir</h6>
      <div class="row">
        <div class="col-md-12 mb-2">
          <label for="metode" class="form-label">Metode</label>
          <select wire:model="penilaian.metode" class="form-select" id="metode">
              <option value="aritmatika" selected>Aritmatika</option>
              <option value="proporsional">Proporsional</option>
          </select>
        </div>
      </div>
      @if ($penilaian['metode'] == 'proporsional')
        <div class="row">
          <div class="col-md-3">
            <label for="atasan" class="form-label">Atasan</label>
            <input wire:model="penilaian.atasan" type="number" min="0" class="form-control" id="atasan">
          </div>
          <div class="col-md-3">
            <label for="sebaya" class="form-label">Sebaya</label>
            <input wire:model="penilaian.sebaya" type="number" min="0" class="form-control" id="sebaya">
          </div>
          <div class="col-md-3">
            <label for="bawahan" class="form-label">Bawahan</label>
            <input wire:model="penilaian.bawahan" type="number" min="0" class="form-control" id="bawahan">
          </div>
          <div class="col-md-3">
            <label for="diriSendiri" class="form-label">Diri Sendiri</label>
            <input wire:model="penilaian.diriSendiri" type="number" min="0" class="form-control" id="diriSendiri">
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

