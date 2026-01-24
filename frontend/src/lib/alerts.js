import Swal from 'sweetalert2'

function isDarkMode() {
  return document.documentElement.classList.contains('dark')
}

function toastBase() {
  return Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    didOpen: (popup) => {
      const dark = isDarkMode()
      popup.style.background = dark ? '#0b1220' : '#ffffff'
      popup.style.color = dark ? '#e5e7eb' : '#0f172a'
      popup.style.borderColor = dark ? 'rgba(148,163,184,.22)' : 'rgba(15,23,42,.12)'
    },
  })
}

export const alerts = {
  success(title, text) {
    return toastBase().fire({ icon: 'success', title, text })
  },
  error(title, text) {
    return toastBase().fire({ icon: 'error', title, text })
  },
  info(title, text) {
    return toastBase().fire({ icon: 'info', title, text })
  },
  warning(title, text) {
    return toastBase().fire({ icon: 'warning', title, text })
  },
}

