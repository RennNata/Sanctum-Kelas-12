import { createRouter, createWebHistory } from 'vue-router'

// Publik (tanpa login)
import HomeView from '../views/Public/HomeView.vue'
import DetailFilm from '../views/Public/DetailFilm.vue'

// Autentikasi
import LoginView from '../views/Auth/LoginView.vue'

// Admin (harus login)
import DashboardView from '../views/Admin/DashboardView.vue'

// Admin - Aktor
import KelolaAktor from '../views/Admin/Aktor/KelolaAktor.vue'
import TambahAktor from '../views/Admin/Aktor/TambahAktor.vue'
import EditAktor from '../views/Admin/Aktor/EditAktor.vue'

// Admin - Genre
import KelolaGenre from '../views/Admin/Genre/KelolaGenre.vue'
import TambahGenre from '../views/Admin/Genre/TambahGenre.vue'
import EditGenre from '../views/Admin/Genre/EditGenre.vue'

// Admin - Film
import KelolaFilm from '../views/Admin/Film/KelolaFilm.vue'
import TambahFilm from '../views/Admin/Film/TambahFilm.vue'
import EditFilm from '../views/Admin/Film/EditFilm.vue'

const routes = [
    // Publik
    { path: '/',         component: HomeView,  name: 'home' }, // localhost:5173/
    { path: '/film/:id', component: DetailFilm, name: 'detail-film' }, // localhost:5173/film/1

    // Login
    { path: '/login', component: LoginView, name: 'login' },

    // Admin (harus login) - ditandai meta: { requiresAuth: true }
    { path: '/dashboard', component: DashboardView, name: 'dashboard', meta: { requiresAuth: true } },

    // Admin - Aktor
    { path: '/kelola-aktor', component: KelolaAktor, name: 'kelola-aktor', meta: { requiresAuth: true } },
    { path: '/tambah-aktor', component: TambahAktor, name: 'tambah-aktor', meta: { requiresAuth: true } },
    { path: '/edit-aktor/:id', component: EditAktor, name: 'edit-aktor', meta: { requiresAuth: true } },

    // Admin - genre
    { path: '/kelola-genre', component: KelolaGenre, name: 'kelola-genre', meta: { requiresAuth: true } },
    { path: '/tambah-genre', component: TambahGenre, name: 'tambah-genre', meta: { requiresAuth: true } },
    { path: '/edit-genre/:id', component: EditGenre, name: 'edit-genre', meta: { requiresAuth: true } },

    // Admin - Film
    { path: '/kelola-film', component: KelolaFilm, name: 'kelola-film', meta: { requiresAuth: true } },
    { path: '/tambah-film', component: TambahFilm, name: 'tambah-film', meta: { requiresAuth: true } },
    { path: '/edit-film/:id', component: EditFilm, name: 'edit-film', meta: { requiresAuth: true } },
]

// Instance Router
const router = createRouter({
    history: createWebHistory(),
    routes,
})

// Navigation Guard (Middleware) || berjalan setiap kali berpindah halaman
router.beforeEach((to, from, next) => {

    if (to.meta.requiresAuth) {
        // cek apakah ada token login di localStorage
        const token = localStorage.getItem('token')

        if (!token) {
            next('/login')
        } else {
            next()
        }
    } else {
        next()
    }

})

export default router