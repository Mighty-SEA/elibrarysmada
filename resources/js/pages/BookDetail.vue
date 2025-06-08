<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Tag, User, Building, Calendar, Hash, MapPin, ChevronLeft, Search, Menu, X, ChevronDown, ChevronUp, BookMarked, LogOut, FileText, CheckCircle, AlertCircle } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import SearchBar from '@/components/SearchBar.vue';
import BookCard from '@/components/BookCard.vue';
import type { SharedData } from '@/types';
import axios from 'axios';

const props = defineProps<{
  book: any;
  relatedBooks: any[];
}>();

const page = usePage<SharedData>();
const isMobileMenuOpen = ref(false);
const isMobileSearchOpen = ref(false);
const isDescriptionExpanded = ref(false);
const showModal = ref(false);
const modalStatus = ref('success'); // 'success' or 'error'
const modalMessage = ref('');
const isLoading = ref(false);

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

// Fungsi untuk menangani peminjaman buku
async function handleBorrowBook() {
  if (isLoading.value) return;
  
  isLoading.value = true;
  
  try {
    // Get CSRF token from meta tag
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    const response = await axios.post(route('loans.request'), {
      book_id: props.book.id
    }, {
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      }
    });
    
    if (response.data.success) {
      modalStatus.value = 'success';
      modalMessage.value = response.data.message || 'Permintaan peminjaman buku berhasil! Silakan ambil buku di perpustakaan.';
      showModal.value = true;
      
      // Don't mutate the prop directly
      if (props.book.ketersediaan > 0) {
        // Create a local copy instead
        const updatedBook = { 
          ...props.book, 
          ketersediaan: props.book.ketersediaan - 1 
        };
        
        // Update only what's displayed in the template
        const countElement = document.querySelector('[data-book-count]');
        if (countElement) {
          if (updatedBook.ketersediaan > 0) {
            countElement.textContent = `Tersedia (${updatedBook.ketersediaan})`;
          } else {
            countElement.textContent = 'Tidak Tersedia';
            // Find the parent of countElement with a class containing 'bg-green'
            const parent = countElement.closest('.bg-green-100');
            if (parent) {
              parent.classList.remove('bg-green-100', 'text-green-800');
              parent.classList.add('bg-red-100', 'text-red-800');
              
              // Find and remove the animated dot
              const animatedDot = parent.querySelector('.relative.flex.h-2.w-2.mr-2');
              if (animatedDot) {
                animatedDot.remove();
                
                // Create and add the red dot
                const redDot = document.createElement('span');
                redDot.className = 'h-2 w-2 bg-red-500 rounded-full mr-2';
                parent.insertBefore(redDot, countElement);
              }
            }
          }
        }
        
        // Update the availability in the book details
        const availabilityElement = document.querySelector('.text-sm.font-medium.text-gray-700 + p .text-green-700, .text-sm.font-medium.text-gray-700 + p .text-red-600');
        if (availabilityElement) {
          availabilityElement.textContent = updatedBook.ketersediaan;
          if (updatedBook.ketersediaan === 0) {
            availabilityElement.classList.remove('text-green-700');
            availabilityElement.classList.add('text-red-600');
          }
        }
        
        // Hide the borrow button if no books are available anymore
        if (updatedBook.ketersediaan === 0) {
          const borrowButtonContainer = document.querySelector('.flex.justify-end');
          const unavailableMessage = document.createElement('div');
          unavailableMessage.className = 'bg-red-50 border border-red-200 rounded-lg p-4 w-full';
          unavailableMessage.innerHTML = `
            <p class="text-red-800 font-medium">Buku tidak tersedia untuk dipinjam</p>
            <p class="text-red-700 text-sm mt-1">Semua salinan buku ini sedang dipinjam. Silakan coba lagi nanti.</p>
          `;
          
          if (borrowButtonContainer && borrowButtonContainer.parentNode) {
            borrowButtonContainer.parentNode.replaceChild(unavailableMessage, borrowButtonContainer);
          }
        }
      }
    }
  } catch (error: any) { // Type assertion for error
    modalStatus.value = 'error';
    modalMessage.value = error.response?.data?.message || 'Terjadi kesalahan saat memproses permintaan peminjaman.';
    showModal.value = true;
  } finally {
    isLoading.value = false;
  }
}

// Fungsi untuk menutup modal
function closeModal() {
  showModal.value = false;
}

// Fungsi untuk navigasi ke rak buku
function navigateToBookshelf() {
  window.location.href = route('bookshelves');
}

// Fungsi helper untuk cek admin
function isAdmin() {
  return page.props.auth?.user?.role === 'administrasi';
}

// Fungsi helper untuk cek user (murid/guru)
function isUser() {
  return page.props.auth?.user && page.props.auth?.user?.role !== 'administrasi';
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
              
              <!-- Tombol Rak Buku untuk murid dan guru -->
              <Link
                v-else
                :href="route('bookshelves')"
                class="inline-flex items-center gap-2 rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
              >
                <BookMarked class="h-4 w-4" />
                Rak Buku
              </Link>
              
              <!-- Tombol Logout untuk semua user -->
              <Link
                :href="route('logout')"
                method="post"
                as="button"
                class="inline-flex items-center gap-2 rounded-md border border-red-600 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50"
              >
                <LogOut class="h-4 w-4" />
                Logout
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
              class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md flex items-center gap-2"
            >
              <FileText class="h-5 w-5" />
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
            
            <Link
              :href="route('logout')"
              method="post"
              as="button"
              class="block px-2 py-2 text-red-600 hover:bg-red-50 rounded-md flex items-center gap-2"
            >
              <LogOut class="h-5 w-5" />
              Logout
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
            <div class="md:w-2/3 lg:w-3/4 p-4 md:p-6">
              <!-- Book Title and Status -->
              <div class="flex flex-wrap justify-between gap-3 mb-5">
                <div>
                  <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2 leading-tight">{{ book.judul }}</h1>
                  <p class="text-lg text-gray-700">
                    <span class="font-medium">Penulis:</span> {{ book.penulis || 'Tidak diketahui' }}
                  </p>
                </div>
                <div>
                  <span 
                    class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium shadow-sm"
                    :class="book.ketersediaan > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                  >
                    <span v-if="book.ketersediaan > 0" class="relative flex h-2 w-2 mr-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span v-else class="h-2 w-2 bg-red-500 rounded-full mr-2"></span>
                    <span data-book-count>{{ book.ketersediaan > 0 ? `Tersedia (${book.ketersediaan})` : 'Tidak Tersedia' }}</span>
                  </span>
                </div>
              </div>
              
              <!-- Book Details Grid -->
              <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-1 border-gray-200">Informasi Buku</h2>
                  <ul class="space-y-2.5">
                    <li class="flex items-start gap-2">
                      <User class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm font-medium text-gray-700">Penulis</span>
                        <p class="text-gray-800">{{ book.penulis || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <Building class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm font-medium text-gray-700">Penerbit</span>
                        <p class="text-gray-800">{{ book.penerbit || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <Calendar class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm font-medium text-gray-700">Tahun Terbit</span>
                        <p class="text-gray-800">{{ book.tahun_terbit || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <Hash class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm font-medium text-gray-700">ISBN</span>
                        <p class="text-gray-800">{{ book.isbn || 'Tidak diketahui' }}</p>
                      </div>
                    </li>
                  </ul>
                </div>
                
                <div>
                  <h2 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-1 border-gray-200">Detail Lainnya</h2>
                  <ul class="space-y-2.5">
                    <li class="flex items-start gap-2">
                      <Tag class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm font-medium text-gray-700">Kategori</span>
                        <div class="flex flex-wrap gap-1 mt-1">
                          <span 
                            v-for="(category, index) in book.kategori_list" 
                            :key="index"
                            class="inline-block px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800 border border-blue-200"
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
                      <MapPin class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm font-medium text-gray-700">Lokasi</span>
                        <p class="text-gray-800">{{ book.lokasi || 'Tidak tersedia' }}</p>
                      </div>
                    </li>
                    <li class="flex items-start gap-2">
                      <BookOpen class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm font-medium text-gray-700">Stok Buku</span>
                        <p class="text-gray-800">
                          <span>Total: <span class="font-medium">{{ book.jumlah || 0 }}</span> buku</span>
                          <span class="mx-1">•</span>
                          <span>Tersedia: <span 
                            :class="book.ketersediaan > 0 ? 'font-medium text-green-700' : 'font-medium text-red-600'"
                          >{{ book.ketersediaan || 0 }}</span> buku</span>
                        </p>
                        <p v-if="book.jumlah > book.ketersediaan" class="text-xs text-gray-600 mt-1">
                          <span v-if="book.pending_loans_count > 0">
                            {{ book.pending_loans_count }} orang belum mengambil buku
                          </span>
                          <span v-if="book.pending_loans_count > 0 && book.borrowed_loans_count > 0">
                            • 
                          </span>
                          <span v-if="book.borrowed_loans_count > 0">
                            {{ book.borrowed_loans_count }} buku sedang dipinjam
                          </span>
                        </p>
                      </div>
                    </li>
                  </ul>
                </div>
              </div>
              
              <!-- Description -->
              <div class="mb-6">
                <div @click="toggleDescription" class="flex justify-between items-center cursor-pointer mb-3 bg-blue-50 px-4 py-2 rounded-lg">
                  <h2 class="text-lg font-semibold text-blue-800">Deskripsi</h2>
                  <button class="p-1 hover:bg-blue-100 rounded-full transition-colors">
                    <ChevronDown v-if="!isDescriptionExpanded" class="h-5 w-5 text-blue-600" />
                    <ChevronUp v-else class="h-5 w-5 text-blue-600" />
                  </button>
                </div>
                <transition name="fade-height">
                  <div v-if="isDescriptionExpanded" class="bg-white rounded-lg p-4 border border-gray-200 leading-relaxed">
                    <p class="text-gray-700 whitespace-pre-line text-justify">{{ book.deskripsi || 'Tidak ada deskripsi untuk buku ini.' }}</p>
                  </div>
                  <div 
                    v-else 
                    class="bg-white rounded-lg p-4 border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors"
                    @click="toggleDescription"
                  >
                    <p class="text-gray-700 line-clamp-2 whitespace-pre-line text-justify">{{ book.deskripsi || 'Tidak ada deskripsi untuk buku ini.' }}</p>
                    <p v-if="book.deskripsi && book.deskripsi.length > 100" class="text-blue-600 text-sm mt-1 flex items-center gap-1 font-medium">
                      <span>Klik untuk melihat selengkapnya</span>
                      <ChevronDown class="h-4 w-4" />
                    </p>
                  </div>
                </transition>
              </div>
              
              <!-- Action Buttons -->
              <div class="flex flex-wrap justify-between items-center">
                <!-- Admin Button -->
                <div v-if="isAdmin()" class="bg-blue-50 border border-blue-200 rounded-lg p-4 w-full">
                  <p class="text-blue-800 font-medium">Sebagai Admin Anda tidak dapat meminjam buku</p>
                  <p class="text-blue-700 text-sm mt-1">Anda dapat mengelola buku ini melalui dashboard admin.</p>
                </div>
                
                <!-- User Button - Logged In -->
                <div v-else-if="isUser()" class="w-full">
                  <div v-if="book.ketersediaan > 0" class="flex justify-end">
                    <button 
                      @click="handleBorrowBook"
                      class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
                      :disabled="isLoading"
                    >
                      <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      <BookOpen v-else class="h-5 w-5" />
                      {{ isLoading ? 'Memproses...' : 'Pinjam Buku' }}
                    </button>
                  </div>
                  <div v-else class="bg-red-50 border border-red-200 rounded-lg p-4 w-full">
                    <p class="text-red-800 font-medium">Buku tidak tersedia untuk dipinjam</p>
                    <p class="text-red-700 text-sm mt-1">
                      {{ book.pending_loans_count > 0 ? 
                        `${book.pending_loans_count} orang belum mengambil buku ini di perpustakaan.` : 
                        'Semua salinan buku ini sedang dipinjam. Silakan coba lagi nanti.' }}
                    </p>
                  </div>
                </div>
                
                <!-- User Button - Not Logged In -->
                <div v-else class="w-full flex justify-center">
                  <Link 
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
    
    <!-- Notification Modal -->
    <transition name="fade">
      <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50">
        <div class="absolute inset-0 backdrop-blur-sm bg-white/30" @click="closeModal"></div>
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md mx-4 z-10 overflow-hidden">
          <div 
            class="p-5 flex items-center gap-4"
            :class="modalStatus === 'success' ? 'bg-green-50' : 'bg-red-50'"
          >
            <div 
              class="rounded-full p-3"
              :class="modalStatus === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'"
            >
              <CheckCircle v-if="modalStatus === 'success'" class="h-7 w-7" />
              <AlertCircle v-else class="h-7 w-7" />
            </div>
            <h3 
              class="text-xl font-bold"
              :class="modalStatus === 'success' ? 'text-green-800' : 'text-red-800'"
            >
              {{ modalStatus === 'success' ? 'Berhasil' : 'Gagal' }}
            </h3>
          </div>
          
          <div class="p-6">
            <p class="text-gray-700 mb-8 text-base leading-relaxed">{{ modalMessage }}</p>
            
            <div class="flex flex-wrap gap-3 justify-end">
              <button 
                @click="closeModal"
                class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors font-medium"
              >
                Tutup
              </button>
              
              <button 
                v-if="modalStatus === 'success'"
                @click="navigateToBookshelf"
                class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
              >
                Lihat Rak Buku
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
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