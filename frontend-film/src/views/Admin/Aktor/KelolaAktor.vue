<script setup>
import { ref, onMounted }        from 'vue'
import { RouterLink, useRouter } from 'vue-router'
import api                       from '../../../utils/api'

const router       = useRouter()
const aktors       = ref([])
const loading      = ref(true)
const successMsg   = ref('')
const deletingId   = ref(null)
const showModal    = ref(false)
const aktorToDelete = ref(null)

onMounted(async () => { await ambilAktor() })

const ambilAktor = async () => {
  try {
    loading.value = true
    const res = await api.get('/aktor')
    aktors.value = res.data.data
  } catch (err) { console.error(err) }
  finally { loading.value = false }
}

const hapusAktor = (id, nama_aktor) => {
  aktorToDelete.value = { id, nama_aktor }
  showModal.value     = true
}

const konfirmasiHapus = async () => {
  const id = aktorToDelete.value.id
  showModal.value = false
  try {
    deletingId.value = id
    await api.delete(`/aktor/${id}`)
    aktors.value = aktors.value.filter(a => a.id !== id)
    successMsg.value = `Aktor "${aktorToDelete.value.nama_aktor}" berhasil dihapus!`
    setTimeout(() => { successMsg.value = '' }, 3000)
  } catch (err) { alert('Gagal menghapus aktor!') }
  finally { deletingId.value = null; aktorToDelete.value = null }
}
</script>

<template>
  <div class="container">
    <div class="page-title">
      <RouterLink to="/dashboard" class="btn-back">← Dashboard</RouterLink>
      <div class="title-row">
        <h1>🌟 Kelola Aktor</h1>
        <RouterLink to="/tambah-aktor" class="btn btn-primary">➕ Tambah Aktor</RouterLink>
      </div>
    </div>

    <div v-if="successMsg" class="alert alert-success">✅ {{ successMsg }}</div>
    <p v-if="loading" class="loading-text">⏳ Memuat data aktor...</p>

    <div v-else class="table-wrapper">
      <table class="aktor-table">
        <thead>
          <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Nama Aktor</th>
            <th>Gender</th>
            <!-- <<th>Tgl Lahir</th> -->
            <th>Umur</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="aktors.length === 0">
            <td colspan="5" class="empty-row">Belum ada data aktor.</td>
          </tr>
          <tr v-for="(aktor, index) in aktors" :key="aktor.id">
            <td>{{ index + 1 }}</td>
            <td><img :src="aktor.foto" :alt="aktor.nama_aktor" class="table-foto"></td>
            <td class="aktor-name-cell"><span class="badge badge-regular">{{ aktor.nama_aktor }}</span></td>
            <!-- Tampilkan teks lengkap, bukan kode L/P -->
            <td><span class="badge" :class="aktor.gender === 'Laki-laki' ? 'badge-l' : 'badge-p'">{{ aktor.gender === 'Laki-laki' ? 'Laki-laki' : 'Perempuan' }}</span></td>
            <!-- <td>{{ aktor.tanggal_lahir }}</td> -->
            <td><span class="badge badge-regular">{{ aktor.umur }} tahun</span></td>
            <td>
              <div class="action-btns">
                <RouterLink :to="'/edit-aktor/' + aktor.id" class="btn-action btn-edit">✏️ Edit</RouterLink>
                <button @click="hapusAktor(aktor.id, aktor.nama_aktor)"
                  :disabled="deletingId === aktor.id" class="btn-action btn-delete">
                  <span v-if="deletingId === aktor.id">⏳</span>
                  <span v-else>🗑️ Hapus</span>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="showModal" class="modal-overlay" @click.self="showModal = false">
      <div class="modal-box">
        <h3>⚠️ Konfirmasi Hapus</h3>
        <p>Yakin menghapus aktor: <strong>{{ aktorToDelete?.nama_aktor }}</strong>?</p>
        <p class="modal-warning">Tindakan ini tidak bisa dibatalkan!</p>
        <div class="modal-actions">
          <button @click="showModal = false" class="btn-modal-cancel">Batal</button>
          <button @click="konfirmasiHapus" class="btn-modal-delete">🗑️ Hapus</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-block;
}
.badge-regular {
  background-color: #e4e4e4;
  color: #333333;
  border: 1px solid #b3b3b3;
}
.badge-l {
  background-color: #e0f2fe;
  color: #0369a1;
  border: 1px solid #bae6fd;
}
.badge-p {
  background-color: #ffe4e6;
  color: #e11d48;
  border: 1px solid #fecdd3;
}
img {
  margin: 0 auto;
}
th, td {
  text-align: center;
}
.page-title {
  margin-bottom: 24px; 
}

.title-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 12px;
  flex-wrap: wrap;
  gap: 12px;
}
.title-row h1 { font-size: 26px; color: #1a1a2e; }
.btn-back { color: #666; font-size: 14px; }
.btn-back:hover { color: #e94560; text-decoration: none; }

.table-wrapper { background: white; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.aktor-table { width: 100%; border-collapse: collapse; }
.aktor-table th { background: #1a1a2e; color: white; padding: 14px 16px; text-align: center; font-size: 13px; font-weight: 600; }
.aktor-table td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; vertical-align: middle; }
.aktor-table tr:last-child td { border-bottom: none; align-items: center; }
.aktor-table tr:hover td { background: #fafafa; }

.table-foto { width: 60px; height: 60px; object-fit: cover; border-radius: 100px; display: block; }
.aktor-name-cell { font-weight: 600; color: #1a1a2e; max-width: 200px; }
.empty-row { text-align: center; color: #aaa; padding: 40px !important; }

.action-btns { display: flex; gap: 8px; align-items: center; justify-content: center; }
.btn-action { padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; border: none; font-family: inherit; text-decoration: none; display: inline-block; transition: opacity 0.2s; }
.btn-edit   { background: #ebf5fb; color: #2980b9; }
.btn-delete { background: #fdedec; color: #e74c3c; }
.btn-action:hover { opacity: 0.75; }
.btn-action:disabled { opacity: 0.5; cursor: not-allowed; }

.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal-box { background: white; padding: 32px; border-radius: 16px; max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.3); text-align: center; }
.modal-box h3 { font-size: 20px; margin-bottom: 12px; }
.modal-box p  { color: #555; font-size: 14px; }
.modal-warning { color: #e74c3c !important; font-size: 13px !important; margin-bottom: 20px; }
.modal-actions { display: flex; gap: 12px; justify-content: center; }
.btn-modal-cancel { padding: 10px 24px; background: #f0f0f0; color: #555; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; }
.btn-modal-delete { padding: 10px 24px; background: #e74c3c; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: inherit; transition: background 0.2s; }
.btn-modal-delete:hover { background: #c0392b; }
</style>