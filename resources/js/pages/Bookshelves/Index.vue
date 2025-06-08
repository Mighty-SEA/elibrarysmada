<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import SearchBar from '@/components/SearchBar.vue';
import { BookOpen, Menu, X, Search, ArrowLeft, BookMarked } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import type { SharedData } from '@/types';

// Cek apakah rute register tersedia
const hasRegisterRoute = computed(() => {
  try {
    return route().has('register');
  } catch {
    return false;
  }
});

const page = usePage<SharedData>();
const isMobileMenuOpen = ref(false);
const isMobileSearchOpen = ref(false);

// Fungsi untuk membuka dan menutup mobile menu
function toggleMobileMenu() {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
}

// Fungsi untuk membuka dan menutup mobile search
function toggleMobileSearch() {
  isMobileSearchOpen.value = !isMobileSearchOpen.value;
  if (isMobileSearchOpen.value) {
    isMobileMenuOpen.value = false;
  }
}

// Fungsi helper untuk cek admin
function isAdmin() {
  return page.props.auth?.user?.role === 'administrasi';
}

// Fungsi untuk mendapatkan login URL
function getLoginUrl() {
  try {
    return route('login');
  } catch {
    return '/login';
  }
}

// Fungsi untuk mendapatkan register URL
function getRegisterUrl() {
  try {
    return route('register');
  } catch {
    return '/register';
  }
}

// Contoh data rak buku (akan diganti dengan data dari server)
const bookshelfItems = ref([]);
</script>

<template>
  <Head title="Rak Buku - E-Library SMADA">
    <meta name="description" content="Rak buku peminjaman perpustakaan digital" />
  </Head>

  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-20">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo & Menu Toggle -->
          <div class="flex items-center">
            <Link :href="route('home')" class="flex items-center gap-3">
              <BookOpen class="h-8 w-8 text-blue-600" />
              <div class="flex flex-col">
                <div class="text-xl font-bold text-blue-700 tracking-wide">E - LIBRARY</div>
                <div class="text-xs font-semibold text-blue-500 tracking-wider uppercase border-t border-blue-200 pt-0.5 mt-0.5">SMA PEMUDA BANJARAN</div>
              </div>
            </Link>
          </div>
          
          <!-- Desktop Search Bar -->
          <div class="flex-1 mx-6 max-w-xl hidden md:block">
            <SearchBar />
          </div>
          
          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-4">
            <template v-if="page.props.auth?.user">
              <div class="mr-4 text-gray-700">
                Halo, <span class="font-medium">{{ page.props.auth.user.name }}</span>
              </div>
              
              <!-- Tombol Dashboard hanya untuk admin -->
              <Link
                v-if="isAdmin()"
                :href="route('dashboard')"
                class="inline-flex items-center gap-2 rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
              >
                Dashboard
              </Link>
              
              <!-- Tombol Rak Buku untuk murid dan guru (aktif) -->
              <Link
                v-else
                :href="route('bookshelves')"
                class="inline-flex items-center gap-2 rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
              >
                <BookMarked class="h-4 w-4" />
                Rak Buku
              </Link>
            </template>
            
            <template v-else>
              <Link
                :href="getLoginUrl()"
                class="inline-block rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 hover:text-blue-700 transition-colors"
              >
                Masuk
              </Link>
              <Link
                v-if="hasRegisterRoute"
                :href="getRegisterUrl()"
                class="inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
              >
                Daftar
              </Link>
            </template>
          </div>
          
          <!-- Mobile Search & Menu Button -->
          <div class="flex items-center gap-2 md:hidden">
            <button @click="toggleMobileSearch" class="p-2 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
              <Search class="h-6 w-6" />
            </button>
            <button 
              @click="toggleMobileMenu" 
              class="p-2 rounded-md text-gray-700 hover:bg-gray-100 transition-colors"
            >
              <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
              <X v-else class="h-6 w-6" />
            </button>
          </div>
        </div>
        
        <!-- Mobile Search Dropdown -->
        <transition name="fade">
          <div v-if="isMobileSearchOpen" class="md:hidden absolute left-0 right-0 top-full bg-white shadow-lg z-30 px-4 py-4 border-b border-gray-200">
            <SearchBar />
          </div>
        </transition>
        
        <!-- Mobile Navigation Menu -->
        <div 
          v-if="isMobileMenuOpen" 
          class="md:hidden mt-4 py-3 border-t border-gray-200 space-y-4"
        >
          <template v-if="page.props.auth?.user">
            <div class="px-2 py-1 text-gray-700">
              Halo, <span class="font-medium">{{ page.props.auth.user.name }}</span>
            </div>
            
            <Link
              v-if="isAdmin()"
              :href="route('dashboard')"
              class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md"
            >
              Dashboard
            </Link>
            
            <Link
              v-else
              :href="route('bookshelves')"
              class="block px-2 py-2 bg-blue-600 text-white hover:bg-blue-700 rounded-md flex items-center gap-2"
            >
              <BookMarked class="h-5 w-5" />
              Rak Buku
            </Link>
          </template>
          
          <template v-else>
            <div class="flex flex-col space-y-2">
              <Link
                :href="getLoginUrl()"
                class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md"
              >
                Masuk
              </Link>
              <Link
                v-if="hasRegisterRoute"
                :href="getRegisterUrl()"
                class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md"
              >
                Daftar
              </Link>
            </div>
          </template>
        </div>
      </div>
    </header>

    <!-- Main Content - Rak Buku -->
    <main class="flex-grow py-6 md:py-12">
      <div class="container mx-auto px-4">
        <!-- Header with back button -->
        <div class="flex items-center mb-8">
          <Link 
            :href="route('home')" 
            class="inline-flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-100 text-gray-700 mr-4"
          >
            <ArrowLeft class="h-5 w-5" />
            <span class="hidden sm:inline">Kembali ke Katalog</span>
          </Link>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Rak Buku Saya</h1>
        </div>
        
        <!-- Bookshelf Content with beautiful styling -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
          <!-- Empty Bookshelf State -->
          <div v-if="bookshelfItems.length === 0" class="flex flex-col items-center justify-center py-16 px-4">
            <div class="relative mb-6">
              <!-- Decorative circle -->
              <div class="absolute inset-0 bg-blue-100 rounded-full opacity-30 scale-150"></div>
              <!-- Bookshelf icon -->
              <BookMarked class="h-20 w-20 text-blue-500 relative z-10" />
            </div>
            <h2 class="text-xl font-semibold text-gray-800 mb-2">Rak buku Anda masih kosong</h2>
            <p class="text-gray-500 text-center max-w-md mb-8">
              Silakan tambahkan buku ke rak untuk melanjutkan proses peminjaman
            </p>
            <Link 
              :href="route('home')" 
              class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 text-white shadow-lg ring-1 ring-blue-400/30 hover:from-blue-600 hover:to-indigo-700 hover:scale-105 hover:shadow-xl transition-all duration-200"
            >
              <BookOpen class="w-5 h-5" />
              <span>Jelajahi Katalog</span>
            </Link>
          </div>
          
          <!-- Bookshelf with Items (Empty for now, will be implemented later) -->
          <div v-else class="divide-y divide-gray-200">
            <!-- Bookshelf items will be rendered here -->
          </div>
        </div>
      </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 mt-auto">
      <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="flex items-center gap-2 mb-4 md:mb-0">
            <BookOpen class="h-6 w-6 text-blue-600" />
            <div class="text-lg font-bold text-blue-700">E-Library SMADA</div>
          </div>
          <div class="text-sm text-gray-500">
            &copy; {{ new Date().getFullYear() }} SMA Pemuda Banjaran. All rights reserved.
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<style scoped>
/* Fade transition for mobile search dropdown */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style> 