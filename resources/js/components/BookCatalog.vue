<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import BookCard from './BookCard.vue';
import CategoryFilter from './CategoryFilter.vue';
import Pagination from './Pagination.vue';
import { useEventBus } from '@/composables/useEventBus';

// Data buku dari prop
const props = defineProps<{ books: any[] }>();
const allBooks = ref(Array.isArray(props.books) ? props.books : []);

// Kategori buku dinamis dari data
const categories = ref(['Semua']);
if (props.books && props.books.length > 0) {
  const allCats = props.books.flatMap(b => b.kategori_list || []);
  const uniqueCats = Array.from(new Set(allCats.map(c => c.trim()))).filter(Boolean);
  categories.value = ['Semua', ...uniqueCats];
}

const selectedCategory = ref('Semua');
const searchQuery = ref('');
const eventBus = useEventBus();

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
  
  // Filter berdasarkan pencarian
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    result = result.filter(book => 
      book.title.toLowerCase().includes(query) || 
      book.author.toLowerCase().includes(query) ||
      book.category.toLowerCase().includes(query)
    );
  }
  
  return result;
});

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

function handleCategorySelect(category: string) {
  selectedCategory.value = category;
  currentPage.value = 1; // Reset ke halaman pertama saat ganti kategori
}

function handlePageChange(page: number) {
  currentPage.value = page;
  // Scroll ke atas halaman
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8 max-w-xs">
      <CategoryFilter
        :categories="categories"
        :selectedCategory="selectedCategory"
        @select="handleCategorySelect"
      />
    </div>
    
    <div v-if="filteredBooks.length > 0">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
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
      
      <!-- Pagination -->
      <Pagination 
        v-if="totalPages > 1"
        :current-page="currentPage" 
        :total-pages="totalPages"
        @page-change="handlePageChange"
      />
      
      <div class="text-center text-gray-500 text-sm mt-4">
        Menampilkan {{ paginatedBooks.length }} dari {{ filteredBooks.length }} buku
      </div>
    </div>
    <div v-else class="text-center py-16">
      <p class="text-gray-500 text-lg">Tidak ada buku yang ditemukan</p>
    </div>
  </div>
</template> 