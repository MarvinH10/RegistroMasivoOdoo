<?php

use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::controller(ProductoController::class)->group(function () {
        Route::get('/productos', 'index')->name('Producto');
        Route::post('/productos/almacenar', 'almacenar');
        Route::put('/productos/actualizar/{id}', 'actualizar');
        Route::delete('/productos/eliminar/{id}', 'quitar');
        Route::get('/productos/favoritos/traer', 'traerProductosFavoritos');
        Route::get('/productos/categorias/traer', 'traerCategorias');
        Route::get('/productos/subcategorias/traer/{id}', 'traerSubcategorias');
        Route::get('/productos/atributos/traer', 'traerAtributos');
        Route::get('/productos/valores_atributos/traer/{id}', 'traerValoresAtributos');
        Route::post('/productos/registrar-en-odoo', 'registrarProductos');
    });
});
