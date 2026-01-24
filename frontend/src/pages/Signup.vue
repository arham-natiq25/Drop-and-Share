<template>
  <div class="rounded-3xl border border-slate-200 bg-white/75 p-6 shadow-soft dark:border-slate-800 dark:bg-slate-950/50 sm:p-8">
    <div class="mb-6">
      <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Create your account</h1>
      <p class="mt-1 text-sm text-slate-600 dark:text-slate-300">Sign up to get a higher daily upload limit.</p>
    </div>

    <form class="space-y-4" @submit.prevent="submit">
      <div>
        <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-200">Full name</label>
        <input
          v-model.trim="name"
          type="text"
          autocomplete="name"
          placeholder="Your name"
          class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-slate-900 outline-none ring-violet-500/30 focus:ring-4 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
        />
      </div>

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

      <div class="grid gap-4 sm:grid-cols-2">
        <div>
          <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-200">Password</label>
          <input
            v-model="password"
            type="password"
            autocomplete="new-password"
            placeholder="••••••••"
            class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-slate-900 outline-none ring-violet-500/30 focus:ring-4 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
          />
        </div>

        <div>
          <label class="mb-1 block text-sm font-semibold text-slate-700 dark:text-slate-200">Confirm</label>
          <input
            v-model="confirmPassword"
            type="password"
            autocomplete="new-password"
            placeholder="••••••••"
            class="h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-slate-900 outline-none ring-violet-500/30 focus:ring-4 dark:border-slate-800 dark:bg-slate-950 dark:text-slate-100"
          />
        </div>
      </div>

      <button
        type="submit"
        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-soft hover:from-violet-700 hover:to-blue-700"
      >
        <UserPlusIcon class="h-5 w-5" />
        Create account
      </button>

      <p class="text-center text-sm text-slate-600 dark:text-slate-300">
        Already have an account?
        <RouterLink class="font-semibold text-violet-600 hover:underline dark:text-violet-400" to="/login">
          Login
        </RouterLink>
      </p>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { UserPlusIcon } from 'lucide-vue-next'
import { alerts } from '../lib/alerts'
import { http } from '../lib/http'
import { useAuth } from '../composables/useAuth'

const router = useRouter()
const { setSession } = useAuth()

const name = ref('')
const email = ref('')
const password = ref('')
const confirmPassword = ref('')

async function submit() {
  if (!name.value) return alerts.error('Missing name', 'Please enter your full name.')
  if (!email.value) return alerts.error('Missing email', 'Please enter your email.')
  if (!password.value) return alerts.error('Missing password', 'Please set a password.')
  if (password.value.length < 6) return alerts.error('Weak password', 'Use at least 6 characters.')
  if (confirmPassword.value !== password.value) return alerts.error('Password mismatch', 'Passwords do not match.')

  try {
    const res = await http.post('/auth/register', {
      name: name.value,
      email: email.value,
      password: password.value,
    })
    setSession(res.data?.token, res.data?.user)
    alerts.success('Account created', 'Welcome!')
    router.push('/upload')
  } catch (e) {
    const msg =
      e?.response?.data?.message ||
      e?.response?.data?.error ||
      (Array.isArray(e?.response?.data?.email) ? e.response.data.email[0] : null) ||
      'Sign up failed. Please try again.'
    alerts.error('Sign up failed', msg)
  }
}
</script>

