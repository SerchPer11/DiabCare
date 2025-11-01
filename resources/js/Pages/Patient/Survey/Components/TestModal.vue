<!-- Test Modal Simple -->
<template>
    <Teleport to="body">
        <div
            v-if="show"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
            style="z-index: 9999;"
        >
            <div class="bg-white rounded-lg p-6 w-96">
                <h2 class="text-xl font-bold mb-4">Modal de Prueba</h2>
                <p class="mb-4">Show: {{ show }}</p>
                <p class="mb-4">Response: {{ response ? 'Datos disponibles' : 'Sin datos' }}</p>
                
                <div class="flex space-x-2">
                    <button
                        @click="$emit('close')"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                    >
                        Cerrar
                    </button>
                    <button
                        @click="testFunction"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Test
                    </button>
                    <button
                        @click="downloadTest"
                        class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
                    >
                        Descargar
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
const props = defineProps({
    show: Boolean,
    response: Object
})

const emit = defineEmits(['close'])

const testFunction = () => {
    console.log('Botón test funcionando')
    alert('Botón test funcionando!')
}

const downloadTest = () => {
    console.log('Botón descarga funcionando')
    alert('Botón descarga funcionando!')
}
</script>