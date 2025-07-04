<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { 
  BookOpen, 
  Clock, 
  Calendar,
  User,
  AlertTriangle,
  CornerDownLeft,
  Search,
  X
} from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { ref, watch } from 'vue';
import { Input } from '@/components/ui/input';
import { debounce } from 'lodash';

const props = defineProps<{
  loans: {
    data: Array<{
      id: number;
      book_id: number;
      status: string;
      request_date: string;
      approval_date: string;
      due_date: string;
      book_title: string;
      username: string;
      is_overdue: boolean;
    }>;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
    current_page: number;
    last_page: number;
    total: number;
  };
  filters: {
    search: string;
  }
}>();

const search = ref('');

// Mengisi nilai awal pencarian dari filter
search.value = props.filters.search || '';

// Debounce pencarian
const debouncedSearch = debounce(() => {
  router.get(route('loans.active'), { search: search.value }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
}, 500);

// Watch perubahan pada search
watch(search, () => {
  debouncedSearch();
});

// Fungsi untuk menghapus pencarian
const clearSearch = () => {
  search.value = '';
  router.get(route('loans.active'), {}, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
};

// Breadcrumbs for the layout
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Sirkulasi Layanan',
    href: route('loans.active'),
  },
];

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
    case 'dipinjam':
      return {
        label: 'Sedang Dipinjam',
        bgColor: 'bg-yellow-100',
        textColor: 'text-yellow-800',
        icon: BookOpen
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

// We're only using formatShortDate in this component

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

// Form untuk mengembalikan buku
const returnForm = useForm({});

// Proses pengembalian buku
const processReturn = (loanId: number) => {
  if (confirm('Apakah Anda yakin ingin memproses pengembalian buku ini?')) {
    returnForm.post(route('loans.return', { loan: loanId }));
  }
};
</script>

<template>
  <Head title="Sirkulasi Layanan - E-Library SMADA" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Sirkulasi Layanan</h1>
          <p class="text-gray-600 mt-1">Daftar buku yang sedang dipinjam oleh anggota perpustakaan.</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('loans.create')" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-blue-600 rounded-md font-medium text-white hover:bg-blue-700"
          >
            Buat Peminjaman
          </Link>
          <Link
            :href="route('loans.index')" 
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50"
          >
            Semua Peminjaman
          </Link>
          <Link
            :href="route('loans.pending')" 
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50"
          >
            Permintaan Peminjaman
          </Link>
        </div>
      </div>

      <!-- Komponen Pencarian -->
      <div class="mb-4 relative">
        <div class="flex">
          <div class="relative flex-1">
            <Input 
              v-model="search"
              placeholder="Cari buku dipinjam berdasarkan judul buku atau nama peminjam..."
              class="pl-10 pr-10"
            />
            <div class="absolute left-0 top-0 h-full flex items-center pl-3">
              <Search class="h-4 w-4 text-gray-400" />
            </div>
            <button 
              v-if="search"
              @click="clearSearch"
              class="absolute right-0 top-0 h-full flex items-center pr-3"
            >
              <X class="h-4 w-4 text-gray-400" />
            </button>
          </div>
        </div>
        <div v-if="loans.total > 0" class="text-sm text-gray-500 mt-2">
          Menampilkan {{ loans.total }} hasil pencarian
        </div>
      </div>

      <!-- Daftar Buku Dipinjam -->
      <div class="bg-white rounded-xl border border-sidebar-border/70 shadow-sm overflow-hidden">
        <div v-if="loans.data.length === 0" class="py-12 px-4 text-center">
          <BookOpen class="w-16 h-16 mx-auto text-gray-400 mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Buku yang Sedang Dipinjam</h3>
          <p class="text-gray-600 max-w-md mx-auto">
            Saat ini tidak ada buku yang sedang dipinjam oleh anggota perpustakaan.
          </p>
        </div>

        <div v-else>
          <!-- Table Body -->
          <ul class="divide-y divide-gray-200">
            <li v-for="loan in loans.data" :key="loan.id" class="p-4 md:p-6">
              <div class="md:flex md:items-start">
                <!-- Book and Status Info -->
                <div class="md:w-1/3 mb-4 md:mb-0 md:pr-6">
                  <div class="flex items-start gap-3">
                    <BookOpen class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                    <div>
                      <h3 class="font-medium text-gray-900">{{ loan.book_title }}</h3>
                      <p class="text-sm text-gray-500 mt-1">ID Buku: {{ loan.book_id }}</p>
                      
                      <div class="flex items-center mt-2">
                        <span 
                          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                          :class="getStatusBadge(loan.status, loan.is_overdue).bgColor + ' ' + getStatusBadge(loan.status, loan.is_overdue).textColor"
                        >
                          <component :is="getStatusBadge(loan.status, loan.is_overdue).icon" class="h-3 w-3 mr-1" />
                          {{ getStatusBadge(loan.status, loan.is_overdue).label }}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Borrower and Date Info -->
                <div class="md:w-1/3 mb-4 md:mb-0">
                  <div class="space-y-3">
                    <div class="flex items-start gap-3">
                      <User class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Peminjam</span>
                        <p class="text-gray-900">{{ loan.username }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                      <Clock class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Tanggal Dipinjam</span>
                        <p class="text-gray-900">{{ formatShortDate(loan.approval_date) }}</p>
                      </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                      <Calendar class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                      <div>
                        <span class="text-sm text-gray-500">Tenggat Pengembalian</span>
                        <p :class="{'text-gray-900': !loan.is_overdue, 'text-red-600 font-medium': loan.is_overdue}">
                          {{ formatShortDate(loan.due_date) }}
                          <span v-if="loan.is_overdue" class="text-red-600">(Terlambat)</span>
                          <span v-else-if="calculateDaysLeft(loan.due_date) <= 3" class="text-orange-500">
                            ({{ calculateDaysLeft(loan.due_date) }} hari lagi)
                          </span>
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="md:w-1/3 flex md:justify-end">
                  <button
                    @click="processReturn(loan.id)"
                    class="inline-flex items-center px-4 py-2 border border-green-600 text-sm rounded-md text-green-700 bg-white hover:bg-green-50"
                  >
                    <CornerDownLeft class="h-4 w-4 mr-1" />
                    Kembalikan Buku
                  </button>
                </div>
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
                    <p class="text-sm text-red-600 mt-0.5">
                      Peminjaman ini sudah melewati tenggat pengembalian. Harap segera diproses.
                    </p>
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
  </AppLayout>
</template> 