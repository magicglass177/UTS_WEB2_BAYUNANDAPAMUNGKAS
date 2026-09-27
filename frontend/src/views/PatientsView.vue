<script setup>
import {
  computed,
  onMounted,
  reactive,
  ref,
} from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api'
import { useAuthStore } from '../stores/auth'

const auth = useAuthStore()
const router = useRouter()

const patients = ref([])
const loading = ref(false)
const saving = ref(false)
const showForm = ref(false)
const editingPatientId = ref(null)

const errorMessage = ref('')
const successMessage = ref('')
const validationErrors = ref({})

// ================================
// Pencarian Pasien
// ================================

const searchQuery = ref('')

const filteredPatients = computed(() => {
  const keyword = searchQuery.value
    .trim()
    .toLowerCase()

  if (!keyword) {
    return patients.value
  }

  return patients.value.filter((patient) => {
    const searchableValues = [
      patient.nik,
      patient.name,
      patient.phone,
      patient.address,
    ]

    return searchableValues.some((value) =>
      String(value ?? '')
        .toLowerCase()
        .includes(keyword),
    )
  })
})

const form = reactive({
  nik: '',
  name: '',
  gender: '',
  birth_date: '',
  phone: '',
  address: '',
})

async function fetchPatients() {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await api.get('/api/patients')
    patients.value = response.data.data
  } catch (error) {
    console.error('Gagal mengambil data pasien:', error)

    errorMessage.value =
      'Data pasien gagal dimuat. Silakan coba lagi.'
  } finally {
    loading.value = false
  }
}

function resetForm() {
  form.nik = ''
  form.name = ''
  form.gender = ''
  form.birth_date = ''
  form.phone = ''
  form.address = ''

  editingPatientId.value = null
  validationErrors.value = {}
}

function openCreateForm() {
  resetForm()

  errorMessage.value = ''
  successMessage.value = ''
  showForm.value = true
}

function openEditForm(patient) {
  resetForm()

  editingPatientId.value = patient.id

  form.nik = patient.nik
  form.name = patient.name
  form.gender = patient.gender
  form.birth_date = patient.birth_date
    ? patient.birth_date.slice(0, 10)
    : ''
  form.phone = patient.phone ?? ''
  form.address = patient.address ?? ''

  errorMessage.value = ''
  successMessage.value = ''
  showForm.value = true

  window.scrollTo({
    top: 0,
    behavior: 'smooth',
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

async function deletePatient(patient) {
  const confirmed = window.confirm(
    `Apakah Anda yakin ingin menghapus pasien "${patient.name}"?`,
  )

  if (!confirmed) {
    return
  }

  errorMessage.value = ''

  try {
    const response = await api.delete(
      `/api/patients/${patient.id}`,
    )

    window.alert(
      response.data.message ??
        'Data pasien berhasil dihapus.',
    )

    await fetchPatients()
  } catch (error) {
    console.error(
      'Gagal menghapus pasien:',
      error,
    )

    if (error.response?.status === 409) {
      errorMessage.value =
        error.response.data.message ??
        'Pasien tidak dapat dihapus karena masih memiliki riwayat pemeriksaan.'

      return
    }

    if (error.response?.status === 403) {
      errorMessage.value =
        'Anda tidak memiliki izin untuk menghapus pasien.'

      return
    }

    if (error.response?.status === 404) {
      errorMessage.value =
        'Data pasien tidak ditemukan.'

      return
    }

    errorMessage.value =
      'Data pasien gagal dihapus. Silakan coba lagi.'
  }
}

function closeForm() {
  resetForm()
  showForm.value = false
}

async function submitPatient() {
  saving.value = true
  errorMessage.value = ''
  successMessage.value = ''
  validationErrors.value = {}

  const payload = {
    nik: form.nik,
    name: form.name,
    gender: form.gender,
    birth_date: form.birth_date,
    phone: form.phone || null,
    address: form.address || null,
  }

  try {
    let response

    if (editingPatientId.value) {
      response = await api.put(
        `/api/patients/${editingPatientId.value}`,
        payload,
      )
    } else {
      response = await api.post(
        '/api/patients',
        payload,
      )
    }

    successMessage.value = response.data.message

    showForm.value = false
    resetForm()

    await fetchPatients()
  } catch (error) {
    if (error.response?.status === 422) {
      validationErrors.value =
        error.response.data.errors ?? {}

      errorMessage.value =
        'Data belum valid. Periksa kembali form.'
    } else if (error.response?.status === 403) {
      errorMessage.value =
        'Anda tidak memiliki izin untuk mengubah data pasien.'
    } else {
      errorMessage.value =
        'Data pasien gagal disimpan. Silakan coba lagi.'
    }
  } finally {
    saving.value = false
  }
}

function formatGender(gender) {
  if (gender === 'L') {
    return 'Laki-laki'
  }

  if (gender === 'P') {
    return 'Perempuan'
  }

  return '-'
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

onMounted(() => {
  fetchPatients()
})
</script>

<template>
  <div>
    <div class="page-header">
      <div>
        <p class="page-label">
          DATA PENDUKUNG
        </p>

        <h1>Data Pasien</h1>

        <p>
          Daftar pasien yang terdaftar pada Praktek THT.
        </p>
      </div>

      <button
        v-if="auth.isAdmin"
        type="button"
        class="primary-button"
        @click="openCreateForm"
      >
        + Tambah Pasien
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
      v-if="showForm && auth.isAdmin"
      class="form-card"
    >
      <div class="form-card-header">
        <div>
          <h2>
            {{
              editingPatientId
                ? 'Edit Pasien'
                : 'Tambah Pasien'
            }}
          </h2>

          <p>
            {{
              editingPatientId
                ? 'Perbarui data pasien berikut.'
                : 'Lengkapi data pasien berikut.'
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

      <form @submit.prevent="submitPatient">
        <div class="form-grid">
          <div class="form-group">
            <label for="nik">
              NIK
            </label>

            <input
              id="nik"
              v-model="form.nik"
              type="text"
              maxlength="16"
              placeholder="16 digit NIK"
            >

            <small
              v-if="validationErrors.nik"
              class="field-error"
            >
              {{ validationErrors.nik[0] }}
            </small>
          </div>

          <div class="form-group">
            <label for="name">
              Nama Pasien
            </label>

            <input
              id="name"
              v-model="form.name"
              type="text"
              placeholder="Nama lengkap pasien"
            >

            <small
              v-if="validationErrors.name"
              class="field-error"
            >
              {{ validationErrors.name[0] }}
            </small>
          </div>

          <div class="form-group">
            <label for="gender">
              Jenis Kelamin
            </label>

            <select
              id="gender"
              v-model="form.gender"
            >
              <option value="">
                Pilih jenis kelamin
              </option>

              <option value="L">
                Laki-laki
              </option>

              <option value="P">
                Perempuan
              </option>
            </select>

            <small
              v-if="validationErrors.gender"
              class="field-error"
            >
              {{ validationErrors.gender[0] }}
            </small>
          </div>

          <div class="form-group">
            <label for="birth_date">
              Tanggal Lahir
            </label>

            <input
              id="birth_date"
              v-model="form.birth_date"
              type="date"
            >

            <small
              v-if="validationErrors.birth_date"
              class="field-error"
            >
              {{ validationErrors.birth_date[0] }}
            </small>
          </div>

          <div class="form-group">
            <label for="phone">
              No. Telepon
            </label>

            <input
              id="phone"
              v-model="form.phone"
              type="text"
              placeholder="Contoh: 081234567890"
            >

            <small
              v-if="validationErrors.phone"
              class="field-error"
            >
              {{ validationErrors.phone[0] }}
            </small>
          </div>

          <div class="form-group form-group-full">
            <label for="address">
              Alamat
            </label>

            <textarea
              id="address"
              v-model="form.address"
              rows="3"
              placeholder="Alamat pasien"
            />

            <small
              v-if="validationErrors.address"
              class="field-error"
            >
              {{ validationErrors.address[0] }}
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
                : editingPatientId
                  ? 'Simpan Perubahan'
                  : 'Simpan Pasien'
            }}
          </button>
        </div>
      </form>
    </section>

    <section class="data-card">

      <!-- PENCARIAN -->
      <div
        v-if="!loading && patients.length > 0"
        class="search-container"
      >
        <div class="search-box">
          <span class="search-icon">
            🔍
          </span>

          <input
            v-model="searchQuery"
            type="search"
            class="search-input"
            placeholder="Cari nama, NIK, telepon, atau alamat..."
          >
        </div>

        <span
          v-if="searchQuery"
          class="search-result-count"
        >
          {{ filteredPatients.length }} data ditemukan
        </span>
      </div>

      <div
        v-if="loading"
        class="state-message"
      >
        Memuat data pasien...
      </div>

      <div
        v-else-if="patients.length === 0"
        class="state-message"
      >
        Belum ada data pasien.
      </div>

      <div
        v-else-if="filteredPatients.length === 0"
        class="state-message"
      >
        Data pasien yang dicari tidak ditemukan.
      </div>

      <div
        v-else
        class="table-wrapper"
      >
        <table class="data-table">
          <thead>
            <tr>
              <th>No.</th>
              <th>NIK</th>
              <th>Nama Pasien</th>
              <th>Jenis Kelamin</th>
              <th>Tanggal Lahir</th>
              <th>No. Telepon</th>
              <th>Alamat</th>
              <th>Aksi</th>
            </tr>
          </thead>

          <tbody>
            <tr
              v-for="(patient, index) in filteredPatients"
              :key="patient.id"
            >
              <td>{{ index + 1 }}</td>

              <td>
                {{ patient.nik }}
              </td>

              <td>
                <strong>
                  {{ patient.name }}
                </strong>
              </td>

              <td>
                {{ formatGender(patient.gender) }}
              </td>

              <td>
                {{ formatDate(patient.birth_date) }}
              </td>

              <td>
                {{ patient.phone || '-' }}
              </td>

              <td>
                {{ patient.address || '-' }}
              </td>

              <td>
                <div class="action-buttons">
                  <button
                    type="button"
                    class="detail-button"
                    @click="openPatientDetail(patient)"
                  >
                    Detail
                  </button>

                  <button
                    v-if="auth.isAdmin"
                    type="button"
                    class="edit-button"
                    @click="openEditForm(patient)"
                  >
                    Edit
                  </button>

                  <button
                    v-if="auth.isAdmin"
                    type="button"
                    class="delete-button"
                    @click="deletePatient(patient)"
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