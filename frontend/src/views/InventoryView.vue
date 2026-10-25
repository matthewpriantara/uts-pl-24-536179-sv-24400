<template>
  <div class="min-h-screen bg-[#FBFBF9] font-sans antialiased text-stone-900 flex flex-col">
    <!-- Topbar Navigation -->
    <header class="bg-white border-b border-stone-200 sticky top-0 z-30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 bg-stone-900 text-white rounded flex items-center justify-center font-bold text-sm tracking-wider">
            GP
          </div>
          <div>
            <h1 class="text-base font-semibold tracking-tight text-stone-900">GUDANG<span class="font-light text-stone-600">PRO</span></h1>
            <p class="text-[10px] uppercase tracking-widest text-stone-500 font-medium">Enterprise Warehouse & Inventory</p>
          </div>
        </div>

        <div class="flex items-center gap-4">
          <div class="hidden sm:flex flex-col text-right">
            <span class="text-xs font-semibold text-stone-800">{{ currentUser?.name || 'Administrator' }}</span>
            <span class="text-[11px] text-stone-500 font-mono">{{ currentUser?.email || 'admin@kepl.test' }}</span>
          </div>
          <button
            @click="handleLogout"
            class="px-3 py-1.5 text-xs font-medium text-stone-700 bg-white border border-stone-300 rounded hover:bg-stone-100 hover:text-black transition-colors cursor-pointer flex items-center gap-1.5"
            title="Keluar dari sistem"
          >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <span>Keluar</span>
          </button>
        </div>
      </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-1 w-full">
      
      <!-- Notification Banner -->
      <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
      >
        <div v-if="notification.show" class="mb-6 p-4 rounded-md border text-sm flex items-center justify-between" :class="notification.type === 'success' ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-red-50 border-red-200 text-red-900'">
          <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            <span>{{ notification.message }}</span>
          </div>
          <button @click="notification.show = false" class="text-xs hover:underline cursor-pointer">Tutup</button>
        </div>
      </transition>

      <!-- Key Metrics Row -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-lg border border-stone-200 shadow-xs">
          <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Total Jenis Barang</p>
          <p class="text-2xl font-semibold text-stone-900 mt-1">{{ items.length }} <span class="text-xs font-normal text-stone-500">item</span></p>
        </div>
        <div class="bg-white p-5 rounded-lg border border-stone-200 shadow-xs">
          <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Total Unit Fisik</p>
          <p class="text-2xl font-semibold text-stone-900 mt-1">{{ totalUnits }} <span class="text-xs font-normal text-stone-500">unit</span></p>
        </div>
        <div class="bg-white p-5 rounded-lg border border-stone-200 shadow-xs">
          <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Stok Kritis (≤ 15 Unit)</p>
          <p class="text-2xl font-semibold mt-1" :class="lowStockCount > 0 ? 'text-amber-700' : 'text-stone-900'">
            {{ lowStockCount }} <span class="text-xs font-normal text-stone-500">perlu restock</span>
          </p>
        </div>
        <div class="bg-white p-5 rounded-lg border border-stone-200 shadow-xs">
          <p class="text-[11px] font-semibold uppercase tracking-wider text-stone-500">Valuasi Total Aset</p>
          <p class="text-2xl font-semibold text-stone-900 mt-1">{{ formatRupiah(totalValuation) }}</p>
        </div>
      </div>

      <!-- Action & Filter Bar -->
      <div class="bg-white p-4 rounded-lg border border-stone-200 shadow-xs mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        
        <!-- Search and Category Filters -->
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1">
          <div class="relative max-w-xs w-full">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-stone-400">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </span>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Cari nama atau SKU barang..."
              class="w-full pl-9 pr-3.5 py-2 bg-stone-50 border border-stone-300 rounded text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900 focus:border-stone-900"
            />
          </div>

          <!-- Category Pill Tabs -->
          <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
            <button
              v-for="cat in categories"
              :key="cat"
              @click="selectedCategory = cat"
              class="px-3 py-1.5 text-xs rounded transition-colors whitespace-nowrap cursor-pointer"
              :class="selectedCategory === cat ? 'bg-stone-900 text-white font-medium' : 'bg-stone-100 text-stone-600 hover:bg-stone-200 hover:text-stone-900'"
            >
              {{ cat }}
            </button>
          </div>
        </div>

        <!-- Add Button -->
        <button
          @click="openAddModal"
          class="px-4 py-2 bg-stone-900 hover:bg-black text-white text-xs font-semibold rounded flex items-center justify-center gap-1.5 transition-colors cursor-pointer shrink-0"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          <span>Tambah Barang Baru</span>
        </button>
      </div>

      <!-- Data Table -->
      <div class="bg-white border border-stone-200 rounded-lg shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-stone-200 text-left text-xs">
            <thead class="bg-stone-50 text-stone-600 font-semibold uppercase tracking-wider">
              <tr>
                <th scope="col" class="py-3.5 px-4">SKU / Kode</th>
                <th scope="col" class="py-3.5 px-4">Nama Barang & Spesifikasi</th>
                <th scope="col" class="py-3.5 px-4">Kategori</th>
                <th scope="col" class="py-3.5 px-4 text-center">Stok</th>
                <th scope="col" class="py-3.5 px-4 text-right">Harga Satuan</th>
                <th scope="col" class="py-3.5 px-4 text-right">Total Nilai</th>
                <th scope="col" class="py-3.5 px-4 text-center">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
              
              <!-- Loading State -->
              <tr v-if="loading">
                <td colspan="7" class="py-12 text-center text-stone-500">
                  <div class="inline-flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-stone-700" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memuat data inventaris dari server Laravel...</span>
                  </div>
                </td>
              </tr>

              <!-- Empty State -->
              <tr v-else-if="filteredItems.length === 0">
                <td colspan="7" class="py-12 text-center text-stone-500">
                  Tidak ada barang yang sesuai dengan kriteria pencarian.
                </td>
              </tr>

              <!-- Data Rows -->
              <tr
                v-else
                v-for="item in filteredItems"
                :key="item.id"
                class="hover:bg-stone-50/70 transition-colors"
              >
                <!-- SKU -->
                <td class="py-3.5 px-4 font-mono font-medium text-stone-800">
                  <span class="bg-stone-100 text-stone-700 px-2 py-0.5 rounded text-[11px] border border-stone-200">
                    {{ item.sku }}
                  </span>
                </td>

                <!-- Name & Description -->
                <td class="py-3.5 px-4 max-w-xs">
                  <div class="font-semibold text-stone-900">{{ item.name }}</div>
                  <div class="text-[11px] text-stone-500 truncate mt-0.5" :title="item.description">
                    {{ item.description || 'Tidak ada deskripsi' }}
                  </div>
                </td>

                <!-- Category -->
                <td class="py-3.5 px-4">
                  <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-stone-100 text-stone-800 border border-stone-200">
                    {{ item.category }}
                  </span>
                </td>

                <!-- Stock Indicator -->
                <td class="py-3.5 px-4 text-center">
                  <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold font-mono"
                    :class="item.stock <= 5 ? 'bg-red-100 text-red-800' : (item.stock <= 15 ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800')"
                  >
                    {{ item.stock }} unit
                  </span>
                </td>

                <!-- Price -->
                <td class="py-3.5 px-4 text-right font-mono font-medium text-stone-900">
                  {{ formatRupiah(item.price) }}
                </td>

                <!-- Subtotal Valuation -->
                <td class="py-3.5 px-4 text-right font-mono text-stone-600">
                  {{ formatRupiah(item.stock * item.price) }}
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-center">
                  <div class="flex items-center justify-center gap-2">
                    <button
                      @click="openEditModal(item)"
                      class="px-2.5 py-1 text-xs text-stone-700 border border-stone-300 rounded hover:bg-stone-100 hover:text-black transition-colors cursor-pointer"
                    >
                      Ubah
                    </button>
                    <button
                      @click="confirmDelete(item)"
                      class="px-2.5 py-1 text-xs text-red-700 border border-red-200 rounded hover:bg-red-50 transition-colors cursor-pointer"
                    >
                      Hapus
                    </button>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>

    </main>

    <!-- Modal Form (Tambah & Ubah Barang) -->
    <div v-if="modal.show" class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/50 backdrop-blur-xs flex items-center justify-center p-4">
      <div class="bg-white rounded-lg border border-stone-200 shadow-xl max-w-lg w-full overflow-hidden">
        
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-stone-200 flex items-center justify-between bg-stone-50">
          <div>
            <h3 class="text-sm font-semibold uppercase tracking-wider text-stone-900">
              {{ modal.isEdit ? 'Perbarui Data Barang' : 'Tambah Barang Baru' }}
            </h3>
            <p class="text-[11px] text-stone-500 mt-0.5">Isi seluruh informasi inventaris dengan benar.</p>
          </div>
          <button @click="modal.show = false" class="text-stone-400 hover:text-stone-700 cursor-pointer">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Form Body -->
        <form @submit.prevent="submitModalForm" class="p-6 space-y-4">
          
          <!-- Validation Errors Alert (422) -->
          <div v-if="modalErrors.general" class="p-3 bg-red-50 border border-red-200 rounded text-red-800 text-xs">
            {{ modalErrors.general }}
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-700 mb-1">
              Nama Barang <span class="text-red-500">*</span>
            </label>
            <input
              v-model="modalForm.name"
              type="text"
              required
              placeholder="Contoh: Laptop Dell XPS 15"
              class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"
            />
            <p v-if="modalErrors.name" class="mt-1 text-[11px] text-red-600">{{ modalErrors.name[0] }}</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-stone-700 mb-1">
                SKU / Kode Barang <span class="text-red-500">*</span>
              </label>
              <input
                v-model="modalForm.sku"
                type="text"
                required
                placeholder="Contoh: BRG-009"
                class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded text-xs font-mono text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"
              />
              <p v-if="modalErrors.sku" class="mt-1 text-[11px] text-red-600">{{ modalErrors.sku[0] }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-stone-700 mb-1">
                Kategori <span class="text-red-500">*</span>
              </label>
              <select
                v-model="modalForm.category"
                required
                class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"
              >
                <option value="Elektronik">Elektronik</option>
                <option value="Furniture">Furniture</option>
                <option value="Perkakas">Perkakas</option>
                <option value="ATK">ATK</option>
                <option value="Lainnya">Lainnya</option>
              </select>
              <p v-if="modalErrors.category" class="mt-1 text-[11px] text-red-600">{{ modalErrors.category[0] }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-stone-700 mb-1">
                Jumlah Stok <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="modalForm.stock"
                type="number"
                min="0"
                required
                class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded text-xs font-mono text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"
              />
              <p v-if="modalErrors.stock" class="mt-1 text-[11px] text-red-600">{{ modalErrors.stock[0] }}</p>
            </div>

            <div>
              <label class="block text-xs font-semibold uppercase tracking-wider text-stone-700 mb-1">
                Harga Satuan (IDR) <span class="text-red-500">*</span>
              </label>
              <input
                v-model.number="modalForm.price"
                type="number"
                min="0"
                step="0.01"
                required
                class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded text-xs font-mono text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"
              />
              <p v-if="modalErrors.price" class="mt-1 text-[11px] text-red-600">{{ modalErrors.price[0] }}</p>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold uppercase tracking-wider text-stone-700 mb-1">
              Deskripsi & Spesifikasi
            </label>
            <textarea
              v-model="modalForm.description"
              rows="3"
              placeholder="Keterangan spesifikasi teknis atau catatan inventaris..."
              class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded text-xs text-stone-900 focus:outline-none focus:ring-1 focus:ring-stone-900"
            ></textarea>
          </div>

          <!-- Modal Action Buttons -->
          <div class="pt-4 border-t border-stone-200 flex items-center justify-end gap-2">
            <button
              type="button"
              @click="modal.show = false"
              class="px-3 py-1.5 text-xs text-stone-700 border border-stone-300 rounded hover:bg-stone-100 cursor-pointer"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="modalSubmitting"
              class="px-4 py-1.5 text-xs font-semibold text-white bg-stone-900 hover:bg-black rounded transition-colors disabled:opacity-50 cursor-pointer flex items-center gap-1.5"
            >
              <svg v-if="modalSubmitting" class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
              </svg>
              <span>{{ modalSubmitting ? 'Menyimpan...' : (modal.isEdit ? 'Simpan Perubahan' : 'Tambahkan Barang') }}</span>
            </button>
          </div>

        </form>

      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import apiClient from '../api'

const router = useRouter()

const items = ref([])
const loading = ref(false)
const currentUser = ref(null)

const searchQuery = ref('')
const selectedCategory = ref('Semua')
const categories = ['Semua', 'Elektronik', 'Furniture', 'Perkakas', 'ATK']

const notification = ref({
  show: false,
  type: 'success',
  message: '',
})

const showToast = (message, type = 'success') => {
  notification.value = { show: true, type, message }
  setTimeout(() => {
    notification.value.show = false
  }, 4000)
}

// Format Rupiah Helper
const formatRupiah = (value) => {
  const number = Number(value) || 0
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(number)
}

// Load Current User & Items
const fetchItems = async () => {
  loading.value = true
  try {
    const response = await apiClient.get('/items')
    items.value = response.data.data || []
  } catch (error) {
    showToast('Gagal mengambil data inventaris dari server.', 'error')
  } finally {
    loading.value = false
  }
}

// Calculated Metrics
const totalUnits = computed(() => {
  return items.value.reduce((acc, curr) => acc + (Number(curr.stock) || 0), 0)
})

const lowStockCount = computed(() => {
  return items.value.filter((i) => i.stock <= 15).length
})

const totalValuation = computed(() => {
  return items.value.reduce((acc, curr) => acc + (Number(curr.stock) * Number(curr.price) || 0), 0)
})

const filteredItems = computed(() => {
  return items.value.filter((item) => {
    const matchesSearch =
      item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      item.sku.toLowerCase().includes(searchQuery.value.toLowerCase())
    const matchesCat =
      selectedCategory.value === 'Semua' || item.category === selectedCategory.value

    return matchesSearch && matchesCat
  })
})

// Modal State
const modal = ref({
  show: false,
  isEdit: false,
  itemId: null,
})

const modalForm = ref({
  name: '',
  sku: '',
  category: 'Elektronik',
  stock: 10,
  price: 0,
  description: '',
})

const modalErrors = ref({})
const modalSubmitting = ref(false)

const openAddModal = () => {
  modal.value = { show: true, isEdit: false, itemId: null }
  modalForm.value = {
    name: '',
    sku: '',
    category: 'Elektronik',
    stock: 10,
    price: 0,
    description: '',
  }
  modalErrors.value = {}
}

const openEditModal = (item) => {
  modal.value = { show: true, isEdit: true, itemId: item.id }
  modalForm.value = {
    name: item.name,
    sku: item.sku,
    category: item.category,
    stock: item.stock,
    price: item.price,
    description: item.description || '',
  }
  modalErrors.value = {}
}

const submitModalForm = async () => {
  modalSubmitting.value = true
  modalErrors.value = {}

  try {
    if (modal.value.isEdit) {
      // PUT /api/items/{id}
      await apiClient.put(`/items/${modal.value.itemId}`, modalForm.value)
      showToast('Data barang berhasil diperbarui.')
    } else {
      // POST /api/items (Status 201)
      await apiClient.post('/items', modalForm.value)
      showToast('Barang baru berhasil ditambahkan ke inventaris.')
    }
    modal.value.show = false
    await fetchItems()
  } catch (error) {
    if (error.response && error.response.status === 422 && error.response.data.errors) {
      modalErrors.value = error.response.data.errors
    } else {
      modalErrors.value = { general: error.response?.data?.message || 'Gagal menyimpan barang.' }
    }
  } finally {
    modalSubmitting.value = false
  }
}

const confirmDelete = async (item) => {
  if (confirm(`Yakin ingin menghapus barang "${item.name}" (SKU: ${item.sku}) dari sistem?`)) {
    try {
      await apiClient.delete(`/items/${item.id}`)
      showToast('Barang berhasil dihapus dari sistem.')
      await fetchItems()
    } catch (error) {
      showToast('Gagal menghapus barang.', 'error')
    }
  }
}

const handleLogout = async () => {
  try {
    await apiClient.post('/logout')
  } catch (error) {
    // Continue even if network error
  } finally {
    localStorage.removeItem('token')
    localStorage.removeItem('user')
    router.push({ name: 'Login' })
  }
}

onMounted(() => {
  const storedUser = localStorage.getItem('user')
  if (storedUser) {
    try {
      currentUser.value = JSON.parse(storedUser)
    } catch (e) {
      // ignore
    }
  }
  fetchItems()
})
</script>
