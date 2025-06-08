// New Bookshelves Page

<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, BookMarked, CheckCircle2, AlertTriangle, Clock, Menu, X, Search, LogOut } from 'lucide-vue-next';
import { ref, computed } from 'vue';
import type { SharedData } from '@/types';

// Props
defineProps<{
  activeLoans: Array<{
    id: number;
    book_id: number;
    status: string;
    request_date: string;
    approval_date: string | null;
    due_date: string | null;
    return_date: string | null;
    book_title: string;
    book_cover: string;
    author: string;
    is_overdue: boolean;
  }>;
  loanHistory: Array<{
    id: number;
    book_id: number;
    status: string;
    request_date: string;
    approval_date: string | null;
    due_date: string | null;
    return_date: string | null;
    book_title: string;
    book_cover: string;
    author: string;
  }>;
}>();

// Tab state
const activeTab = ref('current'); // 'current' or 'history'

// Format tanggal
const formatDate = (dateString: string | null) => {
  if (!dateString) return '-';
  
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric'
  };
  
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Fungsi untuk mendapatkan badge status
const getStatusBadge = (status: string, isOverdue: boolean) => {
  if (isOverdue) {
    return {
      label: 'Terlambat',
      bgColor: 'bg-red-100',
      textColor: 'text-red-800',
      icon: AlertTriangle
    };
  }

  switch (status) {
    case 'belum_diambil':
      return {
        label: 'Menunggu Diambil',
        bgColor: 'bg-blue-100',
        textColor: 'text-blue-800',
        icon: Clock
      };
    case 'dipinjam':
      return {
        label: 'Sedang Dipinjam',
        bgColor: 'bg-yellow-100',
        textColor: 'text-yellow-800',
        icon: BookOpen
      };
    case 'dikembalikan':
      return {
        label: 'Dikembalikan',
        bgColor: 'bg-green-100',
        textColor: 'text-green-800',
        icon: CheckCircle2
      };
    case 'terlambat':
      return {
        label: 'Terlambat',
        bgColor: 'bg-red-100',
        textColor: 'text-red-800',
        icon: AlertTriangle
      };
    default:
      return {
        label: status,
        bgColor: 'bg-gray-100',
        textColor: 'text-gray-800',
        icon: BookOpen
      };
  }
};

// Hitung hari tersisa
const calculateDaysLeft = (dueDateString: string) => {
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  
  const dueDate = new Date(dueDateString);
  dueDate.setHours(0, 0, 0, 0);
  
  const diffTime = dueDate.getTime() - today.getTime();
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  return diffDays;
};

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
</script>

<template>
  <Head title="Rak Buku - E-Library SMADA" />

  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-20">
      <div class="container mx-auto px-4 py-3 md:py-4">
        <div class="flex items-center justify-between">
          <!-- Logo & Menu Toggle -->
          <div class="flex items-center">
            <Link :href="route('home')" class="flex items-center gap-2 md:gap-3">
              <BookOpen class="h-6 w-6 md:h-8 md:w-8 text-blue-600" />
              <div class="flex flex-col">
                <div class="text-lg md:text-xl font-bold text-blue-700 tracking-wide">E - LIBRARY</div>
                <div class="text-xs font-semibold text-blue-500 tracking-wider uppercase border-t border-blue-200 pt-0.5 mt-0.5">SMA PEMUDA BANJARAN</div>
              </div>
            </Link>
          </div>
          
          <!-- Desktop Search Bar -->
          <div class="flex-1 mx-6 max-w-xl hidden md:block">
            <!-- Placeholder for search bar, can be replaced with SearchBar component if needed -->
            <div class="relative rounded-lg border border-gray-300 bg-white shadow-sm focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500">
              <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <Search class="h-5 w-5 text-gray-400" />
              </div>
              <Link :href="route('home')" class="block w-full pl-10 pr-4 py-2 text-sm text-gray-500">
                Cari buku di katalog...
              </Link>
            </div>
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
              
              <!-- Tombol Logout untuk murid dan guru -->
              <Link
                v-else
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
          <div class="flex items-center gap-1 md:hidden">
            <button @click="toggleMobileSearch" class="p-1.5 rounded-md text-gray-700 hover:bg-gray-100 transition-colors">
              <Search class="h-5 w-5" />
            </button>
            <button 
              @click="toggleMobileMenu" 
              class="p-1.5 rounded-md text-gray-700 hover:bg-gray-100 transition-colors"
            >
              <Menu v-if="!isMobileMenuOpen" class="h-5 w-5" />
              <X v-else class="h-5 w-5" />
            </button>
          </div>
        </div>
        
        <!-- Mobile Search Dropdown -->
        <transition name="fade">
          <div v-if="isMobileSearchOpen" class="md:hidden absolute left-0 right-0 top-full bg-white shadow-lg z-30 px-4 py-3 border-b border-gray-200">
            <Link :href="route('home')" class="block w-full px-3 py-2 text-sm text-gray-500 border border-gray-300 rounded-lg">
              Cari buku di katalog...
            </Link>
          </div>
        </transition>
        
        <!-- Mobile Navigation Menu -->
        <div 
          v-if="isMobileMenuOpen" 
          class="md:hidden mt-3 py-2 border-t border-gray-200 space-y-3"
        >
          <template v-if="page.props.auth?.user">
            <div class="px-2 py-1 text-gray-700 text-sm">
              Halo, <span class="font-medium">{{ page.props.auth.user.name }}</span>
            </div>
            
            <Link
              v-if="isAdmin()"
              :href="route('dashboard')"
              class="block px-2 py-1.5 text-blue-600 hover:bg-blue-50 rounded-md text-sm"
            >
              Dashboard
            </Link>
            
            <Link
              v-else
              :href="route('logout')"
              method="post"
              as="button"
              class="block w-full text-left px-2 py-1.5 text-red-600 hover:bg-red-50 rounded-md flex items-center gap-2 text-sm"
            >
              <LogOut class="h-4 w-4" />
              Logout
            </Link>
          </template>
          
          <template v-else>
            <div class="flex flex-col space-y-1">
              <Link
                :href="getLoginUrl()"
                class="block px-2 py-1.5 text-blue-600 hover:bg-blue-50 rounded-md text-sm"
              >
                Masuk
              </Link>
              <Link
                v-if="hasRegisterRoute"
                :href="getRegisterUrl()"
                class="block px-2 py-1.5 text-blue-600 hover:bg-blue-50 rounded-md text-sm"
              >
                Daftar
              </Link>
            </div>
          </template>
        </div>
      </div>
    </header>

    <!-- Main Content - Rak Buku -->
    <main class="py-5 md:py-8">
      <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-5 md:mb-8">
          <h1 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-800">Rak Buku Saya</h1>
          <p class="text-sm md:text-base text-gray-600 mt-1">Lihat peminjaman dan riwayat peminjaman buku</p>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="mb-6 overflow-x-auto -mx-4 px-4 md:mx-0 md:px-0">
          <div class="flex border-b border-gray-200 min-w-max">
            <button 
              @click="activeTab = 'current'" 
              class="px-4 py-3 font-medium text-sm flex items-center gap-2 whitespace-nowrap"
              :class="activeTab === 'current' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-800'"
            >
              <BookOpen class="w-4 h-4" />
              Peminjaman Aktif
            </button>
            <button 
              @click="activeTab = 'history'" 
              class="px-4 py-3 font-medium text-sm flex items-center gap-2 whitespace-nowrap"
              :class="activeTab === 'history' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-800'"
            >
              <CheckCircle2 class="w-4 h-4" />
              Riwayat Peminjaman
            </button>
          </div>
        </div>
        
        <!-- Active Loans Tab -->
        <div v-if="activeTab === 'current'">
          <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <!-- Empty State -->
            <div v-if="activeLoans.length === 0" class="flex flex-col items-center justify-center py-12 px-4">
              <div class="relative mb-5">
                <div class="absolute inset-0 bg-blue-100 rounded-full opacity-30 scale-150"></div>
                <BookMarked class="h-16 w-16 md:h-20 md:w-20 text-blue-500 relative z-10" />
              </div>
              <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2 text-center">Anda tidak memiliki peminjaman aktif</h2>
              <p class="text-gray-500 text-center max-w-md mb-6">
                Silakan pinjam buku dari katalog perpustakaan untuk melihatnya di sini
              </p>
              <Link 
                :href="route('home')" 
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg font-semibold bg-blue-600 text-white hover:bg-blue-700"
              >
                <BookOpen class="w-5 h-5" />
                <span>Jelajahi Katalog</span>
              </Link>
            </div>
            
            <!-- Active Loans List -->
            <div v-else>
              <ul class="divide-y divide-gray-200">
                <li v-for="loan in activeLoans" :key="loan.id" class="p-4 md:p-5 hover:bg-gray-50">
                  <div class="flex flex-col md:flex-row gap-4 md:gap-5">
                    <!-- Book Cover -->
                    <div class="md:w-1/6 flex justify-center md:justify-start">
                      <div class="w-24 h-36 md:w-32 md:h-44 bg-gray-200 rounded-md overflow-hidden shadow-md">
                        <img 
                          v-if="loan.book_cover" 
                          :src="loan.book_cover" 
                          :alt="loan.book_title" 
                          class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                          <BookOpen class="w-10 h-10 md:w-12 md:h-12 text-gray-400" />
                        </div>
                      </div>
                    </div>
                    
                    <!-- Book Details -->
                    <div class="flex-1">
                      <h2 class="text-base md:text-lg font-semibold text-gray-900">{{ loan.book_title }}</h2>
                      <p class="text-gray-600 text-sm mb-2 md:mb-3">{{ loan.author }}</p>
                      
                      <!-- Status Badge -->
                      <div class="mb-2 md:mb-3">
                        <span 
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusBadge(loan.status, loan.is_overdue).bgColor + ' ' + getStatusBadge(loan.status, loan.is_overdue).textColor"
                        >
                          <component :is="getStatusBadge(loan.status, loan.is_overdue).icon" class="h-3 w-3 mr-1" />
                          {{ getStatusBadge(loan.status, loan.is_overdue).label }}
                        </span>
                      </div>
                      
                      <!-- Loan Information -->
                      <div class="grid grid-cols-1 gap-y-1.5 md:gap-y-2 text-sm">
                        <div>
                          <span class="text-gray-500">Tanggal Permintaan:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.request_date) }}</span>
                        </div>
                        
                        <div v-if="loan.approval_date">
                          <span class="text-gray-500">Tanggal Persetujuan:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.approval_date) }}</span>
                        </div>
                        
                        <div v-if="loan.due_date">
                          <span class="text-gray-500">Tenggat Pengembalian:</span>
                          <span 
                            :class="loan.is_overdue ? 'text-red-600 font-medium ml-1' : 'text-gray-900 ml-1'"
                          >
                            {{ formatDate(loan.due_date) }}
                            <span v-if="loan.is_overdue" class="text-red-600"> (Terlambat)</span>
                            <span 
                              v-else-if="loan.due_date && calculateDaysLeft(loan.due_date) <= 3" 
                              class="text-orange-500"
                            >
                              ({{ calculateDaysLeft(loan.due_date) }} hari lagi)
                            </span>
                          </span>
                        </div>
                      </div>
                      
                      <!-- Actions -->
                      <div class="mt-3 md:mt-4" v-if="loan.status === 'belum_diambil'">
                        <Link
                          :href="route('loans.cancel', { loan: loan.id })"
                          method="delete"
                          as="button"
                          type="button"
                          class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm rounded-md text-red-700 bg-white hover:bg-red-50"
                        >
                          Batalkan Permintaan
                        </Link>
                      </div>
                      
                      <!-- Overdue Warning -->
                      <div 
                        v-if="loan.is_overdue" 
                        class="mt-3 md:mt-4 p-2.5 md:p-3 bg-red-50 border border-red-200 rounded-md"
                      >
                        <div class="flex items-start gap-2">
                          <AlertTriangle class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" />
                          <div>
                            <p class="font-medium text-red-700 text-sm md:text-base">Buku Terlambat Dikembalikan</p>
                            <p class="text-xs md:text-sm text-red-600">
                              Harap segera kembalikan buku ke perpustakaan.
                            </p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        
        <!-- Loan History Tab -->
        <div v-if="activeTab === 'history'">
          <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <!-- Empty History State -->
            <div v-if="loanHistory.length === 0" class="flex flex-col items-center justify-center py-12 px-4">
              <div class="relative mb-5">
                <div class="absolute inset-0 bg-green-100 rounded-full opacity-30 scale-150"></div>
                <CheckCircle2 class="h-16 w-16 md:h-20 md:w-20 text-green-500 relative z-10" />
              </div>
              <h2 class="text-lg md:text-xl font-semibold text-gray-800 mb-2 text-center">Belum ada riwayat peminjaman</h2>
              <p class="text-gray-500 text-center max-w-md">
                Riwayat peminjaman buku yang telah dikembalikan akan muncul di sini
              </p>
            </div>
            
            <!-- History List -->
            <div v-else>
              <ul class="divide-y divide-gray-200">
                <li v-for="loan in loanHistory" :key="loan.id" class="p-4 md:p-5 hover:bg-gray-50">
                  <div class="flex flex-col md:flex-row gap-4 md:gap-5">
                    <!-- Book Cover -->
                    <div class="md:w-1/6 flex justify-center md:justify-start">
                      <div class="w-24 h-36 md:w-32 md:h-44 bg-gray-200 rounded-md overflow-hidden shadow-md">
                        <img 
                          v-if="loan.book_cover" 
                          :src="loan.book_cover" 
                          :alt="loan.book_title" 
                          class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                          <BookOpen class="w-10 h-10 md:w-12 md:h-12 text-gray-400" />
                        </div>
                      </div>
                    </div>
                    
                    <!-- Book Details -->
                    <div class="flex-1">
                      <h2 class="text-base md:text-lg font-semibold text-gray-900">{{ loan.book_title }}</h2>
                      <p class="text-gray-600 text-sm mb-2 md:mb-3">{{ loan.author }}</p>
                      
                      <!-- Status Badge -->
                      <div class="mb-2 md:mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          <CheckCircle2 class="h-3 w-3 mr-1" />
                          Dikembalikan
                        </span>
                      </div>
                      
                      <!-- Loan Information -->
                      <div class="grid grid-cols-1 gap-y-1.5 md:gap-y-2 text-sm">
                        <div>
                          <span class="text-gray-500">Tanggal Permintaan:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.request_date) }}</span>
                        </div>
                        
                        <div v-if="loan.approval_date">
                          <span class="text-gray-500">Tanggal Persetujuan:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.approval_date) }}</span>
                        </div>
                        
                        <div v-if="loan.due_date">
                          <span class="text-gray-500">Tenggat Pengembalian:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.due_date) }}</span>
                        </div>
                        
                        <div v-if="loan.return_date">
                          <span class="text-gray-500">Tanggal Pengembalian:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.return_date) }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
