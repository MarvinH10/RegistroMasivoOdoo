<?php

namespace App\Http\Controllers;

use App\Services\OdooService;

class ProductoController extends Controller
{
    protected $ServicioOdoo;

    public function __construct(OdooService $ServicioOdoo)
    {
        $this->ServicioOdoo = $ServicioOdoo;
    }

    public function traerCategorias()
    {
        $this->ServicioOdoo->authenticate();

        $categorias = $this->ServicioOdoo->traerCategorias();
        return response()->json($categorias);
    }
}
