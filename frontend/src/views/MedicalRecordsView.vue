<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const auth = useAuthStore()
const router = useRouter()

const medicalRecords = ref([])
const patients = ref([])

// Pencarian berdasarkan data pemeriksaan dan identitas pasien/petugas.
const searchQuery = ref('')

const filteredMedicalRecords = computed(() => {
  const keyword = searchQuery.value.trim().toLowerCase()

  if (!keyword) return medicalRecords.value

  return medicalRecords.value.filter((record) => {
    const searchableValues = [
      record.patient?.name,
      record.patient?.nik,
      record.complaint,
      record.diagnosis,
      record.treatment,
      record.user?.name,
    ]

    return searchableValues.some((value) =>
      String(value ?? '').toLowerCase().includes(keyword),
    )
  })
})

const loading = ref(false)
const saving = ref(false)
const showForm = ref(false)
const editingRecordId = ref(null)

const errorMessage = ref('')
const successMessage = ref('')
const validationErrors = ref({})

const form = reactive({
  patient_id: '',
  examination_date: '',
  complaint: '',
  diagnosis: '',
  treatment: '',
  notes: '',
})

async function fetchMedicalRecords() {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get('/api/medical-records')
    medicalRecords.value = response.data.data
  } catch (error) {
    console.error(
      'Gagal mengambil riwayat pemeriksaan:',
      error,
    )

    errorMessage.value =
      'Riwayat pemeriksaan gagal dimuat. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}

async function fetchPatients() {
  try {
    const response = await api.get('/api/patients')
    patients.value = response.data.data
  } catch (error) {
    console.error(
      'Gagal mengambil data pasien:',
      error,
    )

    errorMessage.value =
      'Data pasien gagal dimuat.'
  }
}

function resetForm() {
  form.patient_id = ''
  form.examination_date = ''
  form.complaint = ''
  form.diagnosis = ''
  form.treatment = ''
  form.notes = ''

  editingRecordId.value = null
  validationErrors.value = {}
}

function openCreateForm() {
  resetForm()

  errorMessage.value = ''
  successMessage.value = ''
  showForm.value = true
}

function openEditForm(record) {
  if (!canEdit(record)) {
    return
  }

  resetForm()

  editingRecordId.value = record.id

  form.patient_id = record.patient_id

  form.examination_date = record.examination_date
    ? record.examination_date.slice(0, 10)
    : ''

  form.complaint = record.complaint ?? ''
  form.diagnosis = record.diagnosis ?? ''
  form.treatment = record.treatment ?? ''
  form.notes = record.notes ?? ''

  errorMessage.value = ''
  successMessage.value = ''
  showForm.value = true

  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  })
}

function closeForm() {
  resetForm()
  showForm.value = false
}

async function submitMedicalRecord() {
  saving.value = true
  errorMessage.value = ''
  successMessage.value = ''
  validationErrors.value = {}

  const payload = {
    patient_id: form.patient_id,
    examination_date: form.examination_date,
    complaint: form.complaint,
    diagnosis: form.diagnosis,
    treatment: form.treatment || null,
    notes: form.notes || null,
  }

  try {
    let response

    if (editingRecordId.value) {
      response = await api.put(
        `/api/medical-records/${editingRecordId.value}`,
        payload,
      )
    } else {
      response = await api.post(
        '/api/medical-records',
        payload,
      )
    }

    successMessage.value = response.data.message

    showForm.value = false
    resetForm()

    await fetchMedicalRecords()
  } catch (error) {
    if (error.response?.status === 422) {
      validationErrors.value =
        error.response.data.errors ?? {}

      errorMessage.value =
        'Data belum valid. Periksa kembali form.'
    } else if (error.response?.status === 403) {
      errorMessage.value =
        'Anda tidak diperbolehkan mengedit riwayat pemeriksaan ini.'
    } else if (error.response?.status === 401) {
      errorMessage.value =
        'Sesi login telah berakhir. Silakan login kembali.'
    } else {
      errorMessage.value =
        'Riwayat pemeriksaan gagal disimpan. Silakan coba lagi.'
    }
  } finally {
    saving.value = false
  }
}

function formatDate(date) {
  if (!date) {
    return '-'
  }

  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
  }).format(new Date(date))
}

function canEdit(record) {
  if (auth.isAdmin) {
    return true
  }

  return record.user_id === auth.user?.id
}

function openMedicalRecordDetail(record) {
  router.push({
    name: 'medical-record-detail',
    params: {
      id: record.id,
    },
  })
}

async function deleteMedicalRecord(record) {
  const confirmed = window.confirm(
    `Apakah Anda yakin ingin menghapus riwayat pemeriksaan pasien "${record.patient?.name ?? '-'}"?`,
  )

  if (!confirmed) {
    return
  }

  errorMessage.value = ''

  try {
    const response = await api.delete(
      `/api/medical-records/${record.id}`,
    )

    window.alert(
      response.data.message ??
        'Riwayat pemeriksaan berhasil dihapus.',
    )

    await fetchMedicalRecords()
  } catch (error) {
    console.error(
      'Gagal menghapus riwayat pemeriksaan:',
      error,
    )

    if (error.response?.status === 403) {
      errorMessage.value =
        error.response.data.message ??
        'Anda tidak memiliki izin untuk menghapus riwayat pemeriksaan ini.'

      return
    }

    if (error.response?.status === 404) {
      errorMessage.value =
        'Riwayat pemeriksaan tidak ditemukan.'

      return
    }

    errorMessage.value =
      'Riwayat pemeriksaan gagal dihapus. Silakan coba lagi.'
  }
}

onMounted(async () => {
  await Promise.all([
    fetchMedicalRecords(),
    fetchPatients(),
  ])
})
</script>

<template>
  <div>
    <div class="page-header">
      <div>
        <p class="page-label">
          MODUL UTAMA
        </p>

        <h1>Riwayat Pemeriksaan</h1>

        <p>
          Data riwayat pemeriksaan pasien pada Praktek THT.
        </p>
      </div>

      <button
        type="button"
        class="primary-button"
        @click="openCreateForm"
      >
        + Tambah Pemeriksaan
      </button>
    </div>

    <div
      v-if="successMessage"
      class="alert-success"
    >
      {{ successMessage }}
    </div>

    <div
      v-if="errorMessage"
      class="alert-error"
    >
      {{ errorMessage }}
    </div>

    <section
      v-if="showForm"
      class="form-card"
    >
      <div class="form-card-header">
        <div>
          <h2>
            {{
              editingRecordId
                ? 'Edit Pemeriksaan'
                : 'Tambah Pemeriksaan'
            }}
          </h2>

          <p>
            {{
              editingRecordId
                ? 'Perbarui data pemeriksaan pasien berikut.'
                : 'Lengkapi data pemeriksaan pasien berikut.'
            }}
          </p>
        </div>

        <button
          type="button"
          class="close-button"
          @click="closeForm"
        >
          Batal
        </button>
      </div>

      <form @submit.prevent="submitMedicalRecord">
        <div class="form-grid">
          <div class="form-group">
            <label for="patient_id">
              Pasien
            </label>

            <select
              id="patient_id"
              v-model="form.patient_id"
            >
              <option value="">
                Pilih pasien
              </option>

              <option
                v-for="patient in patients"
                :key="patient.id"
                :value="patient.id"
              >
                {{ patient.name }} - {{ patient.nik }}
              </option>
            </select>

            <small
              v-if="validationErrors.patient_id"
              class="field-error"
            >
              {{ validationErrors.patient_id[0] }}
            </small>
          </div>

          <div class="form-group">
            <label for="examination_date">
              Tanggal Pemeriksaan
            </label>

            <input
              id="examination_date"
              v-model="form.examination_date"
              type="date"
            >

            <small
              v-if="validationErrors.examination_date"
              class="field-error"
            >
              {{ validationErrors.examination_date[0] }}
            </small>
          </div>

          <div class="form-group form-group-full">
            <label for="complaint">
              Keluhan
            </label>

            <textarea
              id="complaint"
              v-model="form.complaint"
              rows="3"
              placeholder="Masukkan keluhan pasien"
            />

            <small
              v-if="validationErrors.complaint"
              class="field-error"
            >
              {{ validationErrors.complaint[0] }}
            </small>
          </div>

          <div class="form-group form-group-full">
            <label for="diagnosis">
              Diagnosis
            </label>

            <input
              id="diagnosis"
              v-model="form.diagnosis"
              type="text"
              maxlength="255"
              placeholder="Masukkan hasil diagnosis"
            >

            <small
              v-if="validationErrors.diagnosis"
              class="field-error"
            >
              {{ validationErrors.diagnosis[0] }}
            </small>
          </div>

          <div class="form-group form-group-full">
            <label for="treatment">
              Penanganan
            </label>

            <textarea
              id="treatment"
              v-model="form.treatment"
              rows="3"
              placeholder="Masukkan penanganan atau terapi"
            />

            <small
              v-if="validationErrors.treatment"
              class="field-error"
            >
              {{ validationErrors.treatment[0] }}
            </small>
          </div>

          <div class="form-group form-group-full">
            <label for="notes">
              Catatan
            </label>

            <textarea
              id="notes"
              v-model="form.notes"
              rows="3"
              placeholder="Catatan tambahan jika diperlukan"
            />

            <small
              v-if="validationErrors.notes"
              class="field-error"
            >
              {{ validationErrors.notes[0] }}
            </small>
          </div>
        </div>

        <div class="form-actions">
          <button
            type="button"
            class="secondary-button"
            :disabled="saving"
            @click="closeForm"
          >
            Batal
          </button>

          <button
            type="submit"
            class="primary-button"
            :disabled="saving"
          >
            {{
              saving
                ? 'Menyimpan...'
                : editingRecordId
                  ? 'Simpan Perubahan'
                  : 'Simpan Pemeriksaan'
            }}
          </button>
        </div>
      </form>
    </section>

    <section class="data-card">
      <!-- Pencarian daftar riwayat; tidak memengaruhi pilihan pasien di form. -->
      <div
        v-if="!loading && medicalRecords.length > 0"
        class="search-container"
      >
        <div class="search-box">
          <span class="search-icon">🔍</span>
          <input
            v-model="searchQuery"
            type="search"
            class="search-input"
            placeholder="Cari pasien, NIK, keluhan, diagnosis, penanganan, atau petugas..."
            aria-label="Cari riwayat pemeriksaan"
          >
        </div>

        <span v-if="searchQuery" class="search-result-count">
          {{ filteredMedicalRecords.length }} data ditemukan
        </span>
      </div>
      <div
        v-if="loading"
        class="state-message"
      >
        Memuat riwayat pemeriksaan...
      </div>

      <div
        v-else-if="medicalRecords.length === 0"
        class="state-message"
      >
        Belum ada riwayat pemeriksaan.
      </div>

      <div
        v-else-if="filteredMedicalRecords.length === 0"
        class="state-message"
      >
        Riwayat pemeriksaan yang dicari tidak ditemukan.
      </div>

      <div
        v-else
        class="table-wrapper"
      >
        <table class="data-table">
          <thead>
            <tr>
              <th>No.</th>
              <th>Tanggal</th>
              <th>Pasien</th>
              <th>Keluhan</th>
              <th>Diagnosis</th>
              <th>Penanganan</th>
              <th>Petugas</th>
              <th>Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(record, index) in filteredMedicalRecords"
              :key="record.id"
            >
              <td>{{ index + 1 }}</td>

              <td>
                {{ formatDate(record.examination_date) }}
              </td>

              <td>
                <strong>
                  {{ record.patient?.name ?? '-' }}
                </strong>

                <div class="table-secondary">
                  NIK:
                  {{ record.patient?.nik ?? '-' }}
                </div>
              </td>

              <td>
                {{ record.complaint }}
              </td>

              <td>
                {{ record.diagnosis }}
              </td>

              <td>
                {{ record.treatment || '-' }}
              </td>

              <td>
                {{ record.user?.name ?? '-' }}

                <div class="table-secondary">
                  {{ record.user?.role ?? '-' }}
                </div>
              </td>

              <td>
  <div class="action-buttons">
    <button
      type="button"
      class="detail-button"
      @click="openMedicalRecordDetail(record)"
    >
      Detail
    </button>

    <button
      v-if="canEdit(record)"
      type="button"
      class="edit-button"
      @click="openEditForm(record)"
    >
      Edit
    </button>

    <button
      v-if="canEdit(record)"
      type="button"
      class="delete-button"
      @click="deleteMedicalRecord(record)"
    >
      Hapus
    </button>
  </div>
</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </div>
</template>
