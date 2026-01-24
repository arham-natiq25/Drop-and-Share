import axios from 'axios'
import { getToken } from './auth'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'

export const http = axios.create({
  baseURL: API_BASE_URL,
})

http.interceptors.request.use((config) => {
  const token = getToken()
  if (token) {
    config.headers = config.headers || {}
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

