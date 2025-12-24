<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Form Persetujuan Perawatan Saluran Akar</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 14px; }
        .title { text-align: center; font-weight: bold; margin-bottom: 20px; }
        .row { margin-bottom: 10px; }
        .signature { margin-top: 50px; }
        img { max-width: 300px; }
    </style>
</head>
<body>

<div class="title">FORM PERSUTUJUAN TINDAKAN PERAWATAN SALURAN AKAR</div>

<div class="ttd">
    <strong>Tanda Tangan Penerima Informasi:</strong><br>
    @if($ttd_penerima_informasi)
        <img src="{{ $ttd_penerima_informasi }}" style="max-width:300px;"><br><br>
    @endif

    <strong>Tanda Tangan Dokter:</strong><br>
    @if($ttd_dokter)
        <img src="{{ $ttd_dokter }}" style="max-width:300px;"><br><br>
    @endif

    <strong>Tanda Tangan Perawat:</strong><br>
    @if($ttd_perawat)
        <img src="{{ $ttd_perawat }}" style="max-width:300px;">
    @endif
</div>

</body>
</html>
