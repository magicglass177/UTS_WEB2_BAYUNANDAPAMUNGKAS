<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const patient = ref(null)
const loading = ref(false)
const errorMessage = ref('')

async function fetchPatientDetail() {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get(
      `/api/patients/${route.params.id}`,
    )

    patient.value = response.data.data
  } catch (error) {
    console.error(
      'Gagal mengambil detail pasien:',
      error,
    )

    if (error.response?.status === 404) {
      errorMessage.value =
        'Data pasien tidak ditemukan.'
    } else {
      errorMessage.value =
        'Detail pasien gagal dimuat. Silakan coba lagi.'
    }
  } finally {
    loading.value = false
  }
}

function formatDate(date) {
  if (!date) {
    return '-'
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  }).format(new Date(date))
}

function genderLabel(gender) {
  if (gender === 'L') {
    return 'Laki-laki'
  }

  if (gender === 'P') {
    return 'Perempuan'
  }

  return '-'
}

function goBack() {
  router.push('/patients')
}

onMounted(() => {
  fetchPatientDetail()
})
</script>

<template>
  <div>
    <div class="page-header">
      <div>
        <p class="page-label">
          DETAIL PASIEN
        </p>

        <h1>
          {{ patient?.name ?? 'Detail Pasien' }}
        </h1>

        <p>
          Informasi pasien dan riwayat pemeriksaan.
        </p>
      </div>

      <button
        type="button"
        class="secondary-button"
        @click="goBack"
      >
        ← Kembali
      </button>
    </div>

    <div
      v-if="loading"
      class="state-message"
    >
      Memuat detail pasien...
    </div>

    <div
      v-else-if="errorMessage"
      class="alert-error"
    >
      {{ errorMessage }}
    </div>

    <template v-else-if="patient">
      <section class="detail-card">
        <div class="detail-card-header">
          <div>
            <p class="page-label">
              INFORMASI PASIEN
            </p>

            <h2>{{ patient.name }}</h2>
          </div>

          <span class="detail-badge">
            {{ genderLabel(patient.gender) }}
          </span>
        </div>

        <div class="detail-grid">
          <div class="detail-item">
            <span>NIK</span>
            <strong>{{ patient.nik }}</strong>
          </div>

          <div class="detail-item">
            <span>Jenis Kelamin</span>
            <strong>
              {{ genderLabel(patient.gender) }}
            </strong>
          </div>

          <div class="detail-item">
            <span>Tanggal Lahir</span>
            <strong>
              {{ formatDate(patient.birth_date) }}
            </strong>
          </div>

          <div class="detail-item">
            <span>Nomor Telepon</span>
            <strong>
              {{ patient.phone || '-' }}
            </strong>
          </div>

          <div class="detail-item detail-item-full">
            <span>Alamat</span>
            <strong>
              {{ patient.address || '-' }}
            </strong>
          </div>
        </div>
      </section>

      <section class="detail-card">
        <div class="section-heading">
          <div>
            <p class="page-label">
              RIWAYAT MEDIS
            </p>

            <h2>Riwayat Pemeriksaan</h2>
          </div>

          <span class="record-count">
            {{
              patient.medical_records?.length ?? 0
            }}
            pemeriksaan
          </span>
        </div>

        <div
          v-if="
            !patient.medical_records ||
            patient.medical_records.length === 0
          "
          class="state-message"
        >
          Pasien belum memiliki riwayat pemeriksaan.
        </div>

        <div
          v-else
          class="medical-history"
        >
          <article
            v-for="record in patient.medical_records"
            :key="record.id"
            class="history-item"
          >
            <div class="history-header">
              <div>
                <span class="history-date">
                  {{ formatDate(record.examination_date) }}
                </span>

                <h3>{{ record.diagnosis }}</h3>
              </div>

              <div class="history-user">
                <span>Dicatat oleh</span>
                <strong>
                  {{ record.user?.name ?? '-' }}
                </strong>
              </div>
            </div>

            <div class="history-grid">
              <div class="history-field">
                <span>Keluhan</span>
                <p>{{ record.complaint }}</p>
              </div>

              <div class="history-field">
                <span>Diagnosis</span>
                <p>{{ record.diagnosis }}</p>
              </div>

              <div class="history-field">
                <span>Penanganan</span>
                <p>{{ record.treatment || '-' }}</p>
              </div>

              <div class="history-field">
                <span>Catatan</span>
                <p>{{ record.notes || '-' }}</p>
              </div>
            </div>
          </article>
        </div>
      </section>
    </template>
  </div>
</template>