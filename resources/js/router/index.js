import { useAuthStore } from '@/stores/auth'
import { useModalStore } from '@/stores/modal'
import { createRouter, createWebHistory } from 'vue-router'
import 'vue3-toastify/dist/index.css'
import MovieFilter from '../components/MovieFilter.vue'
import Account from '../views/Account.vue'
import AdminPanel from '../views/admin/AdminPanel.vue'
import AdminMembershipPackages from '../views/admin/MembershipPackages.vue'
import Favorites from '../views/Favorites.vue'
import GenreMovies from '../views/GenreMovies.vue'
import Home from '../views/Home.vue'
import MembershipPackages from '../views/membership/MembershipPackages.vue'
import MovieDetail from '../views/MovieDetail.vue'
import MovieList from '../views/MovieList.vue'
import SearchView from '../views/SearchView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/single',
    name: 'single',
    component: MovieList,
    props: { type: 'single' }
  },
  {
    path: '/series',
    name: 'series',
    component: MovieList,
    props: { type: 'series' }
  },
  {
    path: '/movie/:slug',
    name: 'movie',
    component: MovieDetail
  },

  {
    path: '/genre/:slug',
    name: 'genre',
    component: GenreMovies
  },
  {
    path: '/movies',
    name: 'movie-filter',
    component: MovieFilter
  },
  {
    path: '/country/:slug',
    name: 'country-movies',
    component: () => import('../components/movies/CountryMovies.vue')
  },
  {
    path: '/actor/:slug',
    name: 'actor-movies',
    component: () => import('../components/movies/ActorMovies.vue')
  },
  {
    path: '/actors',
    name: 'actors',
    component: () => import('../views/Actors.vue')
  },
  {
    path: '/watch/:slug',
    name: 'watch',
    component: () => import('../views/WatchMovie.vue')
  },
  {
    path: '/watch/:slug/:episode',
    name: 'watch-episode',
    component: () => import('../views/WatchMovie.vue')
  },
  {
    path: '/director/:slug',
    name: 'director-movies',
    component: () => import('../components/movies/DirectorMovies.vue')
  },
  {
    path: '/favorites',
    name: 'favorites',
    component: Favorites,
    meta: { requiresAuth: true }
  },
  {
    path: '/continue-watching',
    name: 'continue-watching',
    component: () => import('../views/ContinueWatching.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/account',
    name: 'account',
    component: Account,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/movies',
    name: 'admin.movies',
    component: () => import('@/views/admin/AdminMovieManager.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/actors',
    name: 'admin.actors',
    component: () => import('@/views/admin/AdminActorManager.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/admin/directors',
    name: 'admin.directors',
    component: () => import('@/views/admin/AdminDirectorManager.vue'),
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/login',
    name: 'login',
    beforeEnter: (to, from, next) => {
      const modalStore = useModalStore()
      modalStore.openLoginModal()
      next({ name: 'home' })
    }
  },
  {
    path: '/membership',
    name: 'membership',
    component: MembershipPackages,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/membership-packages',
    name: 'admin.membership-packages',
    component: AdminMembershipPackages,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/search',
    name: 'search',
    component: SearchView
  },
  {
    path: '/admin',
    name: 'admin.dashboard',
    component: AdminPanel,
    meta: {
      requiresAuth: true,
      requiresAdmin: true
    }
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation guard: chặn truy cập nếu chưa đăng nhập
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  const requiresAuth = to.matched.some(record => record.meta.requiresAuth)
  if (requiresAuth && !authStore.isAuthenticated) {
    next({ 
      name: 'home',
      query: { 'ban-hay-dang-nhap': true }
    })
  } else {
    next()
  }
})

export default router 