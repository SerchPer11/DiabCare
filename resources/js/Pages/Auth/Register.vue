// resources/js/Pages/Auth/Register.vue

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Button } from '@/Components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

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
      <CardHeader>
        <CardTitle class="text-2xl">
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
            <Button type="submit" class="w-full" :disabled="form.processing">
              Crear Cuenta
            </Button>
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