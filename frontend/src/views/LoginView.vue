<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const form = reactive({
  email: '',
  password: '',
})

const errorMessage = ref('')
const validationErrors = ref({})

async function handleLogin() {
  errorMessage.value = ''
  validationErrors.value = {}

  try {
    await auth.login({
      email: form.email,
      password: form.password,
    })

    await router.push('/')
  } catch (error) {
    if (error.response?.status === 422) {
      validationErrors.value = error.response.data.errors ?? {}
      errorMessage.value =
        error.response.data.message ?? 'Email atau password tidak sesuai.'
      return
    }

    errorMessage.value =
      'Terjadi kesalahan saat menghubungi server. Silakan coba lagi.'
  }
}
</script>

<template>
  <main class="login-page">
    <section class="login-card">
      <div class="login-header">
        <p class="login-label">PRAKTEK THT</p>

        <h1>Sistem Informasi Data Riwayat Pasien</h1>

        <p>
          Silakan masuk menggunakan akun Admin atau Petugas.
        </p>
      </div>

      <form @submit.prevent="handleLogin">
        <div
          v-if="errorMessage"
          class="alert-error"
        >
          {{ errorMessage }}
        </div>

        <div class="form-group">
          <label for="email">Email</label>

          <input
            id="email"
            v-model="form.email"
            type="email"
            placeholder="Masukkan email"
            autocomplete="email"
          >

          <small
            v-if="validationErrors.email"
            class="field-error"
          >
            {{ validationErrors.email[0] }}
          </small>
        </div>

        <div class="form-group">
          <label for="password">Password</label>

          <input
            id="password"
            v-model="form.password"
            type="password"
            placeholder="Masukkan password"
            autocomplete="current-password"
          >

          <small
            v-if="validationErrors.password"
            class="field-error"
          >
            {{ validationErrors.password[0] }}
          </small>
        </div>

        <button
          type="submit"
          class="login-button"
          :disabled="auth.loading"
        >
          {{ auth.loading ? 'Memproses...' : 'Masuk' }}
        </button>
      </form>
    </section>
  </main>
</template>