<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\FormPersetujuanController;

Route::get('/form-persetujuan', function () {
    return view('form');
});

Route::post('/form-persetujuan', [PdfController::class, 'generate']);
Route::get('/form-persetujuan', [FormPersetujuanController::class, 'index']);