<script setup>
import { reactive, ref, onMounted, onBeforeUnmount, watch } from "vue";
import axios from "axios";

defineProps({
  mostrar: {
    type: Boolean,
    required: true,
  },
  cerrar: {
    type: Function,
    required: true,
  },
  categorias: {
    type: Array,
    required: true,
  },
  atributos: {
    type: Array,
    required: true,
  },
  valoresAtributos: {
    type: Array,
    required: true,
  },
  traerNombresValoresAtributos: {
    type: Function,
    required: true,
  },
});

defineEmits(["close", "save"]);

const producto = reactive({
  nombre: "",
  codigo: "",
  categoriaPrincipal: "",
  subcategoriasSeleccionadas: ["", "", "", ""],
  subcategoriasDisponibles: [[], [], [], []],
  precioVenta: 0,
  atributos: [],
});

const selectRefs = ref([]);

const cargarSubcategorias = async (categoriaId, nivel) => {
  try {
    if (categoriaId) {
      const response = await axios.get(
        `/productos/subcategorias/traer/${categoriaId}`
      );
      producto.subcategoriasDisponibles[nivel - 1] = response.data;
    } else {
      producto.subcategoriasDisponibles[nivel - 1] = [];
    }
    limpiarSubcategoriasInferiores(nivel);
  } catch (error) {
    console.error(`Error al cargar subcategorías para nivel ${nivel}:`, error);
  }
};

const actualizarSubcategorias = async (nivel, categoriaId) => {
  try {
    await cargarSubcategorias(categoriaId, nivel);
  } catch (error) {
    console.error(
      `Error al actualizar subcategorías para nivel ${nivel}:`,
      error
    );
  }
};

const limpiarSubcategoriasInferiores = (nivel) => {
  for (let i = nivel; i < producto.subcategoriasSeleccionadas.length; i++) {
    producto.subcategoriasSeleccionadas[i] = "";
    producto.subcategoriasDisponibles[i] = [];
  }
};

const cargarValoresAtributos = async (atributoId, index) => {
  try {
    const response = await axios.get(
      `/productos/valores_atributos/traer/${atributoId}`
    );
    const valores = response.data || [];
    producto.atributos[index].valoresDisponibles = valores;
    producto.atributos[index].valor = [];
    producto.atributos[index].extraReferences = [];
    producto.atributos[index].extraPrices = [];
    producto.atributos[index].valoresNombres = [];
    initSelect2(index);
  } catch (error) {
    console.error(`Error al cargar valores del atributo ${atributoId}:`, error);
  }
};

const agregarAtributo = () => {
  producto.atributos.push({
    nombre: "",
    valor: [],
    valoresDisponibles: [],
    extraReferences: [],
    extraPrices: [],
    valoresNombres: [],
  });
};

const eliminarAtributo = (index) => {
  producto.atributos.splice(index, 1);
};

const registrarProducto = () => {
  console.log("Datos del producto registrado:", producto);
  emit("save", producto);
};

const initSelect2 = (index) => {
  setTimeout(() => {
    $(selectRefs.value[index])
      .select2({
        placeholder: "Seleccione valores",
        allowClear: true,
      })
      .off("change")
      .on("change", function () {
        const valoresSeleccionados = $(this).val();
        producto.atributos[index].valor = valoresSeleccionados;

        producto.atributos[index].valoresNombres = valoresSeleccionados.map(
          (id) => {
            const valor = producto.atributos[index].valoresDisponibles.find(
              (item) => item.id == id
            );
            return valor ? valor.name : "Sin valor";
          }
        );

        producto.atributos[index].extraReferences = Array(
          valoresSeleccionados.length
        ).fill("");
        producto.atributos[index].extraPrices = Array(
          valoresSeleccionados.length
        ).fill("");
      });
  });
};

const actualizarSelectAncho = () => {
  selectRefs.value.forEach((selectElement) => {
    if (selectElement && $(selectElement).data("select2")) {
      $(selectElement).select2("destroy");
      $(selectElement).select2({ width: "100%" });
    }
  });
};

const manejarResize = () => {
  actualizarSelectAncho();
};

onMounted(() => {
  window.addEventListener("resize", manejarResize);
  actualizarSelectAncho();
  watch(
    () => producto.atributos,
    (newAtributos) => {
      newAtributos.forEach((_, index) => initSelect2(index));
    },
    { deep: true }
  );
});

onBeforeUnmount(() => {
  window.removeEventListener("resize", manejarResize);
});
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
          </div>
        </div>
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
                for="categoriaPrincipal"
                class="block text-sm font-medium text-gray-700"
              >
                Categoría Principal:
              </label>
              <select
                id="categoriaPrincipal"
                v-model="producto.categoriaPrincipal"
                @change="
                  actualizarSubcategorias(1, producto.categoriaPrincipal)
                "
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
                <option value="">Seleccione una categoría</option>
                <option
                  v-for="categoria in categorias"
                  :key="categoria.id"
                  :value="categoria.id"
                >
                  {{ categoria.name }}
                </option>
              </select>
            </div>

            <div v-for="nivel in 4" :key="`subcategoria_${nivel}`">
              <label
                :for="`subcategoria_${nivel}`"
                class="block text-sm font-medium text-gray-700"
              >
                Subcategoría {{ nivel }}:
              </label>
              <select
                :id="`subcategoria_${nivel}`"
                v-model="producto.subcategoriasSeleccionadas[nivel - 1]"
                @change="
                  actualizarSubcategorias(
                    nivel + 1,
                    producto.subcategoriasSeleccionadas[nivel - 1]
                  )
                "
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
              >
                <option value="">Seleccione una subcategoría</option>
                <option
                  v-for="subcategoria in producto.subcategoriasDisponibles[
                    nivel - 1
                  ]"
                  :key="subcategoria.id"
                  :value="subcategoria.id"
                >
                  {{ subcategoria.name }}
                </option>
              </select>
            </div>
          </div>

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

          <div class="mt-6">
            <h4 class="text-md font-medium text-gray-700 mb-4">
              Atributos del Producto
            </h4>

            <div
              v-for="(atributo, index) in producto.atributos"
              :key="index"
              class="border-b pb-4 mb-4"
            >
              <div
                class="grid grid-cols-1 md:grid-cols-10 gap-x-6 gap-y-4 items-start"
              >
                <div class="col-span-1 md:col-span-2">
                  <label
                    class="block text-sm font-medium text-gray-700"
                    :for="'atributo_' + index"
                  >
                    Atributo:
                  </label>
                  <select
                    v-model="atributo.nombre"
                    @change="cargarValoresAtributos(atributo.nombre, index)"
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  >
                    <option value="">Seleccione un atributo</option>
                    <option
                      v-for="attr in atributos"
                      :key="attr.id"
                      :value="attr.id"
                    >
                      {{ attr.name }}
                    </option>
                  </select>
                </div>

                <div class="col-span-1 md:col-span-3">
                  <label
                    class="block text-sm font-medium text-gray-700"
                    :for="'atributo_valores_' + index"
                  >
                    Valores de Atributo:
                  </label>
                  <select
                    ref="selectRefs"
                    :id="'atributo_valores_' + index"
                    class="w-full border-gray-300 rounded-md shadow-sm"
                    multiple
                  >
                    <option
                      v-for="valor in atributo.valoresDisponibles"
                      :key="valor.id"
                      :value="valor.id"
                    >
                      {{ valor.name }}
                    </option>
                  </select>
                </div>

                <div class="col-span-1 md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700">
                    Referencia Interna:
                  </label>
                  <div
                    v-for="(valor, i) in atributo.valor"
                    :key="'ref_' + i"
                    class="mb-2"
                  >
                    <input
                      type="text"
                      v-model="atributo.extraReferences[i]"
                      :placeholder="`Referencia para ${atributo.valoresNombres[i]}`"
                      class="w-full border-gray-300 rounded-md shadow-sm py-2 px-3"
                    />
                  </div>
                </div>

                <div class="col-span-1 md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700">
                    Precio Extra:
                  </label>
                  <div
                    v-for="(valor, i) in atributo.valor"
                    :key="'price_' + i"
                    class="mb-2"
                  >
                    <input
                      type="number"
                      v-model="atributo.extraPrices[i]"
                      :placeholder="`Precio para ${atributo.valoresNombres[i]}`"
                      class="w-full border-gray-300 rounded-md shadow-sm py-2 px-3"
                    />
                  </div>
                </div>

                <div class="col-span-1 text-left mt-4">
                  <button
                    type="button"
                    @click="eliminarAtributo(index)"
                    class="bg-red-500 hover:bg-red-600 text-white font-medium px-3 py-1 rounded"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-4">
              <button
                type="button"
                @click="agregarAtributo"
                class="bg-gray-500 hover:bg-gray-600 text-white font-medium px-4 py-2 rounded"
              >
                + Nuevo Atributo
              </button>
            </div>
          </div>

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
</template>
