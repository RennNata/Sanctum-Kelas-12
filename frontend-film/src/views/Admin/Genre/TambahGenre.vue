<template>
  <div class="container">
    <RouterLink to="/kelola-genre" class="btn-back">← Kembali</RouterLink>
    <h1>➕ Tambah Genre Baru</h1>

    <div class="form-wrapper">
      <form @submit.prevent="submitGenre">
        <div class="form-group">
          <label>Nama Genre <span class="required">*</span></label>
          <input v-model="form.nama_genre" type="text" placeholder="Contoh: Action" required class="form-input" />
        </div>
        <button type="submit" :disabled="loading" class="btn-submit">
          <span v-if="loading">⏳ Menyimpan...</span>
          <span v-else>💾 Simpan Genre</span>
        </button>
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref }               from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import api                   from '../../../utils/api'

const router   = useRouter()
const form     = ref({ nama_genre: '' })
const loading  = ref(false)
const errorMsg = ref('')

const submitGenre = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.post('/genre', form.value)
    router.push('/kelola-genre')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>