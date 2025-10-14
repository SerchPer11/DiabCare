<template>
    <div class="min-h-screen bg-gray-100 py-12">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Test de Colores - BaseButton</h1>
            
            <div class="space-y-8">
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
                        <button class="px-6 py-2 bg-forest-400 text-white border border-forest-100 rounded">Info Direct</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import BaseButton from '@/Components/BaseButton.vue'
import { getButtonColor } from '@/colors.js'

const getDebugClasses = (color) => {
    const result = getButtonColor(color, false, true, false)
    return Array.isArray(result) ? result.join(' ') : result
}
</script>