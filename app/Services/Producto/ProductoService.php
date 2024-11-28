<?php

namespace App\Services\Producto;

class ProductoService
{
    protected $modelos;
    protected $base_datos;
    protected $uid;
    protected $contraseña;

    public function __construct($modelos, $base_datos, $uid, $contraseña)
    {
        $this->modelos = $modelos;
        $this->base_datos = $base_datos;
        $this->uid = $uid;
        $this->contraseña = $contraseña;
    }

    public function traerProductosFavoritos()
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.template',
            'search_read',
            [
                [['is_favorite', '=', true]],
            ],
            [
                'fields' => ['id', 'name', 'list_price', 'default_code'],
            ]
        );
    }

    public function traerCategorias()
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.category',
            'search_read',
            [
                [['parent_id', '=', false]],
            ],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }

    public function traerSubcategorias($id)
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.category',
            'search_read',
            [
                [['parent_id', '=', (int) $id]],
            ],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }

    public function traerAtributos()
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.attribute',
            'search_read',
            [],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }

    public function traerValoresAtributos($id)
    {
        return $this->modelos->execute_kw(
            $this->base_datos,
            $this->uid,
            $this->contraseña,
            'product.attribute.value',
            'search_read',
            [
                [['attribute_id', '=', (int) $id]],
            ],
            [
                'fields' => ['id', 'name'],
            ]
        );
    }
}
