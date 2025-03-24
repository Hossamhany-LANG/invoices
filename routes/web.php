<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoicesAttachmentsController;
use App\Http\Controllers\InvoicesDetailsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::resource('invoices' , InvoiceController::class);
Route::resource('sections' , SectionController::class);
Route::resource('products' , ProductController::class);
Route::get('/section/{id}', [InvoiceController::class, 'getproducts']);
Route::resource('invoicesdetails' , InvoicesDetailsController::class);
Route::post('delete_file', [InvoicesDetailsController::class , 'destroy'])->name('delete_file');
Route::get('viewfile/{invoice_number}/{file_name}' ,[InvoicesDetailsController::class , 'open_file']);
Route::get('download/{invoice_number}/{file_name}' ,[InvoicesDetailsController::class , 'get_file']);
Route::resource('InvoiceAttachments' , InvoicesAttachmentsController::class);
Route::get('edit_invoice/{id}' , [InvoiceController::class , 'edit']);
Route::match(['GET', 'POST'], 'Status_show/{id}', [InvoiceController::class, 'show'])->name('Status_show');
Route::post('status_update/{id}' , [InvoiceController::class , 'status_update'])->name('status_update');



/* ----------------------------------------------------------------*/
require __DIR__.'/auth.php';
Route::get('/{page}',[AdminController::class ,'index']);