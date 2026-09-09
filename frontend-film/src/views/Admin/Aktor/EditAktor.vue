<script setup>
import { ref, onMounted }            from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import api                           from '../../../utils/api'

const router  = useRouter()
const route   = useRoute()
const aktorId = route.params.id

const form        = ref({ nama_aktor: '', gender: '', tanggal_lahir: '' })
const loadingData = ref(true)
const loading     = ref(false)
const errorMsg    = ref('')

onMounted(async () => {
  try {
    const res     = await api.get('/aktor')
    const current = res.data.data.find(a => a.id == aktorId)
    if (current) {
      form.value.nama_aktor    = current.nama_aktor
      form.value.gender        = current.gender
      form.value.umur          = current.umur
      form.value.tanggal_lahir = current.tanggal_lahir
      form.value.foto          = current.foto
    } else { router.push('/kelola-aktor') }
  } catch (err) { alert('Gagal memuat data') }
  finally { loadingData.value = false }
})

const submitAktor = async () => {
  try {
    loading.value  = true
    errorMsg.value = ''
    await api.put(`/aktor/${aktorId}`, form.value)
    router.push('/kelola-aktor')
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Terjadi kesalahan.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="container">
    <RouterLink to="/kelola-aktor" class="btn-back">← Kembali</RouterLink>
    <h1 style="margin: 12px 0 24px">✏️ Edit Aktor</h1>

    <p v-if="loadingData" class="loading-text">⏳ Memuat data...</p>

    <div v-else class="form-wrapper">
      <form @submit.prevent="submitAktor" class="form-card">
        <div class="form-group">
          <label>Nama Aktor <span class="required">*</span></label>
          <input v-model="form.nama_aktor" type="text" required class="form-input" />
        </div>

        <div class="form-group">
          <label>Gender <span class="required">*</span></label>
          <select v-model="form.gender" required class="form-input">
            <option value="" disabled>Pilih Gender</option>
            <option value="Laki-laki">Laki-laki</option>
            <option value="Perempuan">Perempuan</option>
          </select>
        </div>

        <div class="form-group">
          <label>Tanggal Lahir <span class="required">*</span></label>
          <input v-model="form.tanggal_lahir" type="date" required class="form-input" />
        </div>

        <div class="form-group">
          <label>🖼️ Foto Aktor <span class="required">*</span></label>
          <input v-model="form.foto" type="text" placeholder="https://..." required />
          <!-- Preview gambar otomatis saat URL diisi -->
          <img v-if="form.foto" :src="form.foto" alt="Foto Aktor" class="foto-preview" />
        </div>

        <div class="form-actions">
          <RouterLink to="/kelola-aktor" class="btn-secondary">Batal</RouterLink>
          <button type="submit" :disabled="loading" class="btn btn-primary">
            <span v-if="loading">⏳ Menyimpan...</span>
            <span v-else>💾 Update Aktor</span>
          </button>
        </div>
        <p v-if="errorMsg" class="error-msg">{{ errorMsg }}</p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.btn-back { color: #666; font-size: 14px; }
.btn-back:hover { color: #e94560; text-decoration: none; }

.form-card { background: white; border-radius: 16px; padding: 32px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); display: flex; flex-direction: column; gap: 20px; max-width: 720px; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
label { font-size: 13px; font-weight: 600; color: #333; }
.required { color: #e94560; margin-left: 2px; }
input, select { padding: 11px 14px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 14px; font-family: inherit; outline: none; transition: border-color 0.2s; }
input:focus, select:focus, textarea:focus { border-color: #e94560; box-shadow: 0 0 0 3px rgba(233,69,96,0.1); }
.foto-preview { margin-top: 10px; width: 120px; height: 160px; object-fit: cover; border-radius: 8px; border: 2px solid #e0e0e0; }
.checkbox-grid { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 4px; }
.checkbox-item { display: flex; align-items: center; gap: 6px; background: #f4f4f8; padding: 6px 14px; border-radius: 20px; cursor: pointer; font-size: 13px; font-weight: normal; transition: background 0.2s; }
.checkbox-item:has(input:checked) { background: #fee2e2; color: #e94560; font-weight: 600; }
.hint { font-size: 12px; color: #999; margin-top: 4px; }
.form-actions { display: flex; gap: 12px; justify-content: flex-end; padding-top: 8px; border-top: 1px solid #f0f0f0; }
.btn-secondary { background: #f0f0f0; color: #555; padding: 10px 24px; border-radius: 10px; text-decoration: none; font-size: 14px; font-weight: 600; }
</style>