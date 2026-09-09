import axios from 'axios'

// Buat instance axios dengan base URL API Laravel
const api = axios.create({
    baseURL: 'http://localhost:8000/api'
})

// Interceptor: otomatis tambahkan token di sxetiap request
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token')
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

export default api