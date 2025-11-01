<template>
    <CardBox v-if="questions.data && questions.data.length > 0" class="border-transparent shadow-none max-h-96"
        bg="transparent">
        <QuestionForm :routeName="routeName" />

        <CardBox v-for="item in questions.data" :key="item.id" class="mt-4 p-4" bg="bg-medic-50">
            <div
                class="flex flex-col md:flex-row justify-between md:items-start text-gray-700 text-base leading-relaxed">
                <h1 class="text-lg font-semibold mb-3 md:mb-0">{{ item.title }}</h1>
                <div
                    class="flex-shrink-0 flex flex-col items-end space-y-2 md:flex-row md:items-center md:space-y-0 md:space-x-4 md:ml-4">
                    <div
                        :class="`${statusColor[item.status?.name]} w-auto rounded-full flex items-center justify-center px-3 py-1`">
                        <p class="text-sm font-medium">{{ item.status?.name }}</p>
                    </div>

                    <p class="text-sm text-gray-400">
                        {{ item.answers_count }} respuestas •
                    <p v-if="item.answers_count === 0">Aún no hay respuestas</p>
                    <p v-else>Última: {{ item.answers.at(-1)?.posted_at }} a las {{ item.answers.at(-1)?.comment_time }}
                    </p>
                    </p>
                </div>
            </div>

            <CardBox class="mt-1 p-4">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">

                    <div class="leading-relaxed flex items-center flex-wrap space-x-2">
                        <h2 v-if="item.user" class="text-sm font-medium text-gray-800">
                            {{ item.user?.name }} {{ item.user?.last_name }} {{ item.user?.second_last_name }} •
                        </h2>
                        <h2 v-else class="text-sm font-medium text-gray-800">Usuario desconocido •</h2>
                        <div class="bg-medic-200 w-auto rounded-full flex items-center justify-center px-3 py-1">
                            <p class="text-gray-100 text-sm">{{ item.category?.name }}</p>
                        </div>
                    </div>

                    <div class="mt-2 sm:mt-0">
                        <p class="text-gray-400 text-sm">Creado: {{ item.posted_at }} a las {{ item.event_time }}</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-medic-200">
                    <p>{{ item.content }}</p>
                </div>
            </CardBox>

            <BaseDivider />
            <div class="overflow-auto h-48 border-medic-100 border rounded-lg shadow-sm pl-2 pr-2 pb-2 bg-medic-50" v-if="item.answers_count > 0">
                <CardBox v-for="item in item.answers" :key="item.id" class="mt-4">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">

                        <div class="leading-relaxed flex items-center flex-wrap space-x-2">
                            <h2 v-if="item.user" class="text-sm font-medium text-gray-800">
                                {{ item.user?.name }} {{ item.user?.last_name }} {{ item.user?.second_last_name }}
                            </h2>
                            <h2 v-else class="text-sm font-medium text-gray-800">Usuario desconocido</h2>
                        </div>

                        <div class="mt-2 sm:mt-0">
                            <p class="text-gray-400 text-sm">{{ item.posted_at }} a las {{ item.comment_time }}</p>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t border-medic-100">
                        <p>{{ item.answer }}</p>
                    </div>
                </CardBox>
            </div>
            <CardBox class="mt-4" isForm v-if="item.answers_count < 3">
                <BaseFormField type="textarea" :label="`Respuesta a la pregunta ${item.id}`"
                    v-model="answerContents[item.id]" :error="form.forum_id === item.id ? form.errors.answer : null"
                    placeholder="Escribe una respuesta útil y respetuosa." :maxLength="1000" h="h-24" nomLabel />
                <div class="flex justify-end mt-4">
                    <BaseButton color="info" :icon="mdiSend" label="Responder" title="Agregar usuario"
                        @click="saveForm(item.id)" :processing="processing" />
                </div>
            </CardBox>
        </CardBox>

        <Pagination v-if="questions?.meta" :links="questions.meta.links" :total="questions.meta.total"
            :to="questions.meta.to" :from="questions.meta.from" />
    </CardBox>

    <CardBox v-else class="mt-2">
        <div class="flex items-center justify-center gap-4 py-8">
            <span class="text-gray-500 text-lg">Aun no hay preguntas. Se el primero en preguntar algo.</span>
            <BaseButton color="info" :icon="mdiPlus" label="Agregar" title="Agregar recomendación"
                :routeName="`${routeName}create`" />

        </div>
    </CardBox>

</template>

<script setup>
import CardBox from '@/Components/CardBox.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiPlus, mdiSend } from '@mdi/js';
import Pagination from "@/Components/Pagination.vue";
import BaseDivider from '@/Components/BaseDivider.vue';
import BaseFormField from '@/Components/BaseFormField.vue';
import { useAnswers } from '/resources/js/Pages/Forum/Answers/Composables/useAnswers.js';
import QuestionForm from './QuestionForm.vue';

const props = defineProps({
    questions: {
        type: Object,
        required: true
    },
    routeName: {
        type: String,
        default: 'questions.'
    }
});

const { processing, saveForm, answerContents, form } = useAnswers('forum.answers.');

const statusColor = {
    Abierto: 'bg-green-400 text-gray-50',
    Cerrado: 'bg-red-500 text-gray-50',
    Resuelto: 'bg-medic-200 text-gray-100',
}
</script>