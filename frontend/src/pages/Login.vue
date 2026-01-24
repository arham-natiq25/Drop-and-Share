<template>
  <div class="rounded-3xl border border-slate-200 bg-white/75 p-6 shadow-soft dark:border-slate-800 dark:bg-slate-950/50 sm:p-8">
    <div class="mb-6">
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Welcome back</h1>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Login to increase your daily upload limit.</p>
    </div>

    <form class="space-y-4" @submit.prevent="submit">
      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-200">Email</label>
        <input
          v-model.trim="email"
          type="email"
          autocomplete="email"
          placeholder="you@example.com"
          class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-slate-900 outline-none ring-violet-500/30 focus:ring-4 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
        />
      </div>

      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-200">Password</label>
        <input
          v-model="password"
          type="password"
          autocomplete="current-password"
          placeholder="••••••••"
          class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-slate-900 outline-none ring-violet-500/30 focus:ring-4 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
        />
      </div>

      <button
        type="submit"
        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-soft hover:from-violet-700 hover:to-blue-700"
      >
        <LogInIcon class="h-5 w-5" />
        Login
      </button>

      <p class="text-center text-sm text-slate-600 dark:text-slate-300">
        Don’t have an account?
        <RouterLink class="font-semibold text-violet-600 hover:underline dark:text-violet-400" to="/signup">
          Sign up
        </RouterLink>
      </p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { LogInIcon } from 'lucide-vue-next'
import { alerts } from '../lib/alerts'
import { http } from '../lib/http'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { setSession } = useAuth()

const email = ref('')
const password = ref('')

async function submit() {
  if (!email.value) return alerts.error('Missing email', 'Please enter your email.')
  if (!password.value) return alerts.error('Missing password', 'Please enter your password.')

  try {
    const res = await http.post('/auth/login', {
      email: email.value,
      password: password.value,
    })
    setSession(res.data?.token, res.data?.user)
    alerts.success('Logged in', 'Welcome back.')
    router.push('/upload')
  } catch (e) {
    const msg =
      e?.response?.data?.message ||
      e?.response?.data?.error ||
      (Array.isArray(e?.response?.data?.email) ? e.response.data.email[0] : null) ||
      'Login failed. Please check your credentials.'
    alerts.error('Login failed', msg)
  }
}
</script>

