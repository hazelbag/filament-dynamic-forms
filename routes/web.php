<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\PanelController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/forms')->group(function () {
   Route::get('/{formConfiguration}', [FormController::class, 'preview'])->name('form.preview');
   Route::post('/{formConfiguration}/submit', [FormController::class, 'submit'])->name('form.submit');
});
