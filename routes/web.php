<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;

Route::get('/form-persetujuan', function () {
    return view('form');
});

Route::post('/form-persetujuan', [PdfController::class, 'generate']);
