<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';
import BookCard from './BookCard.vue';
import CategoryButton from './CategoryButton.vue';
import { useEventBus } from '@/composables/useEventBus';

interface Book {
  id: number;
  title: string;
  author: string;
  coverImage: string;
  category: string;
  available: boolean;
}

// Data buku hardcoded
const allBooks = ref<Book[]>([
  {
    id: 1,
    title: 'Harry Potter dan Batu Bertuah',
    author: 'J.K. Rowling',
    coverImage: 'https://picsum.photos/seed/book1/300/450',
    category: 'Fiksi',
    available: true
  },
  {
    id: 2,
    title: 'Laskar Pelangi',
    author: 'Andrea Hirata',
    coverImage: 'https://picsum.photos/seed/book2/300/450',
    category: 'Novel',
    available: false
  },
  {
    id: 3,
    title: 'Bumi Manusia',
    author: 'Pramoedya Ananta Toer',
    coverImage: 'https://picsum.photos/seed/book3/300/450',
    category: 'Sejarah',
    available: true
  },
  {
    id: 4,
    title: 'Filosofi Teras',
    author: 'Henry Manampiring',
    coverImage: 'https://picsum.photos/seed/book4/300/450',
    category: 'Filsafat',
    available: true
  },
  {
    id: 5,
    title: 'Atomic Habits',
    author: 'James Clear',
    coverImage: 'https://picsum.photos/seed/book5/300/450',
    category: 'Pengembangan Diri',
    available: false
  },
  {
    id: 6,
    title: 'Sapiens: Riwayat Singkat Umat Manusia',
    author: 'Yuval Noah Harari',
    coverImage: 'https://picsum.photos/seed/book6/300/450',
    category: 'Sejarah',
    available: true
  },
  {
    id: 7,
    title: 'Pulang',
    author: 'Tere Liye',
    coverImage: 'https://picsum.photos/seed/book7/300/450',
    category: 'Novel',
    available: true
  },
  {
    id: 8,
    title: 'Laut Bercerita',
    author: 'Leila S. Chudori',
    coverImage: 'https://picsum.photos/seed/book8/300/450',
    category: 'Novel',
    available: false
  }
]);

// Kategori buku
const categories = ref([
  'Semua',
  'Fiksi',
  'Novel',
  'Sejarah',
  'Filsafat',
  'Pengembangan Diri'
]);

const selectedCategory = ref('Semua');
const searchQuery = ref('');
const eventBus = useEventBus();

// Mendengarkan event pencarian
onMounted(() => {
  eventBus.on('search', (query: string) => {
    searchQuery.value = query;
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
    result = result.filter(book => book.category === selectedCategory.value);
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

function handleCategorySelect(category: string) {
  selectedCategory.value = category;
}
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="mb-8 overflow-x-auto">
      <div class="flex space-x-2 pb-2">
        <CategoryButton
          v-for="category in categories"
          :key="category"
          :name="category"
          :active="selectedCategory === category"
          @select="handleCategorySelect(category)"
        />
      </div>
    </div>
    
    <div v-if="filteredBooks.length > 0">
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
        <BookCard
          v-for="book in filteredBooks"
          :key="book.id"
          :id="book.id"
          :title="book.title"
          :author="book.author"
          :cover-image="book.coverImage"
          :category="book.category"
          :available="book.available"
        />
      </div>
    </div>
    <div v-else class="text-center py-16">
      <p class="text-gray-500 text-lg">Tidak ada buku yang ditemukan</p>
    </div>
  </div>
</template> 