<script setup>
import { Head, Link } from '@inertiajs/vue3'
import ForumLayout from '@/Layouts/ForumLayout.vue'

defineProps({
  canLogin: { type: Boolean, default: true },
  canRegister: { type: Boolean, default: true },
  laravelVersion: { type: String, required: true },
  phpVersion: { type: String, required: true },
})


</script>

<template>

  <Head>
    <title>DiabCare — Sistema para evaluación de riesgo y prevención de la diabetes</title>
    <meta name="description"
      content="Plataforma web para registrar mediciones, gestionar planes, citas y recomendaciones, con reportes para apoyo clínico." />
  </Head>
  <ForumLayout>
    <div class="min-h-screen bg-gradient-to-b from-medic-50 via-white to-medic-50 text-medic-800">
      <!-- Top bar -->

      <!-- Hero -->
      <section class="relative">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-14 md:py-20 grid md:grid-cols-2 gap-10 items-center">
          <div>
            <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight text-medic-800">
              Evaluación de riesgo y prevención de la <span class="text-medic-600">diabetes</span>.
            </h1>
            <p class="mt-4 text-lg text-medic-600">
              Registra mediciones (glucosa, peso, presión), gestiona citas, planes de alimentación/actividad y
              recomendaciones. Visualiza reportes claros para la toma de decisiones clínicas.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
              <Link v-if="canRegister && !$page.props.auth?.user" :href="route('register')"
                class="px-6 py-3 rounded-xl bg-medic-600 text-white font-semibold hover:bg-medic-700 shadow">Comenzar
              </Link>
              <Link v-if="canLogin && !$page.props.auth?.user" :href="route('login')"
                class="px-6 py-3 rounded-xl border border-medic-300 text-medic-700 font-semibold hover:bg-medic-50">
              Entrar</Link>
              <Link v-if="canLogin && $page.props.auth?.user" :href="route('dashboard')"
                class="px-6 py-3 rounded-xl border border-medic-300 text-medic-700 font-semibold hover:bg-medic-50">
              Dashboard</Link>
            </div>
          </div>

          <div class="relative">
            <div class="rounded-2xl border border-medic-100 bg-white p-5 shadow-lg">
              <img src="imgHome.png" alt="Imagen Home" class="w-80 h-80 object-contain mx-auto" />
            </div>
            <div class="hidden md:block absolute -bottom-6 -left-6 w-24 h-24 rounded-2xl bg-medic-100/60 blur-2xl">
            </div>
            <div class="hidden md:block absolute -top-6 -right-6 w-24 h-24 rounded-2xl bg-medic-200/60 blur-2xl"></div>
          </div>
        </div>
      </section>

      <!-- Modules -->
      <section id="modules" class="py-14 md:py-20 bg-white border-y border-medic-100">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
          <h2 class="text-2xl md:text-3xl font-extrabold text-medic-800">Módulos del sistema</h2>
          <p class="mt-2 text-medic-600">Organizado por catálogos (CRUD) y módulos operativos.</p>

          <div class="mt-8 grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Catálogos / CRUD -->
            <div class="rounded-2xl border border-medic-100 bg-medic-50 p-5">
              <h3 class="font-semibold text-medic-800">Autenticación y usuarios</h3>
              <p class="mt-2 text-sm text-medic-600">Inicio de sesión seguro y administración de cuentas por rol.</p>
            </div>
            <div class="rounded-2xl border border-medic-100 bg-medic-50 p-5">
              <h3 class="font-semibold text-medic-800">Alimentos y ejercicios</h3>
              <p class="mt-2 text-sm text-medic-600">Catálogos para planes y cálculo de Kcal / intensidad.</p>
            </div>
            <div class="rounded-2xl border border-medic-100 bg-medic-50 p-5">
              <h3 class="font-semibold text-medic-800">Medicamentos</h3>
              <p class="mt-2 text-sm text-medic-600">Dosis, contraindicaciones y estatus.</p>
            </div>
            <div class="rounded-2xl border border-medic-100 bg-medic-50 p-5">
              <h3 class="font-semibold text-medic-800">Citas</h3>
              <p class="mt-2 text-sm text-medic-600">Agenda, modalidades y control de estatus.</p>
            </div>

            <!-- Operativos / Especiales -->
            <div class="rounded-2xl border border-medic-100 bg-white p-5">
              <h3 class="font-semibold text-medic-800">Monitoreos clínicos</h3>
              <p class="mt-2 text-sm text-medic-600">Glucosa, peso y presión con validación de rangos.</p>
            </div>
            <div class="rounded-2xl border border-medic-100 bg-white p-5">
              <h3 class="font-semibold text-medic-800">Asignación de planes</h3>
              <p class="mt-2 text-sm text-medic-600">Planes de alimentación y actividad con vigencia.</p>
            </div>
            <div class="rounded-2xl border border-medic-100 bg-white p-5">
              <h3 class="font-semibold text-medic-800">Bitácora clínica</h3>
              <p class="mt-2 text-sm text-medic-600">Notas, evidencias y relación con citas/planes.</p>
            </div>
            <div class="rounded-2xl border border-medic-100 bg-white p-5">
              <h3 class="font-semibold text-medic-800">Foro y notificaciones</h3>
              <p class="mt-2 text-sm text-medic-600">Dudas moderadas y avisos por correo/sistema.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Final CTA -->
      <section class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-2xl md:text-3xl font-extrabold text-medic-800">Listo para probar DiabCare</h2>
          <p class="mt-2 text-medic-600">Crea tu cuenta o entra con tu perfil para comenzar.</p>
          <div class="mt-6 flex justify-center gap-3">
            <Link v-if="canRegister && !$page.props.auth?.user" :href="route('register')"
              class="px-6 py-3 rounded-xl bg-medic-600 text-white font-semibold hover:bg-medic-700 shadow">Crear cuenta
            </Link>
            <Link v-if="canLogin && !$page.props.auth?.user" :href="route('login')"
              class="px-6 py-3 rounded-xl border border-medic-300 text-medic-700 font-semibold hover:bg-medic-50">
            Iniciar sesión</Link>
            <Link v-if="canLogin && $page.props.auth?.user" :href="route('dashboard')"
              class="px-6 py-3 rounded-xl border border-medic-300 text-medic-700 font-semibold hover:bg-medic-50">
            Dashboard</Link>
          </div>
        </div>
      </section>

      <!-- Footer -->
      <footer class="border-t border-medic-100 bg-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 text-sm text-medic-500">
          <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-4">
            <div class="text-center md:text-left">
              <p>DiabCare &copy; 2025</p>
              <p class="mt-1">Laravel v{{ laravelVersion }} • PHP v{{ phpVersion }}</p>
            </div>
            <div class="flex gap-6">
              <a href="#modules" class="hover:text-medic-700">Módulos</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  </ForumLayout>
</template>
