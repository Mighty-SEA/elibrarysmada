<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Tag, User, Building, Calendar, Hash, MapPin, Share2, Heart, ChevronLeft, Search, Menu, X, ChevronDown, ChevronUp, BookMarked } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import SearchBar from '@/components/SearchBar.vue';
import BookCard from '@/components/BookCard.vue';
import type { SharedData } from '@/types';

defineProps<{
  book: any;
  relatedBooks: any[];
}>();

const page = usePage<SharedData>();
const isMobileMenuOpen = ref(false);
const isMobileSearchOpen = ref(false);
const isDescriptionExpanded = ref(false);

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

// Fungsi untuk toggle deskripsi
function toggleDescription() {
  isDescriptionExpanded.value = !isDescriptionExpanded.value;
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

// Cek apakah rute register tersedia
const hasRegisterRoute = computed(() => {
  try {
    return route().has('register');
  } catch {
    return false;
  }
});
</script>

<template>
  <Head :title="book.judul + ' - E-Library SMADA'">
    <meta name="description" :content="book.deskripsi ? book.deskripsi.substring(0, 160) : 'Detail buku ' + book.judul" />
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
            <template v-if="page.props.auth && page.props.auth.user">
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
              
              <!-- Tombol Keranjang untuk murid dan guru -->
              <Link
                v-else
                :href="route('bookshelves')"
                class="inline-flex items-center gap-2 rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
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
          <template v-if="page.props.auth && page.props.auth.user">
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
              class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md flex items-center gap-2"
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
    
    <main class="flex-grow py-8">
      <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <div class="mb-6">
          <div class="flex items-center text-sm text-gray-500 mb-6">
            <Link :href="route('home')" class="hover:text-blue-600 flex items-center gap-1">
              <ChevronLeft class="h-4 w-4" />
              Kembali ke katalog
            </Link>
          </div>
        </div>
        
        <!-- Book Detail Section -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
          <div class="md:flex">
            <!-- Book Cover Image -->
            <div class="md:w-1/3 lg:w-1/4 bg-gray-100 p-6 flex justify-center items-start">
              <div class="aspect-[2/3] max-w-xs w-full bg-white shadow-md rounded-md overflow-hidden border border-gray-200">
                <img 
                  :src="book.cover_url" 
                  :alt="book.judul" 
                  class="w-full h-full object-cover"
                  onerror="this.src='/images/book-placeholder.svg'; this.onerror=null;"
                />
              </div>
            </div>
            
            <!-- Book Information -->
            <div class="md:w-2/3 lg:w-3/4 p-6 md:p-8">
              <!-- Book Title and Status -->
              <div class="flex flex-wrap justify-between gap-4 mb-6">
                <div>
                  <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ book.judul }}</h1>
                  <p class="text-lg text-gray-700">
                    <span class="font-medium">Penulis:</span> {{ book.penulis || 'Tidak diketahui' }}
                  </p>
                </div>
                <div>
                  <span 
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                    :class="book.jumlah > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ book.jumlah > 0 ? 'Tersedia' : 'Tidak Tersedia' }}
                  </span>
                </div>
              </div>
              
              <!-- Book Details Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Buku</h2>
                  <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                      <User class="h-5 w-5 text-gray-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Penulis</span>
                        <p class="text-gray-800">{{ book.penulis || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <Building class="h-5 w-5 text-gray-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Penerbit</span>
                        <p class="text-gray-800">{{ book.penerbit || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <Calendar class="h-5 w-5 text-gray-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Tahun Terbit</span>
                        <p class="text-gray-800">{{ book.tahun_terbit || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <Hash class="h-5 w-5 text-gray-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">ISBN</span>
                        <p class="text-gray-800">{{ book.isbn || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                  </ul>
                </div>
                
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 mb-4">Detail Lainnya</h2>
                  <ul class="space-y-3">
                    <li class="flex items-start gap-2">
                      <Tag class="h-5 w-5 text-gray-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Kategori</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                          <span 
                            v-for="(category, index) in book.kategori_list" 
                            :key="index"
                            class="inline-block px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"
                          >
                            {{ category }}
                          </span>
                          <span v-if="!book.kategori_list || book.kategori_list.length === 0" class="text-gray-600">
                            Tidak ada kategori
                          </span>
                        </div>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <MapPin class="h-5 w-5 text-gray-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Lokasi</span>
                        <p class="text-gray-800">{{ book.lokasi || 'Tidak tersedia' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <BookOpen class="h-5 w-5 text-gray-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Jumlah Tersedia</span>
                        <p class="text-gray-800">{{ book.jumlah || 0 }} buku</p>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              
              <!-- Description -->
              <div class="mb-8">
                <div @click="toggleDescription" class="flex justify-between items-center cursor-pointer mb-4">
                  <h2 class="text-lg font-semibold text-gray-900">Deskripsi</h2>
                  <button class="p-1 hover:bg-gray-100 rounded-full transition-colors">
                    <ChevronDown v-if="!isDescriptionExpanded" class="h-5 w-5 text-gray-500" />
                    <ChevronUp v-else class="h-5 w-5 text-gray-500" />
                  </button>
                </div>
                <transition name="fade-height">
                  <div v-if="isDescriptionExpanded" class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-700 whitespace-pre-line text-justify">{{ book.deskripsi || 'Tidak ada deskripsi untuk buku ini.' }}</p>
                  </div>
                  <div 
                    v-else 
                    class="bg-gray-50 rounded-lg p-4 border border-gray-200 cursor-pointer hover:bg-gray-100 transition-colors"
                    @click="toggleDescription"
                  >
                    <p class="text-gray-700 line-clamp-2 whitespace-pre-line text-justify">{{ book.deskripsi || 'Tidak ada deskripsi untuk buku ini.' }}</p>
                    <p v-if="book.deskripsi && book.deskripsi.length > 100" class="text-blue-600 text-sm mt-1 flex items-center gap-1">
                      <span>Klik untuk melihat selengkapnya</span>
                      <ChevronDown class="h-4 w-4" />
                    </p>
                  </div>
                </transition>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex flex-wrap justify-between items-center">
                <div class="flex flex-wrap gap-3">
                  <button class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition-colors">
                    <Share2 class="h-5 w-5" />
                    Bagikan
                  </button>
                  
                  <button class="inline-flex items-center gap-2 px-6 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-lg font-medium transition-colors">
                    <Heart class="h-5 w-5" />
                    Tambah ke Favorit
                  </button>
                </div>
                
                <div class="mt-3 sm:mt-0">
                  <Link 
                    v-if="page.props.auth && page.props.auth.user && page.props.auth.user.role !== 'administrasi' && book.jumlah > 0"
                    :href="route('loans.request', { book_id: book.id })"
                    method="post"
                    as="button"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                  >
                    <BookOpen class="h-5 w-5" />
                    Pinjam
                  </Link>
                  
                  <Link 
                    v-if="!page.props.auth || !page.props.auth.user"
                    :href="getLoginUrl()"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                  >
                    Masuk untuk Meminjam
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Related Books Section -->
        <div v-if="relatedBooks && relatedBooks.length > 0" class="mt-12">
          <h2 class="text-xl font-bold text-gray-800 mb-6">Buku Terkait</h2>
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4 md:gap-6">
            <BookCard
              v-for="relatedBook in relatedBooks"
              :key="relatedBook.id"
              :id="relatedBook.id"
              :title="relatedBook.judul"
              :author="relatedBook.penulis"
              :coverImage="relatedBook.cover_url"
              :category="relatedBook.kategori_list && relatedBook.kategori_list.length > 0 ? relatedBook.kategori_list[0] : ''"
              :available="relatedBook.jumlah > 0"
            />
          </div>
        </div>
      </div>
    </main>
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

/* Fade transition for description height */
.fade-height-enter-active, .fade-height-leave-active {
  transition: all 0.3s ease-out;
  overflow: hidden;
}
.fade-height-enter-from, .fade-height-leave-to {
  max-height: 0;
  opacity: 0;
  margin-top: -8px;
}
.fade-height-enter-to, .fade-height-leave-from {
  max-height: 1000px;
  opacity: 1;
  margin-top: 0;
}

/* Line clamp for truncated text */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>