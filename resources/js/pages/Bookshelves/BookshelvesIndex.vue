<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { BookOpen, BookMarked, CheckCircle2, AlertTriangle, Clock, Calendar } from 'lucide-vue-next';
import { ref } from 'vue';

// Props
const props = defineProps<{
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
</script>

<template>
  <Head title="Rak Buku - E-Library SMADA" />

  <div class="min-h-screen bg-gray-50">
    <!-- Main Content - Rak Buku -->
    <main class="py-8">
      <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="mb-8">
          <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Rak Buku Saya</h1>
          <p class="text-gray-600 mt-1">Lihat peminjaman dan riwayat peminjaman buku</p>
        </div>
        
        <!-- Tabs Navigation -->
        <div class="flex border-b border-gray-200 mb-6">
          <button 
            @click="activeTab = 'current'" 
            class="px-4 py-3 font-medium text-sm flex items-center gap-2"
            :class="activeTab === 'current' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-800'"
          >
            <BookOpen class="w-4 h-4" />
            Peminjaman Aktif
          </button>
          <button 
            @click="activeTab = 'history'" 
            class="px-4 py-3 font-medium text-sm flex items-center gap-2"
            :class="activeTab === 'history' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-600 hover:text-gray-800'"
          >
            <CheckCircle2 class="w-4 h-4" />
            Riwayat Peminjaman
          </button>
        </div>
        
        <!-- Active Loans Tab -->
        <div v-if="activeTab === 'current'">
          <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <!-- Empty State -->
            <div v-if="activeLoans.length === 0" class="flex flex-col items-center justify-center py-16 px-4">
              <div class="relative mb-6">
                <div class="absolute inset-0 bg-blue-100 rounded-full opacity-30 scale-150"></div>
                <BookMarked class="h-20 w-20 text-blue-500 relative z-10" />
              </div>
              <h2 class="text-xl font-semibold text-gray-800 mb-2">Anda tidak memiliki peminjaman aktif</h2>
              <p class="text-gray-500 text-center max-w-md mb-8">
                Silakan pinjam buku dari katalog perpustakaan untuk melihatnya di sini
              </p>
              <Link 
                :href="route('home')" 
                class="inline-flex items-center gap-2 px-6 py-3 rounded-lg font-semibold bg-blue-600 text-white hover:bg-blue-700"
              >
                <BookOpen class="w-5 h-5" />
                <span>Jelajahi Katalog</span>
              </Link>
            </div>
            
            <!-- Active Loans List -->
            <div v-else>
              <ul class="divide-y divide-gray-200">
                <li v-for="loan in activeLoans" :key="loan.id" class="p-5 hover:bg-gray-50">
                  <div class="flex flex-col md:flex-row gap-5">
                    <!-- Book Cover -->
                    <div class="w-full md:w-1/6 flex justify-center md:justify-start">
                      <div class="w-32 h-44 bg-gray-200 rounded-md overflow-hidden shadow-md">
                        <img 
                          v-if="loan.book_cover" 
                          :src="loan.book_cover" 
                          :alt="loan.book_title" 
                          class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                          <BookOpen class="w-12 h-12 text-gray-400" />
                        </div>
                      </div>
                    </div>
                    
                    <!-- Book Details -->
                    <div class="flex-1">
                      <h2 class="text-lg font-semibold text-gray-900">{{ loan.book_title }}</h2>
                      <p class="text-gray-600 text-sm mb-3">{{ loan.author }}</p>
                      
                      <!-- Status Badge -->
                      <div class="mb-3">
                        <span 
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusBadge(loan.status, loan.is_overdue).bgColor + ' ' + getStatusBadge(loan.status, loan.is_overdue).textColor"
                        >
                          <component :is="getStatusBadge(loan.status, loan.is_overdue).icon" class="h-3 w-3 mr-1" />
                          {{ getStatusBadge(loan.status, loan.is_overdue).label }}
                        </span>
                      </div>
                      
                      <!-- Loan Information -->
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-sm">
                        <div>
                          <span class="text-gray-500">Tanggal Permintaan:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.request_date) }}</span>
                        </div>
                        
                        <div v-if="loan.approval_date">
                          <span class="text-gray-500">Tanggal Persetujuan:</span>
                          <span class="text-gray-900 ml-1">{{ formatDate(loan.approval_date) }}</span>
                        </div>
                        
                        <div v-if="loan.due_date" class="col-span-2">
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
                      <div class="mt-4" v-if="loan.status === 'belum_diambil'">
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
                        class="mt-4 p-3 bg-red-50 border border-red-200 rounded-md"
                      >
                        <div class="flex items-start gap-2">
                          <AlertTriangle class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" />
                          <div>
                            <p class="font-medium text-red-700">Buku Terlambat Dikembalikan</p>
                            <p class="text-sm text-red-600">
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
            <div v-if="loanHistory.length === 0" class="flex flex-col items-center justify-center py-16 px-4">
              <div class="relative mb-6">
                <div class="absolute inset-0 bg-green-100 rounded-full opacity-30 scale-150"></div>
                <CheckCircle2 class="h-20 w-20 text-green-500 relative z-10" />
              </div>
              <h2 class="text-xl font-semibold text-gray-800 mb-2">Belum ada riwayat peminjaman</h2>
              <p class="text-gray-500 text-center max-w-md">
                Riwayat peminjaman buku yang telah dikembalikan akan muncul di sini
              </p>
            </div>
            
            <!-- History List -->
            <div v-else>
              <ul class="divide-y divide-gray-200">
                <li v-for="loan in loanHistory" :key="loan.id" class="p-5 hover:bg-gray-50">
                  <div class="flex flex-col md:flex-row gap-5">
                    <!-- Book Cover -->
                    <div class="w-full md:w-1/6 flex justify-center md:justify-start">
                      <div class="w-32 h-44 bg-gray-200 rounded-md overflow-hidden shadow-md">
                        <img 
                          v-if="loan.book_cover" 
                          :src="loan.book_cover" 
                          :alt="loan.book_title" 
                          class="w-full h-full object-cover"
                        />
                        <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                          <BookOpen class="w-12 h-12 text-gray-400" />
                        </div>
                      </div>
                    </div>
                    
                    <!-- Book Details -->
                    <div class="flex-1">
                      <h2 class="text-lg font-semibold text-gray-900">{{ loan.book_title }}</h2>
                      <p class="text-gray-600 text-sm mb-3">{{ loan.author }}</p>
                      
                      <!-- Status Badge -->
                      <div class="mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                          <CheckCircle2 class="h-3 w-3 mr-1" />
                          Dikembalikan
                        </span>
                      </div>
                      
                      <!-- Loan Information -->
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-sm">
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