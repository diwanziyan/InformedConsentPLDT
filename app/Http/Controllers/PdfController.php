<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use setasign\Fpdi\Tcpdf\Fpdi;
use Carbon\Carbon;

class PdfController extends Controller
{
    public function generate(Request $request)
    {
        /*
        |==================================================
        | 1. VALIDASI FORM
        |==================================================
        */
        $request->validate([
            'jenis_tindakan'              => 'required',
            'nama_penerima_informasi'     => 'required|string|max:100',
            'hubungan_pasien'             => 'required|string',
            'nama_pasien'                 => 'required|string|max:100',
            'tanggal_lahir'               => 'required|date',
            'tanggal_kunjungan'           => 'required|date',
            'nama_dokter'                 => 'required|string',

            'ttd_penerima_informasi'      => 'required',
            'ttd_dokter'                  => 'required',
            'ttd_perawat'                 => 'required',

            'nama_ttd_penerima_informasi' => 'required|string',
            'nama_ttd_dokter'             => 'required|string',
            'nama_ttd_perawat'            => 'required|string',
        ]);

        // Jika hubungan = diri sendiri → nama pasien mengikuti
        if ($request->hubungan_pasien === 'diri sendiri') {
            $request->merge([
                'nama_pasien' => $request->nama_penerima_informasi
            ]);
        }

        /*
        |==================================================
        | 2. TEMPLATE PDF
        |==================================================
        */
        $templates = [
            'perawatan_saluran_akar' => storage_path('app/pdf_templates/perawatan_saluran_akar.pdf'),
            'odontektomi'            => storage_path('app/pdf_templates/odontektomi.pdf'),
            'pemasangan_gts'         => storage_path('app/pdf_templates/pemasangan_gts.pdf'),
            'dental_implant'         => storage_path('app/pdf_templates/dental_implant.pdf'),
            'pemasangan_gtp'         => storage_path('app/pdf_templates/pemasangan_gtp.pdf'),
        ];

        if (!isset($templates[$request->jenis_tindakan])) {
            abort(404, 'Template PDF tidak ditemukan');
        }

        $templatePath = $templates[$request->jenis_tindakan];

        /*
        |==================================================
        | 3. SIMPAN TTD
        |==================================================
        */
        $ttdPenerima = $this->saveSignature($request->ttd_penerima_informasi, 'penerima');
        $ttdDokter   = $this->saveSignature($request->ttd_dokter, 'dokter');
        $ttdPerawat  = $this->saveSignature($request->ttd_perawat, 'perawat');

        /*
        |==================================================
        | 4. GENERATE PDF (HALAMAN 1 & 2)
        |==================================================
        */
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($templatePath);

        /* ================= HALAMAN 1 ================= */
        $pdf->AddPage();
        $pdf->useTemplate($pdf->importPage(1));
        $pdf->SetFont('helvetica', '', 10);

        $pdf->Text(50, 44.5, $request->nama_penerima_informasi);
        $pdf->Text(50, 49.3, $request->hubungan_pasien);
        $pdf->Text(50, 54,   $request->nama_pasien);
        $pdf->Text(50, 58.8, Carbon::parse($request->tanggal_lahir)->format('d-m-Y'));
        $pdf->Text(50, 64,   Carbon::parse($request->tanggal_kunjungan)->format('d-m-Y'));
        $pdf->Text(50, 68,   $request->nama_dokter);

        // TTD halaman 1
        $pdf->Image($ttdPenerima, 30, 196, 40, 20);
        $pdf->Image($ttdPerawat,  83, 196, 40, 20);
        $pdf->Image($ttdDokter,  150,196, 40, 20);

        $pdf->SetFont('helvetica', '', 9);
        $pdf->SetXY(23, 219.3);
        $pdf->Cell(40, 5, $request->nama_ttd_penerima_informasi, 0, 0, 'C');

        $pdf->SetXY(85, 219.3);
        $pdf->Cell(40, 5, $request->nama_ttd_perawat, 0, 0, 'C');

        $pdf->SetXY(144,219.3);
        $pdf->Cell(40, 5, $request->nama_ttd_dokter, 0, 0, 'C');

        /* ================= HALAMAN 2 ================= */
        if ($pageCount >= 2) {
            $pdf->AddPage();
            $pdf->useTemplate($pdf->importPage(2));
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetAutoPageBreak(false);


            $pdf->Text(77, 242, $request->nama_penerima_informasi);
            $pdf->Text(77, 247, $request->hubungan_pasien);

            $pdf->Image($ttdPerawat,  45, 269, 40, 20);
            $pdf->Image($ttdPenerima,124, 269, 40, 20);

            $pdf->SetFont('helvetica', '', 9);
            $pdf->SetXY(46, 281);
            $pdf->Cell(40, 5, $request->nama_ttd_perawat, 0, 0, 'C');

            $pdf->SetXY(120,281);
            $pdf->Cell(40, 5, $request->nama_ttd_penerima_informasi, 0, 0, 'C');
        }

        /*
        |==================================================
        | 5. SIMPAN PDF LOKAL
        |==================================================
        */
        $namaPasien = str_replace(' ', '_', $request->nama_pasien);
        $namaFile   = "form-persetujuan_{$namaPasien}_" . time() . ".pdf";
        $pathPdf    = storage_path("app/public/pdf/{$namaFile}");

        $pdf->Output($pathPdf, 'F');

        /*
        |==================================================
        | 6. KIRIM KE GOOGLE SHEET (OPSIONAL)
        |==================================================
        */
        $linkPdf = url("storage/pdf/{$namaFile}");

        Http::post(env('GOOGLE_SHEET_WEBAPP_URL'), [
            'nama_pasien'        => $request->nama_pasien,
            'tanggal_lahir'      => $request->tanggal_lahir,
            'tanggal_kunjungan'  => $request->tanggal_kunjungan,
            'nama_dokter'        => $request->nama_dokter,
            'jenis_tindakan'     => $request->jenis_tindakan,
            'nama_file'          => $namaFile,
            'link_pdf'          => $linkPdf,
        ]);

        /*
        |==================================================
        | 7. HAPUS FILE TTD SEMENTARA
        |==================================================
        */
        @unlink($ttdPenerima);
        @unlink($ttdDokter);
        @unlink($ttdPerawat);

        /*
        |==================================================
        | 8. TAMPILKAN PDF
        |==================================================
        */
        return response()->file($pathPdf);
    }

    private function saveSignature($base64, $role)
    {
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $image  = base64_decode($base64);

        $dir = storage_path('app/temp');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $path = "{$dir}/ttd_{$role}.png";
        file_put_contents($path, $image);

        return $path;
    }
}
