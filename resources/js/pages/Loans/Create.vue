<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { BookOpen, User, Clock, Search } from 'lucide-vue-next';

const props = defineProps<{
  users: Array<{
    id: number;
    name: string;
    username: string;
    role: string;
  }>;
  books: Array<{
    id: number;
    judul: string;
    penulis: string;
    ketersediaan: number;
  }>;
}>();

// Breadcrumbs for the layout
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: route('dashboard'),
  },
  {
    title: 'Manajemen Peminjaman',
    href: route('loans.index'),
  },
  {
    title: 'Buat Peminjaman Baru',
    href: route('loans.create'),
  },
];

// Form untuk pembuatan peminjaman
const form = useForm({
  user_id: '',
  book_id: '',
  due_date: '',
});

// Search and filter
const userSearchQuery = ref('');
const bookSearchQuery = ref('');

// Computed untuk filtered users
const filteredUsers = () => {
  if (!userSearchQuery.value) return props.users;
  
  const query = userSearchQuery.value.toLowerCase();
  return props.users.filter(user => 
    user.name.toLowerCase().includes(query) || 
    user.username.toLowerCase().includes(query)
  );
};

// Computed untuk filtered books
const filteredBooks = () => {
  if (!bookSearchQuery.value) return props.books;
  
  const query = bookSearchQuery.value.toLowerCase();
  return props.books.filter(book => 
    book.judul.toLowerCase().includes(query) || 
    (book.penulis && book.penulis.toLowerCase().includes(query))
  );
};

// Reset form
const resetForm = () => {
  form.reset();
  userSearchQuery.value = '';
  bookSearchQuery.value = '';
};

// Submit form
const submitForm = () => {
  form.post(route('loans.store'), {
    onSuccess: () => {
      resetForm();
    },
  });
};

// Get minimum due date (tomorrow)
const getMinDueDate = () => {
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  const year = tomorrow.getFullYear();
  const month = (tomorrow.getMonth() + 1).toString().padStart(2, '0');
  const day = tomorrow.getDate().toString().padStart(2, '0');
  return `${year}-${month}-${day}`;
};

// Get role in Indonesian
const getRoleInIndonesian = (role: string) => {
  switch (role) {
    case 'murid':
      return 'Murid';
    case 'guru':
      return 'Guru';
    default:
      return role;
  }
};
</script>

<template>
  <Head title="Buat Peminjaman Baru - E-Library SMADA" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 p-4">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Buat Peminjaman Baru</h1>
          <p class="text-gray-600 mt-1">Buat peminjaman buku untuk pengguna secara langsung.</p>
        </div>
        <div>
          <Link
            :href="route('loans.index')" 
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50"
          >
            Kembali
          </Link>
        </div>
      </div>

      <div class="bg-white rounded-xl border border-sidebar-border/70 shadow-sm overflow-hidden p-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          <!-- User Selection -->
          <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
              <User class="w-5 h-5 mr-2 text-blue-600" />
              Pilih Pengguna
            </h2>
            
            <div class="mb-4">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  v-model="userSearchQuery"
                  type="text"
                  placeholder="Cari pengguna berdasarkan nama atau username..."
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
              <p class="text-sm text-gray-500 mt-1">
                Menampilkan {{ filteredUsers().length }} dari {{ props.users.length }} pengguna.
              </p>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
              <div class="max-h-64 overflow-y-auto">
                <div v-if="filteredUsers().length === 0" class="p-4 text-gray-500 text-center">
                  Tidak ada pengguna yang ditemukan.
                </div>
                <div v-else>
                  <div class="grid grid-cols-1 divide-y divide-gray-200">
                    <label 
                      v-for="user in filteredUsers()" 
                      :key="user.id"
                      class="flex items-center p-3 cursor-pointer hover:bg-gray-50"
                      :class="{ 'bg-blue-50': form.user_id === user.id.toString() }"
                    >
                      <input
                        type="radio"
                        :value="user.id"
                        v-model="form.user_id"
                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                      />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                        <div class="flex items-center text-xs text-gray-500 mt-1 space-x-2">
                          <span>{{ user.username }}</span>
                          <span>•</span>
                          <span class="px-1.5 py-0.5 bg-gray-100 rounded-full">{{ getRoleInIndonesian(user.role) }}</span>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="form.errors.user_id" class="text-red-600 text-sm mt-1">
              {{ form.errors.user_id }}
            </div>
          </div>
          
          <!-- Book Selection -->
          <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
              <BookOpen class="w-5 h-5 mr-2 text-blue-600" />
              Pilih Buku
            </h2>
            
            <div class="mb-4">
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <Search class="h-5 w-5 text-gray-400" />
                </div>
                <input
                  v-model="bookSearchQuery"
                  type="text"
                  placeholder="Cari buku berdasarkan judul atau penulis..."
                  class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                />
              </div>
              <p class="text-sm text-gray-500 mt-1">
                Menampilkan {{ filteredBooks().length }} dari {{ props.books.length }} buku yang tersedia.
              </p>
            </div>
            
            <div class="border border-gray-200 rounded-lg overflow-hidden">
              <div class="max-h-64 overflow-y-auto">
                <div v-if="filteredBooks().length === 0" class="p-4 text-gray-500 text-center">
                  Tidak ada buku yang ditemukan.
                </div>
                <div v-else>
                  <div class="grid grid-cols-1 divide-y divide-gray-200">
                    <label 
                      v-for="book in filteredBooks()" 
                      :key="book.id"
                      class="flex items-center p-3 cursor-pointer hover:bg-gray-50"
                      :class="{ 'bg-blue-50': form.book_id === book.id.toString() }"
                    >
                      <input
                        type="radio"
                        :value="book.id"
                        v-model="form.book_id"
                        class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
                      />
                      <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ book.judul }}</p>
                        <div class="flex items-center text-xs text-gray-500 mt-1 space-x-2">
                          <span v-if="book.penulis">{{ book.penulis }}</span>
                          <span v-if="book.penulis">•</span>
                          <span class="px-1.5 py-0.5 bg-green-100 text-green-800 rounded-full">Tersedia: {{ book.ketersediaan }}</span>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="form.errors.book_id" class="text-red-600 text-sm mt-1">
              {{ form.errors.book_id }}
            </div>
          </div>
          
          <!-- Due Date -->
          <div>
            <h2 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
              <Clock class="w-5 h-5 mr-2 text-blue-600" />
              Tenggat Pengembalian
            </h2>
            
            <div class="max-w-xs">
              <input
                v-model="form.due_date"
                type="date"
                :min="getMinDueDate()"
                class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              />
              <p class="text-sm text-gray-500 mt-1">
                Pilih tanggal kapan buku harus dikembalikan.
              </p>
              <div v-if="form.errors.due_date" class="text-red-600 text-sm mt-1">
                {{ form.errors.due_date }}
              </div>
            </div>
          </div>
          
          <!-- Submit Button -->
          <div class="flex justify-end pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="resetForm"
              class="mr-3 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Reset
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Buat Peminjaman
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template> 