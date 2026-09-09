<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import api from '../../utils/api'

const films = ref([])
const loading = ref(true)
const error = ref(null)
const keyword = ref('')

const ambilDataFilm = async () => {
  try {
    loading.value = true
    error.value = null

    const response = await api.get('/public/films')

    films.value = response.data.data.data
  } catch (err) {
    error.value = 'Gagal mengambil data. Pastikan server Laravel sedang berjalan'
    console.error('Error:', err)
  } finally {
    loading.value = false
  }
}

// Fungsi pencarian dengan Debounce (tunggu 500ms setelah user berhenti mengetik)
// Agar tidak spam request API setiap mengetik 1 huruf
let searchTimeout = null
const cariFilm = () => {
  clearTiemout(searchTimeout)
  searchTimeout = setTimeout(async () =>{
    if (keyword.value.trim() === '') {
      // Jika search kosong, tampilkan semua film
      ambilDataFilm()
      return
    }

    try {
      loading.value = true
      error.value = null

      const response = await api.get('/public/search', {
        params: { keyword: keyword.value }
      })
      films.value = response.data.data.data
    } catch (err) {
      error.value = 'Pencarian gagal. Pastikan server laravel sedang berjalan.'
      console.error('Error cariFilm:', err)
    } finally {
      loading.value = false
    }
  }, 500)
}

// LifeCycle Hook
// onMounted: dijalankan setelah komponen tampil di browser
onMounted(() => {
  ambilDataFilm() 
})
</script>

<template>
  <div class="container">
    <div class="page-header">
      <h1>Daftar Film</h1>
      <p class="subtitle">Temukan film favoritmu</p>
    </div>

    <!-- Kotak Pencarian -->
    <div class="search-box">
      <input type="text" v-model="keyword" placeholder="Cari judul film..." @input="cariFilm" />
    </div>

    <!-- Tampil saat sedang memuat -->
    <p v-if="loading" class="loading-text">Memuat data film...</p>  

    <!-- Tampil jika error -->
    <p v-else-if="error" class="alert alert-error">❌ {{ error }}</p>

    <!-- Grid film saat data berhasil diambil -->
    <div v-else class="film-grid">
      <div v-for="film in films" :key="film.id" class="film-card">

        <div class="film-poster">
          <img :src="film.poster" :alt="film.judul" />
          <div class="film-overlay">
            <RouterLink class="btn btn-primary" :to="'/film/' + film.id">Lihat Detail</RouterLink>
          </div>
        </div>
        
        <div class="film-info">
          <h3 class="film-title">{{ film.judul }}</h3>
          <div class="film-meta">
            <span class="badge">{{ film.nama_genre }}</span>
            <span class="film-year">📅 {{ film.tanggal_rilis?.substring(0, 4) }}</span>
          </div>
          <p class="film-director">📽 {{ film.sutradara }}</p>
          <p class="film-director">🕒 {{ film.durasi }} menit</p>
        </div>

      </div>
    </div>

    <!-- Jika tidak ada film yang ditemukan -->
    <div v-if="!loading && films.length === 0 && !error" class="empty-state">
      <p>Tidak ada film yang ditemukan.</p>
    </div>
  </div>
</template>

<style scoped>
.page-header { margin-bottom: 28px; }
.page-header h1 { font-size: 32px; color: var(--color-dark); font-weight: 700; }
.subtitle { color: #888; margin-top: 4px; }

/* Search Box */
.search-box { margin-bottom: 28px; }
.search-box input {
  width: 100%;
  max-width: 480px;
  padding: 12px 18px;
  border: 2px solid #e0e0e0;
  border-radius: 50px;
  font-size: 15px;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.search-box input:focus {
  border-color: var(--color-primary);
  box-shadow: 0 0 0 3px rgba(233, 69, 96, 0.15);
}

/* Film Grid */
.film-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 24px;
}

/* Film Card */
.film-card {
  background: white;
  border-radius: 14px;
  box-shadow: var(--shadow);
  overflow: hidden;
  transition: transform 0.25s, box-shadow 0.25s;
}
.film-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}

/* Poster & Overlay */
.film-poster { position: relative; overflow: hidden; height: 270px; }
.film-poster img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
.film-card:hover .film-poster img { transform: scale(1.05); }

.film-overlay {
  position: absolute;
  inset: 0;
  background: rgba(26, 26, 46, 0.75);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
}
.film-card:hover .film-overlay { opacity: 1; }

/* Film Info */
.film-info { padding: 14px; }
.film-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--color-dark);
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.film-meta { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; flex-wrap: wrap; }
.badge { background: #fee2e2; color: var(--color-primary); font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 20px; }
.film-year, .film-director, .film-duration { font-size: 12px; color: #777; }

/* Empty State */
.empty-state { text-align: center; padding: 60px; color: #aaa; font-size: 18px; }
</style>