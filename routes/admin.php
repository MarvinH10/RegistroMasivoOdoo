<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::controller(ProductoController::class)->group(function () {
        Route::get('/productos/categorias/traer', 'traerCategorias');
    });
});
