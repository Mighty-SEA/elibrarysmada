<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
  BookOpen, 
  Clock, 
  CheckCircle2, 
  AlertTriangle, 
  Filter,
  Search,
  X
} from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { ref, watch } from 'vue';
import { debounce } from 'lodash';
import { Input } from '@/components/ui/input';

const props = defineProps<{
  loans: {
    data: Array<{
      id: number;
      book_id: number;
      status: string;
      request_date: string;
      approval_date: string | null;
      due_date: string | null;
      return_date: string | null;
      book_title: string;
      username: string;
      approver_name: string | null;
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
  activeStatus: string;
  filters: {
    search: string;
  }
}>();

const search = ref('');

// Mengisi nilai awal pencarian dari filter
search.value = props.filters.search || '';

// Debounce pencarian
const debouncedSearch = debounce(() => {
  router.get(route('loans.index', { status: props.activeStatus }), { search: search.value }, {
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
  router.get(route('loans.index', { status: props.activeStatus }), {}, {
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
    title: 'Peminjaman Buku',
    href: route('loans.index'),
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
</script>

<template>
  <Head title="Peminjaman Buku - E-Library SMADA" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Peminjaman Buku</h1>
          <p class="text-gray-600 mt-1">Daftar semua peminjaman buku di perpustakaan.</p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('loans.create')" 
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-blue-600 rounded-md font-medium text-white hover:bg-blue-700"
          >
            Buat Peminjaman
          </Link>
          <Link
            :href="route('loans.pending')" 
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50"
          >
            Permintaan Peminjaman
          </Link>
          <Link
            :href="route('loans.active')" 
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50"
          >
            Peminjaman Aktif
          </Link>
        </div>
      </div>

      <!-- Komponen Pencarian -->
      <div class="mb-4 relative">
        <div class="flex">
          <div class="relative flex-1">
            <Input 
              v-model="search"
              placeholder="Cari peminjaman berdasarkan judul buku, nama peminjam, atau admin..."
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

      <!-- Filter by Status -->
      <div class="mb-6 bg-white rounded-xl p-4 border border-sidebar-border/70 shadow-sm">
        <div class="flex flex-wrap items-center gap-3">
          <div class="flex items-center text-gray-700">
            <Filter class="w-5 h-5 mr-2" />
            <span class="font-medium">Filter Status:</span>
          </div>
          
          <div class="flex flex-wrap gap-2 mt-2 sm:mt-0">
            <Link
              :href="route('loans.index', { status: 'all', search })"
              class="px-3 py-1.5 text-sm rounded-md"
              :class="activeStatus === 'all' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
            >
              Semua
            </Link>
            <Link
              :href="route('loans.index', { status: 'belum_diambil', search })"
              class="px-3 py-1.5 text-sm rounded-md"
              :class="activeStatus === 'belum_diambil' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
            >
              Menunggu Diambil
            </Link>
            <Link
              :href="route('loans.index', { status: 'dipinjam', search })"
              class="px-3 py-1.5 text-sm rounded-md"
              :class="activeStatus === 'dipinjam' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
            >
              Sedang Dipinjam
            </Link>
            <Link
              :href="route('loans.index', { status: 'terlambat', search })"
              class="px-3 py-1.5 text-sm rounded-md"
              :class="activeStatus === 'terlambat' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
            >
              Terlambat
            </Link>
            <Link
              :href="route('loans.index', { status: 'dikembalikan', search })"
              class="px-3 py-1.5 text-sm rounded-md"
              :class="activeStatus === 'dikembalikan' ? 'bg-blue-100 text-blue-800 font-medium' : 'bg-gray-100 text-gray-800 hover:bg-gray-200'"
            >
              Dikembalikan
            </Link>
          </div>
        </div>
      </div>

      <!-- Daftar Peminjaman -->
      <div class="bg-white rounded-xl border border-sidebar-border/70 shadow-sm overflow-hidden">
        <div v-if="loans.data.length === 0" class="py-12 px-4 text-center">
          <BookOpen class="w-16 h-16 mx-auto text-gray-400 mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Data Peminjaman</h3>
          <p class="text-gray-600 max-w-md mx-auto">
            Tidak ada data peminjaman yang sesuai dengan filter yang dipilih.
          </p>
        </div>

        <div v-else>
          <!-- Table Header -->
          <div class="hidden md:grid md:grid-cols-5 bg-gray-50 border-b border-gray-200 px-6 py-3">
            <div class="font-medium text-gray-500 text-sm">Buku</div>
            <div class="font-medium text-gray-500 text-sm">Peminjam</div>
            <div class="font-medium text-gray-500 text-sm">Tanggal</div>
            <div class="font-medium text-gray-500 text-sm">Status</div>
            <div class="font-medium text-gray-500 text-sm">Admin</div>
          </div>
          
          <!-- Table Body -->
          <ul class="divide-y divide-gray-200">
            <li v-for="loan in loans.data" :key="loan.id" class="px-6 py-4">
              <div class="md:grid md:grid-cols-5 md:gap-4">
                <!-- Book -->
                <div class="mb-2 md:mb-0">
                  <p class="font-medium text-gray-900">{{ loan.book_title }}</p>
                  <p class="text-sm text-gray-500">ID: {{ loan.book_id }}</p>
                </div>
                
                <!-- Borrower -->
                <div class="mb-2 md:mb-0">
                  <p class="text-gray-900">{{ loan.username }}</p>
                </div>
                
                <!-- Date Info -->
                <div class="mb-2 md:mb-0">
                  <div class="space-y-1">
                    <div class="text-sm">
                      <span class="text-gray-500">Permintaan:</span>
                      <span class="text-gray-900">{{ formatDate(loan.request_date) }}</span>
                    </div>
                    
                    <div v-if="loan.approval_date" class="text-sm">
                      <span class="text-gray-500">Disetujui:</span>
                      <span class="text-gray-900">{{ formatDate(loan.approval_date) }}</span>
                    </div>
                    
                    <div v-if="loan.return_date" class="text-sm">
                      <span class="text-gray-500">Dikembalikan:</span>
                      <span class="text-gray-900">{{ formatDate(loan.return_date) }}</span>
                    </div>
                  </div>
                </div>
                
                <!-- Status -->
                <div class="mb-2 md:mb-0">
                  <span 
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                    :class="getStatusBadge(loan.status, loan.is_overdue).bgColor + ' ' + getStatusBadge(loan.status, loan.is_overdue).textColor"
                  >
                    <component :is="getStatusBadge(loan.status, loan.is_overdue).icon" class="h-3 w-3 mr-1" />
                    {{ getStatusBadge(loan.status, loan.is_overdue).label }}
                  </span>
                </div>
                
                <!-- Admin -->
                <div>
                  <p class="text-gray-900">{{ loan.approver_name || '-' }}</p>
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