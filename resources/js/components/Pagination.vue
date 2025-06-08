<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
  links?: Array<{
    url: string | null;
    label: string;
    active: boolean;
  }>;
  currentPage?: number;
  totalPages?: number;
  onPageChange?: (page: number) => void;
}>();

// For backward compatibility
const emit = defineEmits<{
  (e: 'pageChange', page: number): void;
}>();

// Determine if we're using the old or new pagination style
const isUsingLaravelPagination = computed(() => props.links && props.links.length > 0);

// For old pagination style
function changePage(page: number) {
  if (page >= 1 && page <= (props.totalPages || 1)) {
    if (props.onPageChange) {
      props.onPageChange(page);
    } else {
      emit('pageChange', page);
    }
  }
}

// Calculate page numbers for old pagination style
const getPageNumbers = (isMobile = false) => {
  if (!props.currentPage || !props.totalPages) return [];
  
  const pages = [];
  const maxPagesToShow = isMobile ? 3 : 5;
  
  if (props.totalPages <= maxPagesToShow) {
    // If total pages <= max, show all pages
    for (let i = 1; i <= props.totalPages; i++) {
      pages.push(i);
    }
  } else {
    // If total pages > max, show pages with special logic
    if (props.currentPage <= Math.ceil(maxPagesToShow / 2)) {
      // If current page is at the beginning, show pages 1 to maxPagesToShow
      for (let i = 1; i <= maxPagesToShow; i++) {
        pages.push(i);
      }
    } else if (props.currentPage >= props.totalPages - Math.floor(maxPagesToShow / 2)) {
      // If current page is near the end, show the last maxPagesToShow pages
      for (let i = props.totalPages - (maxPagesToShow - 1); i <= props.totalPages; i++) {
        pages.push(i);
      }
    } else {
      // Show pages with currentPage in the middle
      const offset = Math.floor(maxPagesToShow / 2);
      for (let i = props.currentPage - offset; i <= props.currentPage + offset; i++) {
        pages.push(i);
      }
    }
  }
  
  return pages;
};
</script>

<template>
  <!-- Laravel Pagination (new style) -->
  <div v-if="isUsingLaravelPagination && links && links.length > 3" class="flex flex-wrap justify-center gap-1">
    <template v-for="(link, index) in links" :key="index">
      <!-- We need to use v-html here because Laravel pagination returns HTML entities -->
      <!-- eslint-disable-next-line vue/no-v-html -->
      <div v-if="link.url === null" class="px-4 py-2 text-sm text-gray-500 rounded-md border border-gray-200 bg-gray-50" v-html="link.label"></div>
      <!-- eslint-disable-next-line vue/no-v-html -->
      <Link
        v-else
        :href="link.url"
        class="px-4 py-2 text-sm rounded-md border"
        :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'text-gray-700 bg-white border-gray-200 hover:bg-gray-50'"
        v-html="link.label"
      ></Link>
    </template>
  </div>

  <!-- Old Pagination Style -->
  <div v-else-if="currentPage !== undefined && totalPages !== undefined" class="flex justify-center mt-4 md:mt-8">
    <!-- Desktop Pagination (show on md and above) -->
    <nav class="hidden md:flex items-center gap-1">
      <!-- Previous Button -->
      <button 
        @click="changePage(currentPage - 1)" 
        :disabled="currentPage === 1"
        class="px-3 py-1.5 rounded border shadow-sm flex items-center justify-center transition-colors duration-200"
        :class="currentPage === 1 
          ? 'bg-gray-50 text-gray-400 cursor-not-allowed' 
          : 'bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100'"
      >
        &laquo;
      </button>
      
      <!-- First page button if not in range -->
      <button 
        v-if="getPageNumbers()[0] > 1" 
        @click="changePage(1)"
        class="px-3 py-1.5 rounded border shadow-sm bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-200"
      >
        1
      </button>
      
      <!-- Ellipsis if gap between first page and displayed range -->
      <span v-if="getPageNumbers()[0] > 2" class="px-2 text-gray-700">...</span>
      
      <!-- Page buttons -->
      <button 
        v-for="page in getPageNumbers()" 
        :key="page"
        @click="changePage(page)"
        class="px-3 py-1.5 rounded border shadow-sm flex items-center justify-center min-w-[40px] transition-all duration-200"
        :class="page === currentPage 
          ? 'bg-blue-600 text-white border-blue-600 transform scale-105' 
          : 'bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100'"
      >
        {{ page }}
      </button>
      
      <!-- Ellipsis if gap between displayed range and last page -->
      <span v-if="getPageNumbers()[getPageNumbers().length - 1] < totalPages - 1" class="px-2 text-gray-700">...</span>
      
      <!-- Last page button if not in range -->
      <button 
        v-if="getPageNumbers()[getPageNumbers().length - 1] < totalPages" 
        @click="changePage(totalPages)"
        class="px-3 py-1.5 rounded border shadow-sm bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-200"
      >
        {{ totalPages }}
      </button>
      
      <!-- Next Button -->
      <button 
        @click="changePage(currentPage + 1)" 
        :disabled="currentPage === totalPages"
        class="px-3 py-1.5 rounded border shadow-sm flex items-center justify-center transition-colors duration-200"
        :class="currentPage === totalPages 
          ? 'bg-gray-50 text-gray-400 cursor-not-allowed' 
          : 'bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100'"
      >
        &raquo;
      </button>
    </nav>
    
    <!-- Mobile Pagination (show on small screens) -->
    <nav class="flex md:hidden items-center shadow-sm rounded-md overflow-hidden">
      <!-- Previous Button -->
      <button 
        @click="changePage(currentPage - 1)" 
        :disabled="currentPage === 1"
        class="px-3 py-2 border-r flex items-center justify-center min-w-[40px] transition-colors duration-200"
        :class="currentPage === 1 
          ? 'bg-gray-50 text-gray-400 cursor-not-allowed' 
          : 'bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100'"
      >
        &laquo;
      </button>
      
      <!-- Show current page and total pages -->
      <div class="px-4 py-2 bg-white flex items-center justify-center min-w-[70px]">
        <span class="font-semibold text-blue-600 transition-all duration-200 transform scale-110">{{ currentPage }}</span>
        <span class="mx-1 text-gray-400">/</span>
        <span class="text-gray-700">{{ totalPages }}</span>
      </div>
      
      <!-- Next Button -->
      <button 
        @click="changePage(currentPage + 1)" 
        :disabled="currentPage === totalPages"
        class="px-3 py-2 border-l flex items-center justify-center min-w-[40px] transition-colors duration-200"
        :class="currentPage === totalPages 
          ? 'bg-gray-50 text-gray-400 cursor-not-allowed' 
          : 'bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100'"
      >
        &raquo;
      </button>
    </nav>
  </div>
</template> 