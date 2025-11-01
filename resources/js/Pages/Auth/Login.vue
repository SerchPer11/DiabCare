// resources/js/Pages/Auth/Login.vue

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import BaseButton from '@/Components/BaseButton.vue';
import { mdiCheck, mdiLogin } from '@mdi/js';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui/card';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <GuestLayout>
    <Head title="Log in" />
    <Card class="w-full max-w-sm">
      <CardHeader>
        <CardTitle class="text-2xl">
          Iniciar Sesión
        </CardTitle>
        <CardDescription>
          Ingresa tu correo para acceder a tu cuenta.
        </CardDescription>
      </CardHeader>
      <CardContent>
        <form @submit.prevent="submit">
          <div class="grid gap-4">
            <div class="grid gap-2">
              <Label for="email">Correo Electrónico</Label>
              <Input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="nombre@ejemplo.com"
                required
                autocomplete="username"
              />
              <p v-if="form.errors.email" class="text-sm text-destructive">{{ form.errors.email }}</p>
            </div>
            <div class="grid gap-2">
              <div class="flex items-center">
                <Label for="password">Contraseña</Label>
              </div>
              <Input id="password" v-model="form.password" type="password" required autocomplete="current-password" />
              <p v-if="form.errors.password" class="text-sm text-destructive">{{ form.errors.password }}</p>
              <Link
                  :href="route('password.request')"
                  class="underline transition-opacity hover:opacity-60"
                >
                ¿Olvidaste tu contraseña?
                </Link>
            </div>
            <BaseButton
              type="submit"
              color="info"
              :icon="mdiLogin"
              label="Acceder"
              title="Acceder"
              :disabled="form.processing"
              :processing="form.processing"
            />
          </div>
        </form>
        <div class="mt-4 text-center text-sm">
          ¿No tienes una cuenta?
          <Link :href="route('register')" 
           class="underline transition-opacity hover:opacity-60">
            Regístrate
          </Link>
        </div>
      </CardContent>
    </Card>
  </GuestLayout>
</template>