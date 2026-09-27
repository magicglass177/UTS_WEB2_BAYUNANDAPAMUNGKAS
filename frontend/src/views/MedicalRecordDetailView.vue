<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()

const medicalRecord = ref(null)
const loading = ref(false)
const errorMessage = ref('')

async function fetchMedicalRecordDetail() {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get(
      `/api/medical-records/${route.params.id}`,
    )

    medicalRecord.value = response.data.data
  } catch (error) {
    console.error(
      'Gagal mengambil detail pemeriksaan:',
      error,
    )

    if (error.response?.status === 404) {
      errorMessage.value =
        'Riwayat pemeriksaan tidak ditemukan.'
    } else {
      errorMessage.value =
        'Detail pemeriksaan gagal dimuat. Silakan coba lagi.'
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

/*
|--------------------------------------------------------------------------
| Kembali ke halaman Riwayat Pemeriksaan
|--------------------------------------------------------------------------
*/
function goBack() {
  router.push({
    name: 'medical-records',
  })
}

/*
|--------------------------------------------------------------------------
| Buka detail pasien
|--------------------------------------------------------------------------
*/
function openPatientDetail() {
  if (!medicalRecord.value?.patient?.id) {
    return
  }

  router.push({
    name: 'patient-detail',
    params: {
      id: medicalRecord.value.patient.id,
    },
  })
}

onMounted(() => {
  fetchMedicalRecordDetail()
})
</script>

<template>
  <div>
    <div class="page-header">
      <div>
        <p class="page-label">
          DETAIL PEMERIKSAAN
        </p>

        <h1>Detail Riwayat Pemeriksaan</h1>

        <p>
          Informasi lengkap hasil pemeriksaan pasien.
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
      Memuat detail pemeriksaan...
    </div>

    <div
      v-else-if="errorMessage"
      class="alert-error"
    >
      {{ errorMessage }}
    </div>

    <template v-else-if="medicalRecord">
      <!-- INFORMASI PASIEN -->
      <section class="detail-card">
        <div class="detail-card-header">
          <div>
            <p class="page-label">
              INFORMASI PASIEN
            </p>

            <h2>
              {{ medicalRecord.patient?.name ?? '-' }}
            </h2>
          </div>

          <button
            type="button"
            class="detail-button"
            @click="openPatientDetail"
          >
            Lihat Detail Pasien
          </button>
        </div>

        <div class="detail-grid">
          <div class="detail-item">
            <span>NIK</span>

            <strong>
              {{ medicalRecord.patient?.nik ?? '-' }}
            </strong>
          </div>

          <div class="detail-item">
            <span>Jenis Kelamin</span>

            <strong>
              {{
                genderLabel(
                  medicalRecord.patient?.gender,
                )
              }}
            </strong>
          </div>

          <div class="detail-item">
            <span>Tanggal Lahir</span>

            <strong>
              {{
                formatDate(
                  medicalRecord.patient?.birth_date,
                )
              }}
            </strong>
          </div>

          <div class="detail-item">
            <span>Nomor Telepon</span>

            <strong>
              {{ medicalRecord.patient?.phone || '-' }}
            </strong>
          </div>

          <div class="detail-item detail-item-full">
            <span>Alamat</span>

            <strong>
              {{ medicalRecord.patient?.address || '-' }}
            </strong>
          </div>
        </div>
      </section>

      <!-- HASIL PEMERIKSAAN -->
      <section class="detail-card">
        <div class="detail-card-header">
          <div>
            <p class="page-label">
              HASIL PEMERIKSAAN
            </p>

            <h2>
              {{ medicalRecord.diagnosis }}
            </h2>
          </div>

          <span class="detail-badge">
            {{
              formatDate(
                medicalRecord.examination_date,
              )
            }}
          </span>
        </div>

        <div class="history-grid">
          <div class="history-field">
            <span>Keluhan</span>

            <p>
              {{ medicalRecord.complaint }}
            </p>
          </div>

          <div class="history-field">
            <span>Diagnosis</span>

            <p>
              {{ medicalRecord.diagnosis }}
            </p>
          </div>

          <div class="history-field">
            <span>Penanganan</span>

            <p>
              {{ medicalRecord.treatment || '-' }}
            </p>
          </div>

          <div class="history-field">
            <span>Catatan</span>

            <p>
              {{ medicalRecord.notes || '-' }}
            </p>
          </div>
        </div>
      </section>

      <!-- INFORMASI PETUGAS -->
      <section class="detail-card">
        <div class="section-heading">
          <div>
            <p class="page-label">
              PETUGAS
            </p>

            <h2>Informasi Pencatat</h2>
          </div>
        </div>

        <div class="detail-grid">
          <div class="detail-item">
            <span>Nama</span>

            <strong>
              {{ medicalRecord.user?.name ?? '-' }}
            </strong>
          </div>

          <div class="detail-item">
            <span>Role</span>

            <strong>
              {{ medicalRecord.user?.role ?? '-' }}
            </strong>
          </div>

          <div class="detail-item detail-item-full">
            <span>Email</span>

            <strong>
              {{ medicalRecord.user?.email ?? '-' }}
            </strong>
          </div>
        </div>
      </section>
    </template>
  </div>
</template>