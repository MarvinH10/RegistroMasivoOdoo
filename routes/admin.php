<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::controller(ProductoController::class)->group(function () {
        Route::get('/productos', 'index')->name('Producto');
        Route::get('/productos/favoritos/traer', 'traerProductosFavoritos');
        Route::get('/productos/categorias/traer', 'traerCategorias');
        Route::get('/productos/subcategorias/traer/{id}', 'traerSubcategorias');
        Route::get('/productos/atributos/traer', 'traerAtributos');
        Route::get('/productos/valores_atributos/traer/{id}', 'traerValoresAtributos');
    });
});
