<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HabilitacionController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para la funcion 2 (Ingresar Habitaciones)
Route::get('/habilitacion/crear', [HabilitacionController::class, 'create'])->name('habilitacion.create');
Route::post('/habilitacion', [HabilitacionController::class, 'store'])->name('habilitacion.store');