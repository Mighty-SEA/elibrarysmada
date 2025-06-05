<script setup lang="ts">
import { ref } from 'vue';
import { useEventBus } from '@/composables/useEventBus';

const searchQuery = ref('');
const eventBus = useEventBus();

function handleSearch() {
  eventBus.emit('search', searchQuery.value);
}
</script>

<template>
  <div class="w-full">
    <div class="relative">
      <input
        v-model="searchQuery"
        @keyup.enter="handleSearch"
        type="text"
        placeholder="Cari judul buku, penulis, atau kategori..."
        class="w-full py-2 pl-10 pr-4 text-gray-700 bg-white border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <div class="absolute inset-y-0 left-0 flex items-center pl-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
      </div>
      <button
        @click="handleSearch"
        class="absolute right-2 top-1 bottom-1 px-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm"
      >
        Cari
      </button>
    </div>
  </div>
</template> 