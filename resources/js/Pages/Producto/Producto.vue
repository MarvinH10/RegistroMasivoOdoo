<script setup>
import Swal from "sweetalert2";
import AppLayout from "@/Layouts/AppLayout.vue";
import TablaProducto from "@/Components/Producto/TablaProducto.vue";
import ModalRegistrar from "@/Components/Producto/ModalRegistrar.vue";
import ModalEliminar from "@/Components/Producto/ModalEliminar.vue";
import ModalEditar from "@/Components/Producto/ModalEditar.vue";
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
const mostrarModalEliminar = ref(false);
const mostrarModalEditar = ref(false);
const productoSeleccionado = ref({});
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

const agregarProductoALista = async (nuevoProducto) => {
  try {
    const response = await axios.post("/productos/almacenar", nuevoProducto);
    productos.value.push(response.data);
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
  } catch (error) {
    console.error("Error al almacenar el producto:", error);
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Hubo un problema al almacenar el producto.",
      timer: 2000,
    });
  }
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

const generarIdUnico = (productos) => {
  const maxId = productos.reduce(
    (max, producto) => (producto.id > max ? producto.id : max),
    0
  );
  return maxId + 1;
};

const duplicarProducto = (producto) => {
  try {
    const newId = generarIdUnico(productos.value);

    const duplicatedProducto = {
      ...producto,
      id: newId,
      name: `${producto.name} (Copia)`,
      categoriasConcatenadas: producto.categoriasConcatenadas,
      attributes: producto.attributes.map((attr) => ({
        ...attr,
        value_ids: [...attr.value_ids],
        extra_references: [...attr.extra_references],
        extra_prices: [...attr.extra_prices],
      })),
    };

    productos.value.push(duplicatedProducto);

    Swal.fire({
      icon: "success",
      title: "Producto Duplicado",
      text: `Producto "${duplicatedProducto.name}" duplicado exitosamente con ID ${newId}`,
      timer: 2000,
    });
  } catch (error) {
    console.error("Error al duplicar el producto:", error);
    Swal.fire({
      icon: "error",
      title: "Error al duplicar Producto",
      text: "Hubo un error al duplicar el producto. Por favor, intenta nuevamente.",
      timer: 2000,
    });
  }
};

const abrirModalEditar = (producto) => {
  if (producto && producto.id) {
    productoSeleccionado.value = JSON.parse(JSON.stringify(producto));
    mostrarModalEditar.value = true;
  } else {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Producto inválido o sin ID. No se puede editar.",
      timer: 2000,
    });
  }
};

const actualizarProductoEnLista = (productoActualizado) => {
  const index = productos.value.findIndex(
    (p) => p.id === productoActualizado.id
  );
  if (index !== -1) {
    productos.value[index] = productoActualizado;
  }
};

const cerrarModalEditar = () => {
  mostrarModalEditar.value = false;
};

const abrirModalEliminar = (producto) => {
  productoSeleccionado.value = producto;
  mostrarModalEliminar.value = true;
};

const confirmarEliminacion = async () => {
  try {
    await axios.delete(`/productos/eliminar/${productoSeleccionado.value.id}`);
    mostrarModalEliminar.value = false;

    productos.value = productos.value.filter(
      (producto) => producto.id !== productoSeleccionado.value.id
    );

    Swal.fire({
      icon: "success",
      title: "Producto Eliminado",
      text: "El producto se eliminó correctamente.",
      timer: 2000,
    });
  } catch (error) {
    console.error("Error al eliminar producto:", error);
    Swal.fire({
      icon: "error",
      title: "Error al eliminar",
      text: "Hubo un problema al eliminar el producto. Por favor, intenta nuevamente.",
      timer: 2000,
    });
  }
};

const cerrarModalEliminar = () => {
  mostrarModalEliminar.value = false;
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
              :traerNombresAtributos="traerNombresAtributos"
              :traerNombresValoresAtributos="traerNombresValoresAtributos"
              @duplicar="duplicarProducto"
              @editar="abrirModalEditar"
              @eliminar="abrirModalEliminar"
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
    <ModalEliminar
      :mostrar="mostrarModalEliminar"
      :titulo="'Eliminar Producto en Listado'"
      :mensaje="'¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.'"
      @confirmar="confirmarEliminacion"
      @cancelar="cerrarModalEliminar"
    />
    <ModalEditar
      :producto-edicion="productoSeleccionado"
      :mostrar="mostrarModalEditar"
      :categorias="categorias"
      :subcategorias="subcategorias"
      :atributos="atributos"
      @save="actualizarProductoEnLista"
      @close="cerrarModalEditar"
    />
  </AppLayout>
</template>
