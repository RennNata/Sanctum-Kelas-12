<template>
  <div class="container">
    <RouterLink to="/kelola-genre" class="btn-back">← Kembali</RouterLink>
    <h1>✏️ Edit Genre</h1>

    <p v-if="loadingData" class="loading-text">⏳ Memuat data...</p>

    <div v-else class="form-wrapper">
      <form @submit.prevent="submitGenre">
        <div class="form-group">
          <label>Nama Genre <span class="required">*</span></label>
          <input v-model="form.nama_genre" type="text" required class="form-input" />
        </div>
        <button type="submit" :disabled="loading" class="btn-submit">
          <span v-if="loading">⏳ Menyimpan...</span>
          <span v-else>💾 Simpan Perubahan</span>
        </button>
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted }            from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import api                           from '../../../utils/api'

const router  = useRouter()
const route   = useRoute()
const genreId = route.params.id

const form        = ref({ nama_genre: '' })
const loadingData = ref(true)
const loading     = ref(false)
const errorMsg    = ref('')

onMounted(async () => {
  try {
    // Karena tidak ada GET /genre/:id, ambil semua lalu filter
    const res     = await api.get('/genre')
    const current = res.data.data.find(g => g.id == genreId)
    if (current) { form.value.nama_genre = current.nama_genre }
    else { router.push('/kelola-genre') }
  } catch (err) { alert('Gagal memuat data') }
  finally { loadingData.value = false }
})

const submitGenre = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.put(`/genre/${genreId}`, form.value)
    router.push('/kelola-genre')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>