<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Persetujuan Tindakan</title>

    <!-- CSS Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- JS Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; }
        h2 { text-align: center; margin-top: 20px; }
        form { max-width: 500px; margin: 30px auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        label { font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ccc; }
        button { padding: 10px 20px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
    </style>
</head>
<body>

<h2>Form Persetujuan Tindakan</h2>

<form method="POST" action="/form-persetujuan" target="blank">
    @csrf

    <label>Nama Penerima Informasi</label>
    <input type="text" name="nama_penerima_informasi" id="nama_penerima_informasi" required>

    <label>Hubungan dengan Pasien</label>
    <select name="hubungan_pasien" id="hubungan_pasien" required>
        <option value="">-- Pilih Hubungan --</option>
        <option value="diri sendiri">Diri Sendiri</option>
        <option value="orang tua">Orang Tua</option>
        <option value="anak">Anak</option>
        <option value="kerabat">Kerabat</option>
    </select>

    <label>Nama Pasien</label>
    <input type="text" name="nama_pasien" id="nama_pasien" required>


    <label>Tanggal Lahir</label>
    <input type="date" name="tanggal_lahir" required>

    <label>Tanggal Kunjungan</label>
    <input type="date" name="tanggal_kunjungan" required>

    <label>Nama Dokter</label>
    <select name="nama_dokter" id="nama_dokter" required>
        <option value="">-- Pilih Dokter --</option>
        <option value="Budi">Budi</option>
        <option value="Andi">Andi</option>
        <option value="Sari">Sari</option>
        <option value="Rina">Rina</option>
        <option value="Agus">Agus</option>
    </select>

    <label>Jenis Tindakan</label>
        <select name="jenis_tindakan">
        <option value="perawatan_saluran_akar">Perawatan Saluran Akar</option>
        <option value="odontektomi">Odontektomi</option>
        <option value="pemasangan_gts">Pemasangan GTS</option>
        <option value="dental_implant">Dental Implant</option>
        <option value="pemasangan_gtp">Pemasangan GTP</option>
    </select>


    <label>Tanda Tangan Penerima Informasi</label>
    <canvas id="canvas_penerima_informasi" width="400" height="150" style="border:1px solid #ccc;"></canvas>
    <button type="button" id="clear_penerima_informasi">Clear</button>
    <input type="hidden" name="ttd_penerima_informasi" id="ttd_penerima_informasi">

    <label>Tanda Tangan Dokter</label>
    <canvas id="canvas_dokter" width="400" height="150" style="border:1px solid #ccc;"></canvas>
    <button type="button" id="clear_dokter">Clear</button>
    <input type="hidden" name="ttd_dokter" id="ttd_dokter">

    <label>Tanda Tangan Perawat</label>
    <canvas id="canvas_perawat" width="400" height="150" style="border:1px solid #ccc;"></canvas>
    <button type="button" id="clear_perawat">Clear</button>
    <input type="hidden" name="ttd_perawat" id="ttd_perawat">



    <button type="submit">Submit</button>
</form>

<script>
    $(document).ready(function() {
        $('#nama_dokter').select2({
            placeholder: "Cari dan pilih dokter",
            allowClear: true
        });
    });
</script>

<script>
function setupCanvas(canvasId, hiddenId, clearId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    let painting = false;

    function start(e) { painting = true; draw(e); }
    function end() { 
        painting = false; 
        ctx.beginPath(); 
        document.getElementById(hiddenId).value = canvas.toDataURL();
    }
    function draw(e) {
        if(!painting) return;
        const rect = canvas.getBoundingClientRect();
        ctx.lineWidth = 2;
        ctx.lineCap = "round";
        ctx.strokeStyle = "black";
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    }

    canvas.addEventListener('mousedown', start);
    canvas.addEventListener('mouseup', end);
    canvas.addEventListener('mousemove', draw);

    document.getElementById(clearId).addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        document.getElementById(hiddenId).value = '';
    });
}

// Pasien
setupCanvas('canvas_penerima_informasi', 'ttd_penerima_informasi', 'clear_penerima_informasi');
// Dokter
setupCanvas('canvas_dokter', 'ttd_dokter', 'clear_dokter');
// Perawat
setupCanvas('canvas_perawat', 'ttd_perawat', 'clear_perawat');
</script>

<script>
    const hubunganSelect = document.getElementById('hubungan_pasien');
    const namaPenerima   = document.getElementById('nama_penerima_informasi');
    const namaPasien     = document.getElementById('nama_pasien');

    function syncNama() {
        if (hubunganSelect.value === 'diri sendiri') {
            namaPasien.value = namaPenerima.value;
            namaPasien.readOnly = true; // biar ga diedit
        } else {
            namaPasien.readOnly = false;
        }
    }

    hubunganSelect.addEventListener('change', syncNama);

    namaPenerima.addEventListener('input', () => {
        if (hubunganSelect.value === 'diri sendiri') {
            namaPasien.value = namaPenerima.value;
        }
    });
</script>


</body>
</html>
