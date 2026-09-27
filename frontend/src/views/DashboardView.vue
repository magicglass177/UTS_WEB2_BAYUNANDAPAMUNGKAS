<script setup>
import {
  computed,
  onMounted,
  ref,
} from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const auth = useAuthStore()

const statistics = ref({
  total_patients: 0,
  total_medical_records: 0,
  today_medical_records: 0,
})

const patients = ref([])
const searchQuery = ref('')

const loading = ref(false)
const errorMessage = ref('')

const filteredPatients = computed(() => {
  const keyword = searchQuery.value
    .trim()
    .toLowerCase()

  if (!keyword) {
    return []
  }

  return patients.value.filter((patient) => {
    const searchableValues = [
      patient.name,
      patient.nik,
    ]

    return searchableValues.some((value) =>
      String(value ?? '')
        .toLowerCase()
        .includes(keyword),
    )
  })
})

async function fetchDashboard() {
  loading.value = true
  errorMessage.value = ''

  try {
    const [
      dashboardResponse,
      patientsResponse,
    ] = await Promise.all([
      api.get('/api/dashboard'),
      api.get('/api/patients'),
    ])

    statistics.value =
      dashboardResponse.data.data

    patients.value =
      patientsResponse.data.data
  } catch (error) {
    console.error(
      'Gagal mengambil data dashboard:',
      error,
    )

    errorMessage.value =
      'Data dashboard gagal dimuat. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}

function openPatients() {
  router.push({
    name: 'patients',
  })
}

function openMedicalRecords() {
  router.push({
    name: 'medical-records',
  })
}

function openPatientDetail(patient) {
  router.push({
    name: 'patient-detail',
    params: {
      id: patient.id,
    },
  })
}

function clearSearch() {
  searchQuery.value = ''
}

onMounted(() => {
  fetchDashboard()
})
</script>

<template>
  <div>
    <div class="page-header">
      <div>
        <p class="page-label">
          DASHBOARD
        </p>

        <h1>
          Selamat Datang, {{ auth.user?.name ?? 'Pengguna' }}
        </h1>

        <p>
          Ringkasan Sistem Informasi Data Riwayat Pasien
          Praktek THT.
        </p>
      </div>
    </div>

    <div
      v-if="errorMessage"
      class="alert-error"
    >
      {{ errorMessage }}
    </div>

    <div
      v-if="loading"
      class="state-message"
    >
      Memuat data dashboard...
    </div>

    <template v-else>
      <!-- STATISTIK -->
      <section class="dashboard-stats">
        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">
              Total Pasien
            </span>

            <span class="stat-icon">
              P
            </span>
          </div>

          <strong class="stat-number">
            {{ statistics.total_patients }}
          </strong>

          <p>
            Pasien yang terdaftar dalam sistem.
          </p>
        </div>

        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">
              Total Pemeriksaan
            </span>

            <span class="stat-icon">
              R
            </span>
          </div>

          <strong class="stat-number">
            {{ statistics.total_medical_records }}
          </strong>

          <p>
            Seluruh riwayat pemeriksaan yang tersimpan.
          </p>
        </div>

        <div class="stat-card">
          <div class="stat-card-header">
            <span class="stat-label">
              Pemeriksaan Hari Ini
            </span>

            <span class="stat-icon">
              H
            </span>
          </div>

          <strong class="stat-number">
            {{ statistics.today_medical_records }}
          </strong>

          <p>
            Pemeriksaan yang tercatat pada hari ini.
          </p>
        </div>
      </section>

      <!-- PENCARIAN CEPAT PASIEN -->
      <section class="dashboard-section">
        <div class="section-heading">
          <div>
            <p class="page-label">
              PENCARIAN CEPAT
            </p>

            <h2>Cari Pasien</h2>

            <p>
              Cari pasien berdasarkan nama atau NIK
              untuk membuka detail pasien.
            </p>
          </div>
        </div>

        <div class="dashboard-search">
          <div class="search-box dashboard-search-box">
            <span class="search-icon">
              🔍
            </span>

            <input
              v-model="searchQuery"
              type="search"
              class="search-input"
              placeholder="Cari nama atau NIK pasien..."
            >

            <button
              v-if="searchQuery"
              type="button"
              class="search-clear-button"
              title="Hapus pencarian"
              @click="clearSearch"
            >
              ×
            </button>
          </div>

          <div
            v-if="searchQuery.trim()"
            class="dashboard-search-results"
          >
            <div
              v-if="filteredPatients.length === 0"
              class="dashboard-search-empty"
            >
              Pasien yang dicari tidak ditemukan.
            </div>

            <button
              v-for="patient in filteredPatients"
              v-else
              :key="patient.id"
              type="button"
              class="dashboard-search-result"
              @click="openPatientDetail(patient)"
            >
              <div>
                <strong>
                  {{ patient.name }}
                </strong>

                <span>
                  NIK: {{ patient.nik }}
                </span>
              </div>

              <span class="dashboard-search-action">
                Lihat Detail →
              </span>
            </button>
          </div>
        </div>
      </section>

      <!-- AKSES CEPAT -->
      <section class="dashboard-section">
        <div class="section-heading">
          <div>
            <p class="page-label">
              AKSES CEPAT
            </p>

            <h2>Menu Utama</h2>

            <p>
              Pilih menu untuk mengelola data sistem.
            </p>
          </div>
        </div>

        <div class="quick-menu-grid">
          <button
            type="button"
            class="quick-menu-card"
            @click="openPatients"
          >
            <div class="quick-menu-icon">
              P
            </div>

            <div>
              <h3>Data Pasien</h3>

              <p>
                Lihat data pasien dan riwayat
                pemeriksaannya.
              </p>
            </div>

            <span class="quick-menu-arrow">
              →
            </span>
          </button>

          <button
            type="button"
            class="quick-menu-card"
            @click="openMedicalRecords"
          >
            <div class="quick-menu-icon">
              R
            </div>

            <div>
              <h3>Riwayat Pemeriksaan</h3>

              <p>
                Lihat dan kelola data pemeriksaan pasien.
              </p>
            </div>

            <span class="quick-menu-arrow">
              →
            </span>
          </button>
        </div>
      </section>

      <!-- AKUN AKTIF -->
      <section class="dashboard-section">
        <div class="dashboard-user-info">
          <div>
            <p class="page-label">
              AKUN AKTIF
            </p>

            <h2>
              {{ auth.user?.name ?? '-' }}
            </h2>

            <p>
              {{ auth.user?.email ?? '-' }}
            </p>
          </div>

          <span class="detail-badge">
            {{ auth.user?.role ?? '-' }}
          </span>
        </div>
      </section>
    </template>
  </div>
</template>