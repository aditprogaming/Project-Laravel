<?php

use App\Http\Controllers\PortivaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PortivaController::class, 'landing'])->name('portiva.landing');
Route::get('/beranda', [PortivaController::class, 'dashboard'])->name('portiva.dashboard');
Route::get('/template', [PortivaController::class, 'templates'])->name('portiva.templates');
Route::post('/template', [PortivaController::class, 'storeTemplate'])->name('portiva.template.store');
Route::patch('/template/{id}', [PortivaController::class, 'updateTemplate'])->name('portiva.template.update');
Route::delete('/template/{id}', [PortivaController::class, 'destroyTemplate'])->name('portiva.template.destroy');
Route::get('/portofolio', [PortivaController::class, 'portfolio'])->name('portiva.portfolio');
Route::get('/lihat-portofolio/{id}', [PortivaController::class, 'viewPortfolio'])->name('portiva.view');
Route::delete('/hapus-portofolio/{id}', [PortivaController::class, 'deletePortfolio'])->name('portiva.delete');
Route::get('/akun', [PortivaController::class, 'account'])->name('portiva.account');
Route::delete('/akun/hapus-akun', [PortivaController::class, 'deleteAccount'])->name('portiva.account.delete');
Route::delete('/akun/hapus-akun/{id}', [PortivaController::class, 'deleteAccountByAdmin'])->name('portiva.account.delete.admin');
Route::get('/akun/upload-poto', [PortivaController::class, 'uploadPhotoForm'])->name('portiva.upload.form');
Route::post('/akun/upload-poto', [PortivaController::class, 'uploadPhoto'])->name('portiva.upload');
Route::post('/register', [PortivaController::class, 'register'])->name('portiva.register');
Route::post('/login', [PortivaController::class, 'login'])->name('portiva.login');
Route::post('/admin-login', [PortivaController::class, 'adminLogin'])->name('portiva.admin.login');
Route::post('/save-portfolio', [PortivaController::class, 'savePortfolio'])->name('portiva.save');
Route::get('/logout', [PortivaController::class, 'logout'])->name('portiva.logout');
