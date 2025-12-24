<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form Persetujuan Dental Implant</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        .title { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .row { margin-bottom: 10px; }
        .signature { margin-top: 50px; }
        img { max-width: 300px; }
    </style>
</head>
<body>

<div class="title">FORM PERSUTUJUAN TINDAKAN DENTAL IMPLANT</div>

<div class="row"><strong>Nama Pasien:</strong> {{ $nama_pasien }}</div>
<div class="row"><strong>Tanggal Lahir:</strong> {{ $tanggal_lahir }}</div>
<div class="row"><strong>Tanggal Kunjungan:</strong> {{ $tanggal_kunjungan }}</div>
<div class="row"><strong>Nama Dokter:</strong> {{ $nama_dokter }}</div>

<div style="display: flex; justify-content: space-between; margin-top: 50px;">

    <div style="text-align: center; width: 30%;">
        <strong>Penerima Informasi</strong><br>
        @if($ttd_penerima_informasi)
            <img src="{{ $ttd_penerima_informasi }}" style="max-width: 100%; height: 150px;">
        @endif
    </div>

    <div style="text-align: center; width: 30%;">
        <strong>Perawat</strong><br>
        @if($ttd_perawat)
            <img src="{{ $ttd_perawat }}" style="max-width: 100%; height: 150px;">
        @endif
    </div>

    <div style="text-align: center; width: 30%;">
        <strong>Dokter</strong><br>
        @if($ttd_dokter)
            <img src="{{ $ttd_dokter }}" style="max-width: 100%; height: 150px;">
        @endif
    </div>

</div>

</body>
</html>
