<script setup>
import { ref, reactive, onMounted }           from 'vue'
import { RouterLink, useRouter, useRoute }    from 'vue-router'
import api                                    from '../../../utils/api'

const router = useRouter()
const route  = useRoute()
const filmId = route.params.id   // Ambil :id dari URL

const loadingData   = ref(true)
const loadingSubmit = ref(false)
const successMsg    = ref('')
const errorMsg      = ref('')
const genres        = ref([])
const aktors        = ref([])

const form = reactive({
  judul: '', id_genre: '', rating: '', sutradara: '',
  tanggal_rilis: '', durasi: '', poster: '',
  deskripsi: '', id_aktor: [],
})

onMounted(async () => {
  await ambilDataFilm()
})

const ambilDataFilm = async () => {
  try {
    loadingData.value = true

    // Panggil 3 API sekaligus secara paralel
    const [filmRes, genreRes, aktorRes] = await Promise.all([
      api.get(`/film/${filmId}`),
      api.get('/genre'),
      api.get('/aktor')
    ])

    genres.value = genreRes.data.data
    aktors.value = aktorRes.data.data

    const filmData   = filmRes.data.data

    // Pre-fill form dengan data film yang sudah ada
    form.judul         = filmData.judul
    form.id_genre      = filmData.id_genre
    form.rating        = filmData.rating
    form.sutradara     = filmData.sutradara
    form.tanggal_rilis = filmData.tanggal_rilis
    form.durasi        = filmData.durasi
    form.poster        = filmData.poster
    form.deskripsi     = filmData.deskripsi

    // Ekstrak ID aktor: [{id:1, nama:"A"},...] → [1, 2, ...]
    // Convert ke Number biar gak bentrok antara string vs number
    form.id_aktor = filmData.aktors ? filmData.aktors.map(a => Number(a.id)) : []

    console.log(filmData)
  } catch (err) {
    errorMsg.value = 'Gagal memuat data film untuk diedit.'
  } finally {
    loadingData.value = false
  }
}

const handleUpdate = async () => {
  if (form.id_aktor.length === 0) {
    errorMsg.value = 'Pilih minimal 1 aktor!'
    return
  }
  try {
    loadingSubmit.value = true
    errorMsg.value      = ''

    // PUT request untuk update data
    await api.put(`/film/${filmId}`, form)

    successMsg.value = 'Data film berhasil diupdate!'
    setTimeout(() => { router.push('/kelola-film') }, 2000)
    window.scrollTo({ top: 0, behavior: 'smooth' })

  } catch (err) {
    errorMsg.value = 'Gagal mengupdate film!'
  } finally {
    loadingSubmit.value = false
  }
}
</script>

<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/kelola-film" class="btn-back">← Batal Edit</RouterLink>
      <h1>✏️ Edit Film</h1>
    </div>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <div v-if="errorMsg"   class="alert alert-error">❌ {{ errorMsg }}</div>

    <div v-if="loadingData" class="loading-text">⏳ Memuat data film...</div>

    <form v-else @submit.prevent="handleUpdate" class="form-card">
      <!-- Field sama persis dengan TambahFilm.vue -->
      <div class="form-group">
        <label>🎬 Judul Film <span class="required">*</span></label>
        <input v-model="form.judul" type="text" required />
      </div>

      <div class="form-group">
        <label>🎭 Genre <span class="required">*</span></label>
        <select v-model="form.id_genre" required>
          <option value="">-- Pilih Genre --</option>
          <option v-for="genre in genres" :key="genre.id" :value="genre.id">
            {{ genre.nama_genre }}
          </option>
        </select>
      </div>

      <div class="form-group">
        <label>🌟 Rating <span class="required">*</span></label>
        <input v-model="form.rating" type="number" step="0.1" required />
      </div>

      <div class="form-group">
        <label>🎥 Sutradara <span class="required">*</span></label>
        <input v-model="form.sutradara" type="text" required />
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>📅 Tanggal Rilis <span class="required">*</span></label>
          <input v-model="form.tanggal_rilis" type="date" required />
        </div>
        <div class="form-group">
          <label>⏱️ Durasi (menit) <span class="required">*</span></label>
          <input v-model="form.durasi" type="number" min="1" required />
        </div>
      </div>

      <div class="form-group">
        <label>🖼️ URL Poster <span class="required">*</span></label>
        <input v-model="form.poster" type="text" required />
        <img v-if="form.poster" :src="form.poster" alt="Preview" class="poster-preview" />
      </div>

      <div class="form-group">
        <label>🎭 Pilih Aktor <span class="required">*</span></label>
        <div class="checkbox-grid">
          <label v-for="aktor in aktors" :key="aktor.id" class="checkbox-item">
            <input type="checkbox" :value="aktor.id" v-model="form.id_aktor" />
            <span>{{ aktor.nama_aktor }}</span>
          </label>
        </div>
      </div>

      <div class="form-group">
        <label>📖 Sinopsis <span class="required">*</span></label>
        <textarea v-model="form.deskripsi" rows="5" required></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" :disabled="loadingSubmit" class="btn btn-primary">
          <span v-if="loadingSubmit">⏳ Mengupdate...</span>
          <span v-else>💾 Update Film</span>
        </button>
      </div>
    </form>
  </div>
</template>

<style scoped>
.page-title { margin-bottom: 24px; }
.page-title h1 { font-size: 28px; color: #1a1a2e; margin-top: 12px; }
.btn-back { color: #666; font-size: 14px; }
.btn-back:hover { color: #e94560; text-decoration: none; }

.form-card { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 20px; max-width: 720px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
label { font-size: 13px; font-weight: 600; color: #333; }
.required { color: #e94560; margin-left: 2px; }
input, select, textarea { padding: 11px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; }
input:focus, select:focus, textarea:focus { border-color: #e94560; box-shadow: 0 0 0 3px rgba(233,69,96,0.1); }
.poster-preview { margin-top: 10px; width: 120px; height: 160px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; }
.checkbox-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
.checkbox-item { display: flex; align-items: center; gap: 6px; background: #f4f4f8; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: normal; transition: background 0.2s; }
.checkbox-item:has(input:checked) { background: #fee2e2; color: #e94560; font-weight: 600; }
.hint { font-size: 12px; color: #999; margin-top: 4px; }
.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid #f0f0f0; }
.btn-secondary { background: #f0f0f0; color: #555; padding: 10px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; }
</style>