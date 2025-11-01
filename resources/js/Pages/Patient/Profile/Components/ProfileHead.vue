<template>
    <CardBox>
        <div class="p-6">
            <div class="flex flex-col items-center gap-6 md:flex-row md:items-start">
                
                <Avatar class="h-24 w-24 border-2 border-border border-medic-300 shadow-lg text-3xl">
                    <AvatarFallback>{{ userInitials }}</AvatarFallback>
                </Avatar>

                <div class="flex-grow text-center md:text-left">
                    <h1 class="text-2xl font-bold tracking-tight">{{ userName }}</h1>
                    <p class="text-md text-muted-foreground">{{ userEmail }}</p>
                    <Badge variant="outline" class="mt-2 bg-medic-100 border-medic-300 shadow-md">Paciente</Badge>
                </div>
                <div class="m-auto flex justify-center md:justify-end">
                <BaseButton :icon="mdiArchiveClock " color="info" label="Mi seguimiento clínico" :routeName="`patient.clinical-log.show`" 
                :parameter="form.id"/>
            </div>
            </div>
        </div>
    </CardBox>
</template>

<script setup>
import { computed, inject } from 'vue';
import CardBox from '@/Components/CardBox.vue';
import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import { Badge } from '@/Components/ui/badge';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiArchiveClock } from '@mdi/js';

const form = inject('form');

const userName = computed(() => `${form.name} ${form.last_name}`);
const userEmail = computed(() => form.email);

const userInitials = computed(() => {
    if (!form.name) return 'U'
    const name = form.name || ''
    const words = name.trim().split(' ')
    if (words.length >= 2) {
      return `${words[0].charAt(0)}${words[1].charAt(0)}`.toUpperCase()
    }
    return name.charAt(0).toUpperCase() || 'U'
});
</script>