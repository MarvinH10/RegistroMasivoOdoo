<?php

namespace App\Http\Controllers;

use App\Services\OdooService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductoController extends Controller
{
    protected $ServicioOdoo;

    public function __construct(OdooService $ServicioOdoo)
    {
        $this->ServicioOdoo = $ServicioOdoo;
    }

    private function readProductsFromFile()
    {
        if (Storage::exists('productos.json')) {
            return json_decode(Storage::get('productos.json'), true);
        }
        return [];
    }

    private function writeProductsToFile(array $productos)
    {
        Storage::put('productos.json', json_encode($productos));
    }

    /*INICIO FUNCIONALIDADES EN VISTA*/
    public function index()
    {
        return Inertia::render('Producto/Producto');
    }

    public function almacenar(Request $request)
    {
        try {
            $newProduct = $request->validate([
                'name' => 'required|string|max:255',
                'default_code' => 'nullable|string|max:255',
                'categ_id' => 'required|integer',
                'subcateg1_id' => 'nullable|integer',
                'subcateg2_id' => 'nullable|integer',
                'subcateg3_id' => 'nullable|integer',
                'subcateg4_id' => 'nullable|integer',
                'categoriasConcatenadas' => 'nullable|string|max:600',
                'list_price' => 'nullable|numeric',
                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required|integer',
                'attributes.*.value_ids' => 'required|array',
                'attributes.*.value_ids.*' => 'required|integer',
                'attributes.*.extra_references' => 'nullable|array',
                'attributes.*.extra_references.*' => 'nullable|string',
                'attributes.*.extra_prices' => 'nullable|array',
                'attributes.*.extra_prices.*' => 'nullable|numeric',
            ]);

            $newProduct['id'] = !empty($productos) ? end($productos)['id'] + 1 : 1;

            if (!empty($newProduct['attributes'])) {
                $processedAttributes = [];
                $attributesWithRefs = [];

                foreach ($newProduct['attributes'] as $attribute) {
                    $hasRefs = false;
                    if (!empty($attribute['extra_references'])) {
                        foreach ($attribute['extra_references'] as $ref) {
                            if (!empty($ref)) {
                                $hasRefs = true;
                                break;
                            }
                        }
                    }

                    $processedAttribute = [
                        'attribute_id' => $attribute['attribute_id'],
                        'value_ids' => $attribute['value_ids'] ?? [],
                        'extra_references' => $attribute['extra_references'] ?? [],
                        'extra_prices' => $attribute['extra_prices'] ?? [],
                    ];

                    if ($hasRefs) {
                        $attributesWithRefs[] = $processedAttribute;
                    } else {
                        $processedAttributes[] = $processedAttribute;
                    }
                }

                $newProduct['attributes'] = array_merge($processedAttributes, $attributesWithRefs);
            }

            $productos[] = $newProduct;
            $this->writeProductsToFile($productos);

            return response()->json($newProduct);
        } catch (Exception $e) {
            Log::error('Error almacenando producto:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Error almacenando producto: ' . $e->getMessage()], 500);
        }
    }

    public function actualizar(Request $request, $id)
    {
        try {
            $productos = $this->readProductsFromFile();

            $productoIndex = array_search($id, array_column($productos, 'id'));

            if ($productoIndex === false) {
                Log::warning("Producto no encontrado: ID {$id}");
                return response()->json(['error' => 'Producto no encontrado'], 404);
            }

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'default_code' => 'nullable|string|max:255',
                'categ_id' => 'required|integer',
                'subcateg1_id' => 'nullable|integer',
                'subcateg2_id' => 'nullable|integer',
                'subcateg3_id' => 'nullable|integer',
                'subcateg4_id' => 'nullable|integer',
                'list_price' => 'nullable|numeric',
                'attributes' => 'nullable|array',
                'attributes.*.attribute_id' => 'required|integer',
                'attributes.*.value_ids' => 'required|array',
                'attributes.*.value_ids.*' => 'required|integer',
                'attributes.*.extra_references' => 'nullable|array',
                'attributes.*.extra_references.*' => 'nullable|string',
                'attributes.*.extra_prices' => 'nullable|array',
                'attributes.*.extra_prices.*' => 'nullable|numeric',
            ]);

            $productos[$productoIndex] = array_merge($productos[$productoIndex], $validatedData);
            $this->writeProductsToFile($productos);

            return response()->json($productos[$productoIndex]);
        } catch (Exception $e) {
            Log::error('Error actualizando producto:', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'Error actualizando producto'], 500);
        }
    }


    public function quitar($id)
    {
        try {
            $productos = $this->readProductsFromFile();
            $productos = array_filter($productos, function ($producto) use ($id) {
                return $producto['id'] != $id;
            });
            $this->writeProductsToFile(array_values($productos));
            return response()->json(['message' => 'Producto eliminado']);
        } catch (Exception $e) {
            Log::error('Error quitando producto: ' . $e->getMessage());
            return response()->json(['error' => 'Error quitando producto'], 500);
        }
    }
    /*FINAL FUNCIONALIDADES EN VISTA*/

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
