import { computed, ref } from 'vue'

const THEME_KEY = 'dns_theme'
const theme = ref(localStorage.getItem(THEME_KEY) || 'dark')

const isDark = computed(() => theme.value === 'dark')

function applyTheme() {
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.setItem(THEME_KEY, theme.value)
}

applyTheme()

export function useTheme() {
  const toggleTheme = () => {
    theme.value = isDark.value ? 'light' : 'dark'
    applyTheme()
  }

  const setTheme = (next) => {
    theme.value = next === 'dark' ? 'dark' : 'light'
    applyTheme()
  }

  return {
    theme,
    isDark,
    toggleTheme,
    setTheme,
  }
}

