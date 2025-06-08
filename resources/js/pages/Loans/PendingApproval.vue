<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
  BookOpen, 
  Clock, 
  CheckCircle2, 
  Calendar,
  User
} from 'lucide-vue-next';
import { ref } from 'vue';
import Pagination from '@/components/Pagination.vue';
import dayjs from 'dayjs';

defineProps<{
  loans: {
    data: Array<{
      id: number;
      book_id: number;
      status: string;
      request_date: string;
      book_title: string;
      username: string;
    }>;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
  };
}>();

// Breadcrumbs for the layout
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Permintaan Peminjaman',
    href: route('loans.pending'),
  },
];

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

// State untuk form approval
const approvingLoanId = ref<number | null>(null);
const showApprovalModal = ref(false);

// Default due date (7 hari dari sekarang)
const defaultDueDate = dayjs().add(7, 'day').format('YYYY-MM-DD');
const selectedDueDate = ref(defaultDueDate);

// Form untuk approval
const approvalForm = useForm({
  due_date: defaultDueDate
});

// Buka modal approval
const openApprovalModal = (loanId: number) => {
  approvingLoanId.value = loanId;
  selectedDueDate.value = defaultDueDate;
  approvalForm.due_date = defaultDueDate;
  showApprovalModal.value = true;
};

// Tutup modal approval
const closeApprovalModal = () => {
  showApprovalModal.value = false;
  approvingLoanId.value = null;
};

// Submit approval
const submitApproval = () => {
  approvalForm.post(route('loans.approve', { loan: approvingLoanId.value }), {
    onSuccess: () => {
      closeApprovalModal();
    }
  });
};

// Form untuk rejection
const rejectionForm = useForm({});

// Reject request
const rejectRequest = (loanId: number) => {
  if (confirm('Apakah Anda yakin ingin menolak permintaan peminjaman ini?')) {
    rejectionForm.delete(route('loans.reject', { loan: loanId }));
  }
};
</script>

<template>
  <Head title="Permintaan Peminjaman - E-Library SMADA" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Permintaan Peminjaman</h1>
          <p class="text-gray-600 mt-1">Daftar buku yang diminta untuk dipinjam dan menunggu persetujuan.</p>
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
            :href="route('loans.active')" 
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50"
          >
            Peminjaman Aktif
          </Link>
        </div>
      </div>

      <!-- Daftar Permintaan Peminjaman -->
      <div class="bg-white rounded-xl border border-sidebar-border/70 shadow-sm overflow-hidden">
        <div v-if="loans.data.length === 0" class="py-12 px-4 text-center">
          <Clock class="w-16 h-16 mx-auto text-gray-400 mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak Ada Permintaan Peminjaman</h3>
          <p class="text-gray-600 max-w-md mx-auto">
            Saat ini tidak ada permintaan peminjaman buku yang perlu disetujui.
          </p>
        </div>

        <div v-else>
          <!-- Table Header -->
          <div class="hidden md:flex bg-gray-50 border-b border-gray-200 px-6 py-3">
            <div class="w-1/4 font-medium text-gray-500 text-sm">Buku</div>
            <div class="w-1/4 font-medium text-gray-500 text-sm">Peminjam</div>
            <div class="w-1/4 font-medium text-gray-500 text-sm">Tanggal Permintaan</div>
            <div class="w-1/4 font-medium text-gray-500 text-sm text-right">Aksi</div>
          </div>
          
          <!-- Table Body -->
          <ul class="divide-y divide-gray-200">
            <li v-for="loan in loans.data" :key="loan.id" class="px-6 py-4">
              <div class="md:flex md:items-center">
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="flex items-start gap-3">
                    <BookOpen class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                    <div>
                      <p class="font-medium text-gray-900">{{ loan.book_title }}</p>
                      <p class="text-sm text-gray-500">ID: {{ loan.book_id }}</p>
                    </div>
                  </div>
                </div>
                
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="flex items-start gap-3">
                    <User class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                    <p class="text-gray-900">{{ loan.username }}</p>
                  </div>
                </div>
                
                <div class="w-full md:w-1/4 mb-2 md:mb-0">
                  <div class="flex items-start gap-3">
                    <Clock class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" />
                    <p class="text-gray-900">{{ formatDate(loan.request_date) }}</p>
                  </div>
                </div>
                
                <div class="w-full md:w-1/4 flex justify-end space-x-2">
                  <button
                    @click="openApprovalModal(loan.id)"
                    class="inline-flex items-center px-3 py-1.5 border border-green-600 text-sm rounded-md text-green-700 bg-white hover:bg-green-50"
                  >
                    <CheckCircle2 class="h-4 w-4 mr-1" />
                    Setujui
                  </button>
                  <button
                    @click="rejectRequest(loan.id)"
                    class="inline-flex items-center px-3 py-1.5 border border-red-300 text-sm rounded-md text-red-700 bg-white hover:bg-red-50"
                  >
                    Tolak
                  </button>
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
  
  <!-- Modal Approval -->
  <div v-if="showApprovalModal" class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center">
      <div class="fixed inset-0 transition-opacity" aria-hidden="true">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
      </div>
      
      <div class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-medium text-gray-900">Setujui Peminjaman</h3>
          <button @click="closeApprovalModal" class="text-gray-400 hover:text-gray-500">
            <span class="sr-only">Close</span>
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
        
        <form @submit.prevent="submitApproval">
          <div class="mb-4">
            <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
            <div class="flex items-center">
              <Calendar class="h-5 w-5 text-gray-400 mr-2" />
              <input
                id="due_date"
                v-model="approvalForm.due_date"
                type="date"
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                :min="dayjs().add(1, 'day').format('YYYY-MM-DD')"
                required
              />
            </div>
            <p v-if="approvalForm.errors.due_date" class="mt-1 text-sm text-red-600">{{ approvalForm.errors.due_date }}</p>
            <p class="text-sm text-gray-500 mt-1">
              Pilih tanggal jatuh tempo pengembalian buku. Minimal 1 hari dari sekarang.
            </p>
          </div>
          
          <div class="mt-5 flex justify-end space-x-3">
            <button
              type="button"
              @click="closeApprovalModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              Batal
            </button>
            <button
              type="submit"
              :disabled="approvalForm.processing"
              class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <div v-if="approvalForm.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Memproses...
              </div>
              <span v-else>Setujui dan Serahkan Buku</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template> 