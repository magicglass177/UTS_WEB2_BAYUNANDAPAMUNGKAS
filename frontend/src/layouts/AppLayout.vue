<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

async function handleLogout() {
  try {
    await auth.logout()
    await router.push('/login')
  } catch (error) {
    console.error('Logout gagal:', error)
  }
}
</script>

<template>
  <div class="app-layout">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <p class="sidebar-label">
          PRAKTEK THT
        </p>

        <h2>Riwayat Pasien</h2>
      </div>

      <nav class="sidebar-nav">
        <RouterLink
          to="/"
          class="nav-link"
        >
          Dashboard
        </RouterLink>

        <RouterLink
          to="/patients"
          class="nav-link"
        >
          Data Pasien
        </RouterLink>

        <RouterLink
          to="/medical-records"
          class="nav-link"
        >
          Riwayat Pemeriksaan
        </RouterLink>
      </nav>

      <div class="sidebar-user">
        <div v-if="auth.user">
          <strong>{{ auth.user.name }}</strong>

          <span>
            {{ auth.user.role }}
          </span>
        </div>

        <button
          type="button"
          class="logout-button"
          :disabled="auth.loading"
          @click="handleLogout"
        >
          {{ auth.loading ? 'Memproses...' : 'Logout' }}
        </button>
      </div>
    </aside>

    <main class="main-content">
      <RouterView />
    </main>
  </div>
</template>