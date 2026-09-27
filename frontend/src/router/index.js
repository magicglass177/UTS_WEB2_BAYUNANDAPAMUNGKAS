import { createRouter, createWebHistory } from 'vue-router'

import LoginView from '../views/LoginView.vue'
import DashboardView from '../views/DashboardView.vue'
import PatientsView from '../views/PatientsView.vue'
import PatientDetailView from '../views/PatientDetailView.vue'
import MedicalRecordsView from '../views/MedicalRecordsView.vue'
import MedicalRecordDetailView from '../views/MedicalRecordDetailView.vue'

import AppLayout from '../layouts/AppLayout.vue'
import { useAuthStore } from '../stores/auth'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: {
      guestOnly: true,
    },
  },

  {
    path: '/',
    component: AppLayout,
    meta: {
      requiresAuth: true,
    },

    children: [
      {
        path: '',
        name: 'dashboard',
        component: DashboardView,
      },

      {
        path: 'patients',
        name: 'patients',
        component: PatientsView,
      },

      {
        path: 'patients/:id',
        name: 'patient-detail',
        component: PatientDetailView,
      },

      {
        path: 'medical-records',
        name: 'medical-records',
        component: MedicalRecordsView,
      },

      {
        path: 'medical-records/:id',
        name: 'medical-record-detail',
        component: MedicalRecordDetailView,
      },
    ],
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach(async (to) => {
  const auth = useAuthStore()

  if (!auth.initialized) {
    await auth.fetchUser()
  }

  const requiresAuth = to.matched.some(
    (record) => record.meta.requiresAuth,
  )

  const guestOnly = to.matched.some(
    (record) => record.meta.guestOnly,
  )

  if (requiresAuth && !auth.isAuthenticated) {
    return {
      name: 'login',
    }
  }

  if (guestOnly && auth.isAuthenticated) {
    return {
      name: 'dashboard',
    }
  }

  return true
})

export default router