<script setup>
defineProps({
  productos: {
    type: Array,
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

const getColorClass = () => {
  const colors = [
    "bg-red-500",
    "bg-blue-500",
    "bg-green-500",
    "bg-yellow-500",
    "bg-purple-500",
    "bg-pink-500",
    "bg-teal-500",
    "bg-orange-500",
    "bg-cyan-500",
    "bg-rose-500",
    "bg-lime-500",
    "bg-amber-500",
    "bg-indigo-500",
    "bg-emerald-500",
  ];
  return colors[Math.floor(Math.random() * colors.length)];
};
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
          {{ producto.categoriasConcatenadas }}
        </td>
        <td class="px-6 py-4 border-b">S/. {{ producto.list_price }}</td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
            class="flex flex-wrap gap-1"
          >
            {{ traerNombresAtributos(attribute.attribute_id) }}
          </span>
        </td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
            class="flex flex-wrap gap-1"
          >
            <span
              v-for="(value, index) in attribute.value_ids"
              :key="value"
              class="inline-block text-xs font-medium px-2 py-1 rounded-full text-white mb-2"
              :class="getColorClass(index)"
            >
              {{ traerNombresValoresAtributos(value) }}
            </span>
          </span>
        </td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
            class="flex flex-wrap gap-1"
          >
            <span
              v-for="(ref, index) in attribute.extra_references"
              :key="index"
              class="inline-block text-xs font-medium px-2 py-1 rounded-full text-white mb-2"
              :class="getColorClass(index)"
            >
              {{ ref }}
            </span>
          </span>
        </td>
        <td class="px-6 py-4 border-b">
          <span
            v-for="attribute in producto.attributes"
            :key="attribute.attribute_id"
            class="flex flex-wrap gap-1"
          >
            <span
              v-for="(price, index) in attribute.extra_prices"
              :key="index"
              class="inline-block text-xs font-medium px-2 py-1 rounded-full text-white mb-2"
              :class="getColorClass(index)"
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
