<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import BookCard from './BookCard.vue';
import BookListView from './BookListView.vue';
import BookFilters from './BookFilters.vue';
import Pagination from './Pagination.vue';
import { useEventBus } from '@/composables/useEventBus';

// Data buku dari prop
const props = defineProps<{ 
  books: any[];
  initialSearchQuery?: string;
}>();

const allBooks = ref(Array.isArray(props.books) ? props.books : []);

// Kategori buku dinamis dari data
const categories = ref(['Semua']);
if (props.books && props.books.length > 0) {
  const allCats = props.books.flatMap(b => b.kategori_list || []);
  const uniqueCats = Array.from(new Set(allCats.map(c => c.trim()))).filter(Boolean);
  categories.value = ['Semua', ...uniqueCats];
}

const selectedCategory = ref('Semua');
const searchQuery = ref(props.initialSearchQuery || '');
const eventBus = useEventBus();
const viewMode = ref<'grid' | 'list'>('grid');
const selectedAvailability = ref<boolean | null>(null);
const sortField = ref('title');
const sortDirection = ref<'asc' | 'desc'>('asc');

// Pagination
const currentPage = ref(1);
const itemsPerPage = ref(24); // 4x6 grid pada layar besar

// Mendengarkan event pencarian
onMounted(() => {
  eventBus.on('search', (query: any) => {
    searchQuery.value = typeof query === 'string' ? query : '';
    currentPage.value = 1;
  });
});

onUnmounted(() => {
  eventBus.off('search');
});

// Filter buku berdasarkan kategori dan pencarian
const filteredBooks = computed(() => {
  let result = allBooks.value;
  
  // Filter berdasarkan kategori
  if (selectedCategory.value !== 'Semua') {
    result = result.filter(book => (book.kategori_list && book.kategori_list.includes(selectedCategory.value)) || book.category === selectedCategory.value);
  }
  
  // Filter berdasarkan ketersediaan
  if (selectedAvailability.value !== null) {
    result = result.filter(book => {
      const isAvailable = book.jumlah === undefined || book.jumlah > 0;
      return isAvailable === selectedAvailability.value;
    });
  }
  
  // Filter berdasarkan pencarian
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(book => 
      ((book.judul ?? book.title ?? '') + '').toLowerCase().includes(query) || 
      ((book.penulis ?? book.author ?? '') + '').toLowerCase().includes(query) ||
      ((book.kategori ?? book.category ?? '') + '').toLowerCase().includes(query)
    );
  }
  
  // Urutkan hasil
  result = sortBooks(result);
  
  return result;
});

// Fungsi untuk mengurutkan buku
function sortBooks(books: any[]) {
  return [...books].sort((a, b) => {
    let valueA, valueB;
    
    if (sortField.value === 'title') {
      valueA = ((a.judul ?? a.title ?? '') + '').toLowerCase();
      valueB = ((b.judul ?? b.title ?? '') + '').toLowerCase();
    } else if (sortField.value === 'author') {
      valueA = ((a.penulis ?? a.author ?? '') + '').toLowerCase();
      valueB = ((b.penulis ?? b.author ?? '') + '').toLowerCase();
    } else if (sortField.value === 'category') {
      valueA = ((a.kategori ?? a.category ?? '') + '').toLowerCase();
      valueB = ((b.kategori ?? b.category ?? '') + '').toLowerCase();
    } else {
      valueA = (a[sortField.value] ?? '') + '';
      valueB = (b[sortField.value] ?? '') + '';
    }
    
    // Urutan ascending atau descending
    const modifier = sortDirection.value === 'asc' ? 1 : -1;
    return valueA < valueB ? -1 * modifier : valueA > valueB ? 1 * modifier : 0;
  });
}

// Buku yang ditampilkan di halaman saat ini
const paginatedBooks = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  return filteredBooks.value.slice(startIndex, endIndex);
});

// Total halaman
const totalPages = computed(() => {
  return Math.ceil(filteredBooks.value.length / itemsPerPage.value);
});

// Callback handlers untuk filter dan urutan
function handleCategorySelect(category: string) {
  selectedCategory.value = category;
  currentPage.value = 1; // Reset ke halaman pertama saat ganti kategori
}

function handleAvailabilityFilter(available: boolean | null) {
  selectedAvailability.value = available;
  currentPage.value = 1;
}

function handleSort(field: string, direction: 'asc' | 'desc') {
  sortField.value = field;
  sortDirection.value = direction;
  currentPage.value = 1;
}

function handleViewChange(view: 'grid' | 'list') {
  viewMode.value = view;
}

function handlePageChange(page: number) {
  currentPage.value = page;
  const katalogEl = document.getElementById('katalog');
  if (katalogEl) {
    const katalogTop = katalogEl.getBoundingClientRect().top + window.scrollY;
    const currentScroll = window.scrollY;
    console.log('katalogTop:', katalogTop, 'currentScroll:', currentScroll);
    if (currentScroll > katalogTop) {
      console.log('User di bawah katalog, scroll ke katalog.');
      katalogEl.scrollIntoView({ behavior: 'smooth' });
    } else {
      console.log('User sudah di atas/di katalog, tidak perlu scroll.');
    }
  } else {
    console.log('Elemen katalog tidak ditemukan.');
  }
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <!-- Filter dan Opsi Sorting -->
    <BookFilters
      :categories="categories"
      :selectedCategory="selectedCategory"
      :totalBooks="allBooks.length"
      :filteredCount="filteredBooks.length"
      @filter-category="handleCategorySelect"
      @filter-availability="handleAvailabilityFilter"
      @sort="handleSort"
      @view-change="handleViewChange"
    />
    
    <div v-if="filteredBooks.length > 0">
      <!-- Grid View Mode -->
      <div v-if="viewMode === 'grid'" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
        <BookCard
          v-for="book in paginatedBooks"
          :key="book.id"
          :id="book.id"
          :title="book.judul || book.title"
          :author="book.penulis || book.author"
          :cover-image="book.cover_url || book.coverImage"
          :category="(book.kategori_list && book.kategori_list.length > 0) ? book.kategori_list.join(', ') : (book.kategori || book.category)"
          :available="book.jumlah === undefined ? true : book.jumlah > 0"
        />
      </div>
      
      <!-- List View Mode -->
      <BookListView v-else :books="paginatedBooks" />
      
      <!-- Pagination -->
      <Pagination 
        v-if="totalPages > 1"
        :current-page="currentPage" 
        :total-pages="totalPages"
        @page-change="handlePageChange"
        class="mt-8"
      />
    </div>
    <div v-else class="text-center py-16">
      <p class="text-gray-500 text-lg">Tidak ada buku yang ditemukan</p>
    </div>
  </div>
</template> 