// resources/js/Pages/Auth/Register.vue

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import BaseButton from '@/Components/BaseButton.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Register" />

    <Card class="w-full max-w-sm">
      <CardHeader class="text-center">
        <Link :href="route('home')" class="flex flex-col items-center gap-3 mb-4">
          <img src="/logoDiabCare.png" alt="DiabCare" class="w-12 h-12" />
          <span class="font-extrabold text-2xl text-medic-700">DiabCare</span>
        </Link>
        <CardTitle class="text-xl">
          Crear una Cuenta
        </CardTitle>
        <CardDescription>
          Ingresa tus datos para registrarte.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit">
          <div class="grid gap-4">
            <div class="grid gap-2">
              <Label for="name">Nombre</Label>
              <Input id="name" v-model="form.name" required autofocus autocomplete="name" />
              <p v-if="form.errors.name" class="text-sm text-destructive">{{ form.errors.name }}</p>
            </div>
            <div class="grid gap-2">
              <Label for="email">Correo Electrónico</Label>
              <Input id="email" v-model="form.email" type="email" required autocomplete="username" />
              <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>
            <div class="grid gap-2">
              <Label for="password">Contraseña</Label>
              <Input id="password" v-model="form.password" type="password" required autocomplete="new-password" />
              <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
            </div>
            <div class="grid gap-2">
              <Label for="password_confirmation">Confirmar Contraseña</Label>
              <Input id="password_confirmation" v-model="form.password_confirmation" type="password" required autocomplete="new-password" />
              <p v-if="form.errors.password_confirmation" class="text-sm text-destructive">{{ form.errors.password_confirmation }}</p>
            </div>
            <BaseButton
              type="submit"
              color="info"
              :icon="mdiLogin"
              label="Crear Cuenta"
              title="Crear Cuenta"
              :disabled="form.processing"
              :processing="form.processing"
            />
          </div>
        </form>
        <div class="mt-4 text-center text-sm">
          ¿Ya tienes una cuenta?
          <Link :href="route('login')" class="underline">
            Inicia sesión
          </Link>
        </div>
      </CardContent>
    </Card>
  </GuestLayout>
</template>