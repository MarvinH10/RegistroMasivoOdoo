<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import TablaProducto from "@/Components/Producto/TablaProducto.vue";
import ModalRegistrar from "@/Components/Producto/ModalRegistrar.vue";
import axios from "axios";
import { ref, onMounted, reactive } from "vue";

const productos = ref([]);
const categorias = ref([]);
const subcategorias = reactive({
  1: [],
  2: [],
  3: [],
  4: [],
});
const atributos = ref([]);
const valoresAtributos = ref([]);
const cargando = ref(false);
const mostrarModal = ref(false);
const registrarProductosEnOdoo = ref(false);

const cargarProductos = async () => {
  try {
    cargando.value = true;
    const response = await axios.get("/productos/traer");
    productos.value = response.data;
  } catch (error) {
    console.error("Error al cargar productos:", error);
  } finally {
    cargando.value = false;
  }
};

const cargarCategorias = async () => {
  try {
    const response = await axios.get("/productos/categorias/traer");
    categorias.value = response.data;
  } catch (error) {
    console.error("Error al cargar categorías:", error);
  }
};

const cargarAtributos = async () => {
  try {
    const response = await axios.get("/productos/atributos/traer");
    atributos.value = response.data;
  } catch (error) {
    console.error("Error al cargar atributos:", error);
  }
};

const cargarValoresAtributos = async (atributoId, index) => {
  if (!atributoId) {
    // console.error("Error: ID del atributo no proporcionado.");
    return;
  }
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

onMounted(async () => {
  await Promise.all([
    cargarCategorias(),
    cargarAtributos(),
    cargarValoresAtributos(),
  ]);
});

const agregarProductoALista = (nuevoProducto) => {
  productos.value.push(nuevoProducto);

  if (!nuevoProducto.attributes || !nuevoProducto.attributes.length) {
    console.warn("El producto registrado no tiene atributos definidos.");
    return;
  }

  nuevoProducto.attributes.forEach((attribute) => {
    if (attribute.value_ids && attribute.value_names) {
      attribute.value_ids.forEach((valueId, index) => {
        if (
          !valoresAtributos.value.some((val) => val.id === valueId) &&
          attribute.value_names[index]
        ) {
          valoresAtributos.value.push({
            id: valueId,
            name: attribute.value_names[index],
          });
        }
      });
    }
  });
};

const registrarTodosLosProductos = async () => {
  if (confirm("¿Estás seguro de registrar todos los productos en Odoo?")) {
    try {
      registrarProductosEnOdoo.value = true;
      await axios.post("/productos/registrar-en-odoo", {
        productos: productos.value,
      });

      alert("Productos registrados exitosamente en Odoo");
    } catch (error) {
      console.error("Error al registrar productos en Odoo:", error);
      alert(
        "Hubo un error al registrar los productos en Odoo. Inténtalo nuevamente."
      );
    } finally {
      registrarProductosEnOdoo.value = false;
    }
  }
};

const traerNombresCategorias = (producto) => {
  const categoria = categorias.value.find(
    (cat) => cat.id === producto.category_id
  );
  return categoria ? categoria.name : "Sin categoría";
};

const traerSubcategoriasDeCategoria = (categoriaId) => {
  return subcategorias.value.filter(
    (subcat) => subcat.parent_id === categoriaId
  );
};

const traerNombresAtributos = (id) => {
  const atributo = atributos.value.find((attr) => attr.id === id);
  return atributo ? atributo.name : "Desconocido";
};

const traerNombresValoresAtributos = (id) => {
  const valor = valoresAtributos.value.find((val) => val.id === id);
  return valor ? valor.name : "Sin valor";
};

const abrirModalCrearProducto = () => {
  mostrarModal.value = true;
};

const cerrarModal = () => {
  mostrarModal.value = false;
};

const duplicarProducto = async (producto) => {
  try {
    const response = await axios.post("/api/productos/duplicar", { producto });
    await cargarProductos();
    alert("Producto duplicado exitosamente");
  } catch (error) {
    console.error("Error al duplicar producto:", error);
  }
};

const editarProducto = (producto) => {
  console.log("Abrir modal para editar producto:", producto);
};

const eliminarProducto = async (id) => {
  if (confirm("¿Estás seguro de eliminar este producto?")) {
    try {
      await axios.delete(`/api/productos/${id}`);
      await cargarProductos();
      alert("Producto eliminado exitosamente");
    } catch (error) {
      console.error("Error al eliminar producto:", error);
    }
  }
};
</script>

<template>
  <AppLayout title="Producto">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Producto
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-12xl mx-auto sm:px-6 lg:px-1">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <button
            @click="abrirModalCrearProducto"
            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
          >
            <i class="fas fa-circle-plus"></i> Agregar
          </button>
          <div v-if="cargando" class="text-center my-4">
            <i class="fas fa-spinner fa-spin mr-2"></i> Cargando productos...
          </div>
          <div v-else class="overflow-x-auto">
            <TablaProducto
              :productos="productos"
              :traerNombresCategorias="traerNombresCategorias"
              :traerNombresAtributos="traerNombresAtributos"
              :traerNombresValoresAtributos="traerNombresValoresAtributos"
              @duplicar="duplicarProducto"
              @editar="editarProducto"
              @eliminar="eliminarProducto"
            />
          </div>
          <p
            v-if="!cargando && !productos.length"
            class="mt-4 text-center text-gray-500"
          >
            No hay productos por mostrar
          </p>
        </div>
        <div class="flex justify-end mt-4">
          <div
            v-if="registrarProductosEnOdoo"
            class="text-center text-blue-500 font-bold"
          >
            <i class="fas fa-spinner fa-spin mr-2"></i> Registrando productos en
            Odoo...
          </div>
          <button
            v-else
            @click="registrarTodosLosProductos"
            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
          >
            <i class="fas fa-save"></i> Crear Productos en Odoo
          </button>
        </div>
      </div>
    </div>
    <ModalRegistrar
      :mostrar="mostrarModal"
      :categorias="categorias"
      :subcategorias="subcategorias"
      :atributos="atributos"
      @save="agregarProductoALista"
      @close="cerrarModal"
    />
  </AppLayout>
</template>
