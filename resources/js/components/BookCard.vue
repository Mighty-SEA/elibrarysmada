<template>
  <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform duration-300 hover:shadow-lg hover:-translate-y-1 flex flex-col h-full">
    <div class="relative aspect-[2/3] overflow-hidden">
      <img :src="coverImage" :alt="title" class="w-full h-full object-cover" onerror="this.src='/images/book-placeholder.svg'; this.onerror=null;" />
      <div v-if="available" class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
        Tersedia
      </div>
      <div v-else class="absolute top-2 right-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">
        Dipinjam
      </div>
    </div>
    <div class="p-4 flex-grow">
      <div class="text-xs text-gray-500 mb-1">{{ category || '' }}</div>
      <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 min-h-[2.5rem]">{{ title }}</h3>
      <p class="text-sm text-gray-600">{{ author }}</p>
    </div>
    <div class="px-4 pb-4 mt-auto">
      <Link :href="route('book.detail', id)" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-md text-sm transition-colors">
        Detail Buku
      </Link>
    </div>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
  id: number;
  title: string;
  author: string;
  coverImage: string;
  category?: string;
  available: boolean;
}>();
</script> 