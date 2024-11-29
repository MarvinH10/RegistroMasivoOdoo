<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
  productos: {
    type: Array,
    required: true,
  },
  traerNombresCategorias: {
    type: Function,
    required: true,
  },
  traerNombresAtributos: {
    type: Function,
    required: true,
  },
  traerNombresValoresAtributos: {
    type: Function,
    required: true,
  },
});

const emits = defineEmits(["duplicar", "editar", "eliminar"]);
</script>

<template>
  <table v-if="productos.length" class="min-w-full mt-4 table-auto">
    <thead>
      <tr>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">ID</th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Nombre</th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Código</th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">
          Categorías
        </th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Precio</th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">
          Atributos
        </th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">
          Valores de Atributos
        </th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">
          Referencia Interna
        </th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">
          Precio Extra
        </th>
        <th class="px-6 py-3 border-b-2 border-gray-300 text-left">Acciones</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(producto, index) in productos" :key="producto.id">
        <td class="px-6 py-4 border-b">{{ index + 1 }}</td>
        <td class="px-6 py-4 border-b">{{ producto.name }}</td>
        <td class="px-6 py-4 border-b">{{ producto.default_code }}</td>
        <td class="px-6 py-4 border-b">
          {{ traerNombresCategorias(producto) }}
        </td>
        <td class="px-6 py-4 border-b">S/. {{ producto.list_price }}</td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
            class="tag"
          >
            {{ traerNombresAtributos(attribute.attribute_id) }}
          </span>
        </td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
          >
            <span v-for="value in attribute.value_ids" :key="value" class="tag">
              {{ traerNombresValoresAtributos(value) }}
            </span>
          </span>
        </td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
          >
            <span
              v-for="(ref, index) in attribute.extra_references"
              :key="index"
              class="tag"
            >
              {{ ref }}
            </span>
          </span>
        </td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
          >
            <span
              v-for="(price, index) in attribute.extra_prices"
              :key="index"
              class="tag"
            >
              {{ price !== null ? "S/. " + price : "S/. 0" }}
            </span>
          </span>
        </td>
        <td class="px-6 py-4 border-b">
          <button
            @click="$emit('duplicar', producto)"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded"
          >
            <i class="fas fa-copy"></i>
          </button>
          <button
            @click="$emit('editar', producto)"
            class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-1 px-2 ml-1 rounded"
          >
            <i class="fas fa-edit"></i>
          </button>
          <button
            @click="$emit('eliminar', producto.id)"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 ml-1 rounded"
          >
            <i class="fas fa-trash-alt"></i>
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</template>
