import axios from 'axios'

// Base URL of the Laravel API, e.g. https://dropnsharee.arhamnatiq.com/api
// Set per environment in .env / .env.production (VITE_API_BASE_URL).
const baseURL = (import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api').replace(/\/+$/, '')

const api = axios.create({
  baseURL,
  // Uploads can be large and slow; don't cut them off.
  timeout: 0,
  headers: { Accept: 'application/json' },
})

/** Pull a readable message out of an axios error. */
export function apiErrorMessage(error, fallback = 'Something went wrong. Please try again.') {
  if (error?.response) {
    if (error.response.status === 413) {
      return 'Total file size exceeds the server limit.'
    }
    const data = error.response.data
    if (data?.error) return data.error
    if (data?.message) return data.message
  }
  if (error?.code === 'ERR_NETWORK') {
    return 'Could not reach the server. Please check your connection and try again.'
  }
  return fallback
}

export { baseURL }
export default api
