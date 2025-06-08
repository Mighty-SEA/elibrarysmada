<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { 
  BookOpen, 
  ChevronLeft, 
  Clock, 
  CheckCircle2, 
  AlertTriangle, 
  BookX,
  Calendar
} from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';

defineProps<{
  loans: {
    data: Array<{
      id: number;
      book_id: number;
      status: string;
      request_date: string;
      approval_date: string | null;
      due_date: string | null;
      return_date: string | null;
      fine_amount: number | null;
      book_title: string;
      book_cover: string;
      is_overdue: boolean;
    }>;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
  };
}>();

// Computed untuk mendapatkan status badge
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
    default:
      return {
        label: 'Status Tidak Diketahui',
        bgColor: 'bg-gray-100',
        textColor: 'text-gray-800',
        icon: BookX
      };
  }
};

// Format tanggal
const formatDate = (dateString: string | null) => {
  if (!dateString) return '-';
  
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  };
  
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Format tanggal singkat (tanpa jam)
const formatShortDate = (dateString: string | null) => {
  if (!dateString) return '-';
  
  const options: Intl.DateTimeFormatOptions = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric'
  };
  
  return new Date(dateString).toLocaleDateString('id-ID', options);
};

// Format denda
const formatMoney = (amount: number | null) => {
  if (amount === null) return 'Rp 0';
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(amount);
};
</script>

<template>
  <Head title="Riwayat Peminjaman - E-Library SMADA" />

  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Breadcrumb dan Judul -->
    <div class="bg-white shadow-sm border-b">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center gap-2 text-sm text-gray-500">
          <Link :href="route('home')" class="hover:text-blue-600">Beranda</Link>
          <span>/</span>
          <span class="text-gray-900 font-medium">Riwayat Peminjaman</span>
        </div>
      </div>
    </div>

    <div class="flex-grow container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Riwayat Peminjaman</h1>
          <p class="text-gray-600 mt-1">Daftar buku yang Anda pinjam, baik yang sudah dikembalikan maupun masih dalam proses.</p>
        </div>
        <Link
          :href="route('home')" 
          class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50"
        >
          <ChevronLeft class="w-4 h-4 mr-1" />
          Kembali ke Katalog
        </Link>
      </div>

      <!-- Daftar Peminjaman -->
      <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
        <div v-if="loans.data.length === 0" class="py-12 px-4 text-center">
          <BookX class="w-16 h-16 mx-auto text-gray-400 mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-1">Belum Ada Peminjaman</h3>
          <p class="text-gray-600 max-w-md mx-auto">
            Anda belum meminjam buku apapun. Silakan cari dan pinjam buku di katalog perpustakaan.
          </p>
          <Link
            :href="route('home')"
            class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700"
          >
            Lihat Katalog Buku
          </Link>
        </div>

        <div v-else>
          <ul class="divide-y divide-gray-200">
            <li v-for="loan in loans.data" :key="loan.id" class="p-4 md:p-6 flex flex-col md:flex-row gap-4">
              <!-- Cover Image -->
              <div class="flex-shrink-0 md:w-32">
                <div class="aspect-[2/3] bg-gray-100 border border-gray-200 rounded-md overflow-hidden">
                  <img 
                    :src="loan.book_cover" 
                    :alt="loan.book_title"
                    class="w-full h-full object-cover"
                    onerror="this.src='/images/book-placeholder.svg'; this.onerror=null;"
                  />
                </div>
              </div>
              
              <!-- Loan Details -->
              <div class="flex-grow flex flex-col">
                <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-2">
                  <!-- Book Title and Status -->
                  <div>
                    <h3 class="text-lg font-medium text-gray-900">{{ loan.book_title }}</h3>
                    <div class="mt-1 flex items-center">
                      <span 
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                        :class="getStatusBadge(loan.status, loan.is_overdue).bgColor + ' ' + getStatusBadge(loan.status, loan.is_overdue).textColor"
                      >
                        <component :is="getStatusBadge(loan.status, loan.is_overdue).icon" class="h-3 w-3 mr-1" />
                        {{ getStatusBadge(loan.status, loan.is_overdue).label }}
                      </span>
                    </div>
                  </div>
                  
                  <!-- Action Buttons -->
                  <div v-if="loan.status === 'belum_diambil'" class="mt-2 md:mt-0">
                    <Link
                      :href="route('loans.cancel', { loan: loan.id })"
                      method="delete"
                      as="button"
                      class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm rounded-md text-red-700 bg-white hover:bg-red-50"
                    >
                      Batalkan
                    </Link>
                  </div>
                </div>
                
                <!-- Loan Dates Info -->
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                  <div class="flex items-start gap-2">
                    <Clock class="h-4 w-4 text-gray-500 mt-0.5" />
                    <div>
                      <span class="text-gray-500">Tanggal Permintaan:</span>
                      <p class="text-gray-900">{{ formatDate(loan.request_date) }}</p>
                    </div>
                  </div>
                  
                  <div class="flex items-start gap-2">
                    <Calendar class="h-4 w-4 text-gray-500 mt-0.5" />
                    <div>
                      <span class="text-gray-500">Tanggal Diambil:</span>
                      <p class="text-gray-900">{{ formatDate(loan.approval_date) }}</p>
                    </div>
                  </div>
                  
                  <div v-if="loan.due_date" class="flex items-start gap-2">
                    <Calendar class="h-4 w-4 text-gray-500 mt-0.5" />
                    <div>
                      <span class="text-gray-500">Tenggat Pengembalian:</span>
                      <p :class="{'text-gray-900': !loan.is_overdue, 'text-red-600 font-medium': loan.is_overdue}">
                        {{ formatShortDate(loan.due_date) }}
                        <span v-if="loan.is_overdue" class="text-red-600">(Terlambat)</span>
                      </p>
                    </div>
                  </div>
                  
                  <div v-if="loan.return_date" class="flex items-start gap-2">
                    <CheckCircle2 class="h-4 w-4 text-gray-500 mt-0.5" />
                    <div>
                      <span class="text-gray-500">Tanggal Dikembalikan:</span>
                      <p class="text-gray-900">{{ formatDate(loan.return_date) }}</p>
                    </div>
                  </div>
                  
                  <div v-if="loan.fine_amount !== null && loan.fine_amount > 0" class="flex items-start gap-2 md:col-span-2">
                    <AlertTriangle class="h-4 w-4 text-red-500 mt-0.5" />
                    <div>
                      <span class="text-gray-500">Denda:</span>
                      <p class="text-red-600 font-medium">{{ formatMoney(loan.fine_amount) }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
          
          <!-- Pagination -->
          <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            <Pagination :links="loans.links" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template> 