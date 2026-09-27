import { defineStore } from 'pinia'
import { computed, ref } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const loading = ref(false)
  const initialized = ref(false)

  const isAuthenticated = computed(() => user.value !== null)

  const isAdmin = computed(() => {
    return user.value?.role === 'admin'
  })

  const isPetugas = computed(() => {
    return user.value?.role === 'petugas'
  })

  async function getCsrfCookie() {
    await api.get('/sanctum/csrf-cookie')
  }

  async function login(credentials) {
    loading.value = true

    try {
      await getCsrfCookie()

      const response = await api.post('/login', credentials)

      user.value = response.data.user

      return response.data
    } finally {
      loading.value = false
    }
  }

  async function fetchUser() {
  try {
    const response = await api.get('/api/user')

    user.value = response.data

    return response.data
  } catch (error) {
    if (error.response?.status === 401) {
      user.value = null
      return null
    }

    if (!error.response) {
      console.error(
        'Backend tidak dapat dihubungi:',
        error,
      )

      return null
    }

    throw error
  } finally {
    initialized.value = true
  }
}

  async function logout() {
    loading.value = true

    try {
      await getCsrfCookie()
      await api.post('/logout')

      user.value = null
    } finally {
      loading.value = false
    }
  }

  return {
    user,
    loading,
    initialized,
    isAuthenticated,
    isAdmin,
    isPetugas,
    login,
    fetchUser,
    logout,
  }
})