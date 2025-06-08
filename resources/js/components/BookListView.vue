<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { BookOpen } from 'lucide-vue-next';

defineProps<{
  books: any[];
}>();
</script>

<template>
  <div class="space-y-4">
    <div v-for="book in books" :key="book.id" class="flex border rounded-lg overflow-hidden bg-white hover:shadow-md transition-shadow">
      <!-- Cover Image -->
      <div class="w-24 h-32 sm:w-32 sm:h-44 flex-shrink-0 bg-gray-200">
        <img 
          :src="book.cover_url || book.coverImage" 
          :alt="book.judul || book.title" 
          class="w-full h-full object-cover"
          onerror="this.src='/images/book-placeholder.svg'; this.onerror=null;"
        />
      </div>
      
      <!-- Book Details -->
      <div class="flex-1 p-4 flex flex-col">
        <div class="flex-1">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-lg font-medium text-gray-900">{{ book.judul || book.title }}</h3>
              <p class="text-sm text-gray-600">{{ book.penulis || book.author }}</p>
            </div>
            <span 
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              :class="(book.jumlah === undefined || book.jumlah > 0) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
            >
              <BookOpen class="h-3 w-3 mr-1" />
              {{ (book.jumlah === undefined || book.jumlah > 0) ? 'Tersedia' : 'Tidak Tersedia' }}
            </span>
          </div>
          
          <div class="mt-2">
            <p class="text-xs text-gray-500">Kategori:</p>
            <div class="flex flex-wrap gap-1 mt-1">
              <span 
                v-for="(cat, index) in (book.kategori_list && book.kategori_list.length > 0) 
                  ? book.kategori_list 
                  : [book.kategori || book.category]" 
                :key="index"
                class="inline-block px-2 py-0.5 rounded-full text-xs bg-blue-100 text-blue-800"
              >
                {{ cat }}
              </span>
            </div>
          </div>
          
          <p v-if="book.deskripsi || book.description" class="mt-2 text-sm text-gray-600 line-clamp-2">
            {{ book.deskripsi || book.description }}
          </p>
        </div>
        
        <div class="mt-4 flex justify-end">
          <Link
            :href="route('book.detail', book.id)"
            class="inline-flex items-center px-3 py-1.5 border border-blue-600 text-sm font-medium rounded-md text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
          >
            Detail Buku
          </Link>
        </div>
      </div>
    </div>
  </div>
</template> 