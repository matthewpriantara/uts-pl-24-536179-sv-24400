<template>
  <div class="min-h-screen bg-[#FBFBF9] flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans antialiased text-stone-900">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <!-- Brand Minimalist Header -->
      <div class="text-center">
        <span class="text-xs uppercase tracking-[0.25em] text-stone-500 font-semibold">Warehouse Asset Management</span>
        <h1 class="mt-2 text-3xl font-light tracking-tight text-stone-900">
          GUDANG<span class="font-semibold">PRO</span>
        </h1>
        <p class="mt-2 text-sm text-stone-600">
          Masuk untuk mengelola inventaris aset dan barang gudang.
        </p>
      </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4 sm:px-0">
      <div class="bg-white py-8 px-6 sm:px-10 border border-stone-200 rounded-lg shadow-sm">
        
        <!-- Error Alert -->
        <div v-if="errorMessage" class="mb-6 p-4 rounded-md bg-stone-100 border-l-4 border-stone-800 text-stone-800 text-sm flex items-start gap-3">
          <svg class="w-5 h-5 text-stone-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
          </svg>
          <div>
            <p class="font-medium">Otentikasi Gagal</p>
            <p class="text-xs text-stone-600 mt-0.5">{{ errorMessage }}</p>
          </div>
        </div>

        <form class="space-y-5" @submit.prevent="handleLogin">
          <div>
            <label for="email" class="block text-xs font-medium uppercase tracking-wider text-stone-700">
              Alamat Email
            </label>
            <div class="mt-1.5">
              <input
                id="email"
                v-model="form.email"
                type="email"
                required
                autocomplete="email"
                placeholder="nama@perusahaan.com"
                class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-md text-stone-900 text-sm focus:outline-none focus:ring-1 focus:ring-stone-900 focus:border-stone-900 transition-colors"
              />
            </div>
          </div>

          <div>
            <label for="password" class="block text-xs font-medium uppercase tracking-wider text-stone-700">
              Kata Sandi
            </label>
            <div class="mt-1.5">
              <input
                id="password"
                v-model="form.password"
                type="password"
                required
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full px-3.5 py-2.5 bg-stone-50 border border-stone-300 rounded-md text-stone-900 text-sm focus:outline-none focus:ring-1 focus:ring-stone-900 focus:border-stone-900 transition-colors"
              />
            </div>
          </div>

          <div>
            <button
              type="submit"
              :disabled="loading"
              class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md text-sm font-medium text-white bg-stone-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-900 transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              <span>{{ loading ? 'Memverifikasi...' : 'Masuk ke Sistem' }}</span>
            </button>
          </div>
        </form>

        <!-- Quick Fill for Graders / Testing -->
        <div class="mt-6 pt-6 border-t border-stone-200">
          <div class="bg-stone-50 p-3.5 rounded-md border border-stone-200">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-xs font-semibold text-stone-700">Akun Pengujian Evaluasi</p>
                <p class="text-[11px] text-stone-500 font-mono mt-0.5">admin@kepl.test • password123</p>
              </div>
              <button
                type="button"
                @click="fillAdminCredentials"
                class="text-xs font-medium text-stone-800 hover:text-black underline cursor-pointer"
              >
                Isi Otomatis
              </button>
            </div>
          </div>
        </div>

      </div>

      <!-- Footer Info -->
      <div class="mt-6 text-center text-xs text-stone-500">
        UTS Kualitas & Evolusi Perangkat Lunak 2026 • Matthew H. Priantara
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../api'

const router = useRouter()

const form = ref({
  email: '',
  password: '',
})

const loading = ref(false)
const errorMessage = ref('')

const fillAdminCredentials = () => {
  form.value.email = 'admin@kepl.test'
  form.value.password = 'password123'
}

const handleLogin = async () => {
  loading.value = true
  errorMessage.value = ''

  try {
    const response = await apiClient.post('/login', {
      email: form.value.email,
      password: form.value.password,
    })

    // Store token and user information
    localStorage.setItem('token', response.data.access_token)
    localStorage.setItem('user', JSON.stringify(response.data.user))

    // Redirect to inventory dashboard
    router.push({ name: 'Inventory' })
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message
    } else {
      errorMessage.value = 'Gagal terhubung ke server API. Pastikan backend aktif.'
    }
  } finally {
    loading.value = false
  }
}
</script>
