import { computed, ref } from 'vue'
import { clearAuth, getToken, getUser, setAuth as persistAuth } from '../lib/auth'

const token = ref(getToken())
const user = ref(getUser())

export function useAuth() {
  const isAuthed = computed(() => Boolean(token.value))

  const setSession = (nextToken, nextUser) => {
    token.value = nextToken || ''
    user.value = nextUser || null
    persistAuth(token.value, user.value)
  }

  const logoutLocal = () => {
    token.value = ''
    user.value = null
    clearAuth()
  }

  return {
    token,
    user,
    isAuthed,
    setSession,
    logoutLocal,
  }
}

