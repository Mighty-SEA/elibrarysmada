<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps<{
  categories: string[];
  selectedCategory: string;
}>();

const emit = defineEmits<{
  (e: 'select', category: string): void;
}>();

const isDropdownOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref<HTMLDivElement | null>(null);

// Filter kategori berdasarkan pencarian
const filteredCategories = computed(() => {
  if (!searchQuery.value) return props.categories;
  
  const query = searchQuery.value.toLowerCase();
  return props.categories.filter(category => 
    category.toLowerCase().includes(query)
  );
});

// Menangani klik di luar dropdown untuk menutupnya
function handleClickOutside(event: MouseEvent) {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target as Node)) {
    isDropdownOpen.value = false;
  }
}

// Menangani pemilihan kategori
function selectCategory(category: string) {
  emit('select', category);
  isDropdownOpen.value = false;
  searchQuery.value = '';
}

// Menambahkan event listener untuk klik di luar
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative" ref="dropdownRef">
    <!-- Tombol kategori terpilih -->
    <button 
      @click="isDropdownOpen = !isDropdownOpen"
      class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
    >
      <span>Kategori: {{ selectedCategory }}</span>
      <svg class="w-5 h-5 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
      </svg>
    </button>

    <!-- Dropdown menu -->
    <div 
      v-if="isDropdownOpen" 
      class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
    >
      <!-- Kolom pencarian -->
      <div class="sticky top-0 p-2 bg-white border-b border-gray-300">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari kategori..."
          class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          @click.stop
        />
      </div>

      <!-- Daftar kategori -->
      <div v-if="filteredCategories.length > 0">
        <button
          v-for="category in filteredCategories"
          :key="category"
          @click="selectCategory(category)"
          class="block w-full px-4 py-2 text-sm text-left hover:bg-gray-100"
          :class="category === selectedCategory ? 'bg-blue-100 text-blue-700 font-medium' : 'text-gray-700'"
        >
          {{ category }}
        </button>
      </div>
      <div v-else class="p-4 text-sm text-gray-500 text-center">
        Tidak ada kategori yang ditemukan
      </div>
    </div>
  </div>
</template> 