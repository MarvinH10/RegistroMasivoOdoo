<script setup>
defineProps({
  mostrar: {
    type: Boolean,
    required: true,
  },
  cerrar: {
    type: Function,
    required: true,
  },
});

defineEmits(["close", "save"]);

import { reactive } from "vue";

const producto = reactive({
  nombre: "",
  codigo: "",
  categoria: "",
  areaAplicacion: "",
  tipoProducto: "",
  activoPresentacion: "",
  marca: "",
  precioVenta: 0,
  atributos: [],
});

const agregarAtributo = () => {
  producto.atributos.push({ nombre: "", valor: "" });
};

const eliminarAtributo = (index) => {
  producto.atributos.splice(index, 1);
};

const registrarProducto = () => {
  console.log("Datos del producto registrado:", producto);
  emit("save", producto);
};
</script>

<template>
  <div v-if="mostrar" class="fixed z-10 inset-0 overflow-y-auto">
    <div
      class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0"
    >
      <div
        class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
      ></div>
      <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
      &#8203;
      <div
        class="inline-block align-bottom bg-white rounded-lg px-4 sm:px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle max-w-full w-full sm:max-w-[90vw] lg:max-w-[90vw] xl:max-w-[90vw]"
      >
        <div class="sm:flex sm:items-start">
          <div class="w-full">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
              Registrar Producto
            </h3>

            <form @submit.prevent="registrarProducto">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label
                    for="nombre"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Nombre:
                  </label>
                  <input
                    id="nombre"
                    type="text"
                    v-model="producto.nombre"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  />
                </div>

                <div>
                  <label
                    for="codigo"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Código del producto:
                  </label>
                  <input
                    id="codigo"
                    type="text"
                    v-model="producto.codigo"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-4">
                <div>
                  <label
                    for="categoria"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Categoría Principal:
                  </label>
                  <select
                    id="categoria"
                    v-model="producto.categoria"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="">Seleccione una categoría</option>
                    <option value="cat1">Categoría 1</option>
                    <option value="cat2">Categoría 2</option>
                  </select>
                </div>

                <div>
                  <label
                    for="subcategoria1"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Categoría 1:
                  </label>
                  <select
                    id="subcategoria1"
                    v-model="producto.subcategoria_1"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="">Seleccione una subcategoría</option>
                    <option value="tipo1">Tipo 1</option>
                    <option value="tipo2">Tipo 2</option>
                  </select>
                </div>

                <div>
                  <label
                    for="subcategoria2"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Categoría 2:
                  </label>
                  <select
                    id="subcategoria2"
                    v-model="producto.subcategoria_2"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="">Seleccione una subcategoría</option>
                    <option value="tipo1">Tipo 1</option>
                    <option value="tipo2">Tipo 2</option>
                  </select>
                </div>

                <div>
                  <label
                    for="subcategoria3"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Categoría 3:
                  </label>
                  <select
                    id="subcategoria3"
                    v-model="producto.subcategoria_2"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="">Seleccione una subcategoría</option>
                    <option value="tipo1">Tipo 1</option>
                    <option value="tipo2">Tipo 2</option>
                  </select>
                </div>

                <div>
                  <label
                    for="subcategoria4"
                    class="block text-sm font-medium text-gray-700"
                  >
                    Categoría 4:
                  </label>
                  <select
                    id="subcategoria4"
                    v-model="producto.subcategoria_2"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="">Seleccione una subcategoría</option>
                    <option value="tipo1">Tipo 1</option>
                    <option value="tipo2">Tipo 2</option>
                  </select>
                </div>
              </div>

              <!-- Precio Venta -->
              <div class="mt-4">
                <label
                  for="precioVenta"
                  class="block text-sm font-medium text-gray-700"
                >
                  Precio Venta:
                </label>
                <input
                  id="precioVenta"
                  type="number"
                  v-model="producto.precioVenta"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                />
              </div>

              <!-- Atributos -->
              <div class="mt-6">
                <h4 class="text-md font-medium text-gray-700 mb-2">
                  Atributos del Producto
                </h4>
                <div
                  v-for="(atributo, index) in producto.atributos"
                  :key="index"
                  class="flex gap-4 mb-2"
                >
                  <input
                    type="text"
                    v-model="atributo.nombre"
                    placeholder="Nombre del atributo"
                    class="block w-1/2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  />
                  <input
                    type="text"
                    v-model="atributo.valor"
                    placeholder="Valor del atributo"
                    class="block w-1/2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  />
                  <button
                    type="button"
                    @click="eliminarAtributo(index)"
                    class="text-red-500 font-medium hover:underline"
                  >
                    Eliminar
                  </button>
                </div>
                <button
                  type="button"
                  @click="agregarAtributo"
                  class="mt-2 text-blue-500 font-medium hover:underline"
                >
                  + Nuevo Atributo
                </button>
              </div>

              <!-- Botones -->
              <div class="mt-6 flex justify-end">
                <button
                  type="button"
                  @click="cerrar"
                  class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="ml-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                  Registrar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

