<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Tcpdf\Fpdi;
use Carbon\Carbon;

class PdfController extends Controller
{
    
    public function generate(Request $request)
{
    
    // =====================
        // 1. VALIDASI REQUEST (NO.1)
        // =====================
        $request->validate([
            'jenis_tindakan'            => 'required',
            'nama_penerima_informasi'   => 'required|string|max:100',
            'hubungan_pasien'           => 'required|string',
            'nama_pasien'               => 'required|string|max:100',
            'tanggal_lahir'             => 'required|date',
            'tanggal_kunjungan'         => 'required|date',
            'nama_dokter'               => 'required|string',

            // TTD YANG BENAR
            'ttd_penerima_informasi'    => 'required',
            'ttd_dokter'                => 'required',
            'ttd_perawat'               => 'required',

            // Nama TTD
              'nama_ttd_penerima_informasi' => 'required|string',
              'nama_ttd_dokter'             => 'required|string',
              'nama_ttd_perawat'            => 'required|string',
        ]);
        if ($request->hubungan_pasien === 'diri sendiri') {
            $request->merge([
                'nama_pasien' => $request->nama_penerima_informasi
            ]);
        }

        // =====================
        // 2. MAPPING TEMPLATE PDF
        // =====================
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

        // =====================
        // 3. LABEL TINDAKAN
        // =====================
        $labelTindakan = [
            'perawatan_saluran_akar' => 'PERAWATAN SALURAN AKAR',
            'odontektomi'            => 'ODONTEKTOMI',
            'pemasangan_gts'         => 'PEMASANGAN GTS',
            'dental_implant'         => 'DENTAL IMPLANT',
            'pemasangan_gtp'         => 'PEMASANGAN GTP',
        ];

        // =====================
        // 4. DECODE TANDA TANGAN
        // =====================
        $ttdPenerimaInformasi  = $this->saveSignature($request->ttd_penerima_informasi, 'penerima_informasi');
        $ttdDokter  = $this->saveSignature($request->ttd_dokter, 'dokter');
        $ttdPerawat = $this->saveSignature($request->ttd_perawat, 'perawat');

        // =====================
        // 5. BUAT PDF DARI TEMPLATE
        // =====================
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($templatePath);

        /*
        |--------------------------------------------------------------------------
        | HALAMAN 1
        |--------------------------------------------------------------------------
        */
        $pdf->AddPage();
        $page1 = $pdf->importPage(1);
        $pdf->useTemplate($page1);

        $pdf->SetFont('helvetica', '', 10);

        // =====================
        // ISI DATA HALAMAN 1
        // =====================
        $pdf->Text(50, 44.5, $request->nama_penerima_informasi);
        $pdf->Text(50, 49.3, $request->hubungan_pasien);
        $pdf->Text(50, 54, $request->nama_pasien);
        $pdf->Text(50, 58.8, Carbon::parse($request->tanggal_lahir)->format('d-m-Y'));
        $pdf->Text(50, 64, Carbon::parse($request->tanggal_kunjungan)->format('d-m-Y'));
        $pdf->Text(50, 68, $request->nama_dokter);


        // =====================
        // TTD HALAMAN 1
        // =====================
        $pdf->Image($ttdPenerimaInformasi, 30, 196, 40, 20);
        $pdf->Image($ttdPerawat, 83, 196, 40, 20);
        $pdf->Image($ttdDokter, 150, 196, 40, 20);

        $pdf->SetFont('helvetica', '', 9);

        // Nama Penerima Informasi
        $pdf->SetXY(23, 219.3);
        $pdf->Cell(40, 5, $request->nama_ttd_penerima_informasi, 0, 0, 'C');

        // Nama Perawat
        $pdf->SetXY(85, 219.3  );
        $pdf->Cell(40, 5, $request->nama_ttd_perawat, 0, 0, 'C');

        // Nama Dokter
        $pdf->SetXY(144, 219);
        $pdf->Cell(40, 5, $request->nama_ttd_dokter, 0, 0, 'C');


        /*
        |--------------------------------------------------------------------------
        | HALAMAN 2 (JIKA ADA)
        |--------------------------------------------------------------------------
        */
        if ($pageCount >= 2) {
            $pdf->AddPage();
            $page2 = $pdf->importPage(2);
            $pdf->useTemplate($page2);
            $pdf->SetAutoPageBreak(false);

            $pdf->SetFont('helvetica', '', 10);

            // =====================
            // ISI DATA HALAMAN 2
            // =====================
            $pdf->Text(77, 242, $request->nama_penerima_informasi);
            $pdf->Text(77, 247, $request->hubungan_pasien);

            // =====================
            // TTD HALAMAN 2
            $pdf->Image($ttdPerawat, 45, 269, 40, 20);
            $pdf->Image($ttdPenerimaInformasi, 124, 269, 40, 20);

            $pdf->SetFont('helvetica', '', 9);

            // Nama Perawat
            $pdf->SetXY(46, 281);
            $pdf->Cell(40, 5, $request->nama_ttd_perawat, 0, 0, 'C');

            // Nama Penerima Informasi
            $pdf->SetXY(120, 281);
            $pdf->Cell(40, 5, $request->nama_ttd_penerima_informasi, 0, 0, 'C');
        }

        // =====================
        // 8. BERSIHKAN FILE TTD
        // =====================
        @unlink($ttdPenerimaInformasi);
        @unlink($ttdPerawat);
        @unlink($ttdDokter);

        // =====================
        // 9. OUTPUT PDF
        // =====================
        return response($pdf->Output('persetujuan.pdf', 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="persetujuan.pdf"');
    }


    private function saveSignature($base64, $role)
    {
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $image  = base64_decode($base64);

        $path = storage_path("app/temp/ttd_{$role}.png");
        file_put_contents($path, $image);

        return $path;
    }
}
