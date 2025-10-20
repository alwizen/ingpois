<?php

use App\Http\Controllers\InvoicePdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('welcome'));

Route::redirect('/login', '/admin/login', 302); // <-- tambah ini

Route::get('/invoice/{invoice}/pdf',  [\App\Http\Controllers\InvoicePdfController::class, 'generate'])->name('invoice.pdf');
Route::get('/invoice/{invoice}/pdf2', [\App\Http\Controllers\InvoicePdfController::class, 'generateV2'])->name('invoice.pdf2');
