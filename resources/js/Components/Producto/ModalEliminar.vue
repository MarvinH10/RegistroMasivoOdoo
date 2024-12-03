<script setup>
defineProps({
  mostrar: {
    type: Boolean,
    required: true,
  },
  titulo: {
    type: String,
  },
  mensaje: {
    type: String,
  },
});

const emits = defineEmits(["confirmar", "cancelar"]);

const confirmar = () => emits("confirmar");
const cancelar = () => emits("cancelar");
</script>

<template>
  <div
    v-if="mostrar"
    class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-50"
  >
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
      <div class="text-center text-red-500">
        <i class="fas fa-exclamation-triangle text-4xl mb-4"></i>
        <h2 class="text-xl font-semibold">{{ titulo }}</h2>
      </div>
      <p class="text-gray-700 text-center mt-4">{{ mensaje }}</p>
      <div class="mt-6 flex justify-around">
        <button
          @click="confirmar"
          class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-medium"
        >
          Eliminar
        </button>
        <button
          @click="cancelar"
          class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium"
        >
          Cancelar
        </button>
      </div>
    </div>
  </div>
</template>
