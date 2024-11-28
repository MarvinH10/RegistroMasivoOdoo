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

    public function traerProductosFavoritos()
    {
        $this->ServicioOdoo->authenticate();

        $favoritos = $this->ServicioOdoo->traerProductosFavoritos();
        return response()->json($favoritos);
    }

    public function traerCategorias()
    {
        $this->ServicioOdoo->authenticate();

        $categorias = $this->ServicioOdoo->traerCategorias();
        return response()->json($categorias);
    }

    public function traerSubcategorias($id)
    {
        $this->ServicioOdoo->authenticate();

        $subcategorias = $this->ServicioOdoo->traerSubcategorias($id);
        return response()->json($subcategorias);
    }

    public function traerAtributos()
    {
        $this->ServicioOdoo->authenticate();

        $atributos = $this->ServicioOdoo->traerAtributos();
        return response()->json($atributos);
    }

    public function traerValoresAtributos($id)
    {
        $this->ServicioOdoo->authenticate();

        $valoresAtributos = $this->ServicioOdoo->traerValoresAtributos($id);
        return response()->json($valoresAtributos);
    }
}
