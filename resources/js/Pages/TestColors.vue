<template>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Test de Colores - BaseButton</h1>
            
            <div class="space-y-8">
                <!-- Test SweetAlert -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Test SweetAlert - Directo JavaScript</h2>
                    <div class="flex flex-wrap gap-4">
                        <BaseButton color="success" label="Test Success Alert" @click="testSuccessAlert" />
                        <BaseButton color="danger" label="Test Error Alert" @click="testErrorAlert" />
                        <BaseButton color="warning" label="Test Confirm Alert" @click="testConfirmAlert" />
                        <BaseButton color="info" label="Test Info Alert" @click="testInfoAlert" />
                    </div>
                </div>

                <!-- Test Flash Messages desde Backend -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Test SweetAlert - Flash Messages Backend</h2>
                    <div class="flex flex-wrap gap-4">
                        <BaseButton color="success" label="Success desde Backend" href="/test-success" />
                        <BaseButton color="danger" label="Error desde Backend" href="/test-error" />
                        <BaseButton color="info" label="Info desde Backend" href="/test-info" />
                    </div>
                </div>

                <!-- Botones normales -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Botones Normales</h2>
                    <div class="flex flex-wrap gap-4">
                        <BaseButton color="success" label="Success" />
                        <BaseButton color="danger" label="Danger" />
                        <BaseButton color="warning" label="Warning" />
                        <BaseButton color="info" label="Info" />
                    </div>
                </div>

                <!-- Debug: mostrar las clases que se están aplicando -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Debug - Clases aplicadas</h2>
                    <div class="space-y-2 text-sm font-mono">
                        <div><strong>Info:</strong> {{ getDebugClasses('info') }}</div>
                        <div><strong>Success:</strong> {{ getDebugClasses('success') }}</div>
                        <div><strong>Danger:</strong> {{ getDebugClasses('danger') }}</div>
                        <div><strong>Warning:</strong> {{ getDebugClasses('warning') }}</div>
                    </div>
                </div>

                <!-- Botones de ejemplo directo con clases -->
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Botones con clases directas (para comparar)</h2>
                    <div class="flex flex-wrap gap-4">
                        <button class="px-6 py-2 bg-success-400 text-white border border-success-400 rounded">Success Direct</button>
                        <button class="px-6 py-2 bg-error-400 text-white border border-red-600 rounded">Danger Direct</button>
                        <button class="px-6 py-2 bg-warning-300 text-white border border-warning-300 rounded">Warning Direct</button>
                        <button class="px-6 py-2 bg-medic-400 text-white border border-medic-100 rounded">Info Direct</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import BaseButton from '@/Components/BaseButton.vue'
import { getButtonColor } from '@/colors.js'
import { messageSuccess, error500, error422, messageConfirm } from '@/Hooks/useErrorsForm'

const getDebugClasses = (color) => {
    const result = getButtonColor(color, false, true, false)
    return Array.isArray(result) ? result.join(' ') : result
}

const testSuccessAlert = () => {
    messageSuccess('¡Esta es una alerta de éxito!')
}

const testErrorAlert = () => {
    error500('Este es un mensaje de error de prueba')
}

const testInfoAlert = () => {
    error422('Este es un mensaje de información')
}

const testConfirmAlert = async () => {
    const result = await messageConfirm('¿Estás seguro de que quieres continuar?')
    if (result.isConfirmed) {
        messageSuccess('¡Confirmaste la acción!')
    }
}
</script>