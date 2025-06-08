<script setup lang="ts">
const props = defineProps<{
  currentPage: number;
  totalPages: number;
}>();

const emit = defineEmits<{
  (e: 'pageChange', page: number): void;
}>();

function changePage(page: number) {
  if (page >= 1 && page <= props.totalPages) {
    emit('pageChange', page);
  }
}

// Menghitung halaman yang akan ditampilkan (maksimal 5 halaman untuk desktop, 3 untuk mobile)
const getPageNumbers = (isMobile = false) => {
  const pages = [];
  const maxPagesToShow = isMobile ? 3 : 5;
  
  if (props.totalPages <= maxPagesToShow) {
    // Jika total halaman <= max, tampilkan semua halaman
    for (let i = 1; i <= props.totalPages; i++) {
      pages.push(i);
    }
  } else {
    // Jika total halaman > max, tampilkan halaman dengan logika khusus
    if (props.currentPage <= Math.ceil(maxPagesToShow / 2)) {
      // Jika halaman saat ini di awal, tampilkan halaman 1 sampai maxPagesToShow
      for (let i = 1; i <= maxPagesToShow; i++) {
        pages.push(i);
      }
    } else if (props.currentPage >= props.totalPages - Math.floor(maxPagesToShow / 2)) {
      // Jika halaman saat ini mendekati akhir, tampilkan maxPagesToShow halaman terakhir
      for (let i = props.totalPages - (maxPagesToShow - 1); i <= props.totalPages; i++) {
        pages.push(i);
      }
    } else {
      // Tampilkan halaman dengan currentPage di tengah
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
  <div class="flex justify-center mt-4 md:mt-8">
    <!-- Desktop Pagination (tampilkan pada md dan di atasnya) -->
    <nav class="hidden md:flex items-center gap-1">
      <!-- Tombol Previous -->
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
      
      <!-- Tombol halaman pertama jika tidak ditampilkan dalam range -->
      <button 
        v-if="getPageNumbers()[0] > 1" 
        @click="changePage(1)"
        class="px-3 py-1.5 rounded border shadow-sm bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-200"
      >
        1
      </button>
      
      <!-- Ellipsis jika ada gap antara halaman pertama dan range yang ditampilkan -->
      <span v-if="getPageNumbers()[0] > 2" class="px-2 text-gray-700">...</span>
      
      <!-- Tombol halaman -->
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
      
      <!-- Ellipsis jika ada gap antara range yang ditampilkan dan halaman terakhir -->
      <span v-if="getPageNumbers()[getPageNumbers().length - 1] < totalPages - 1" class="px-2 text-gray-700">...</span>
      
      <!-- Tombol halaman terakhir jika tidak ditampilkan dalam range -->
      <button 
        v-if="getPageNumbers()[getPageNumbers().length - 1] < totalPages" 
        @click="changePage(totalPages)"
        class="px-3 py-1.5 rounded border shadow-sm bg-white text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition-colors duration-200"
      >
        {{ totalPages }}
      </button>
      
      <!-- Tombol Next -->
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
    
    <!-- Mobile Pagination (tampilkan pada layar kecil) -->
    <nav class="flex md:hidden items-center shadow-sm rounded-md overflow-hidden">
      <!-- Tombol Previous -->
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
      
      <!-- Tampilkan halaman saat ini dan total halaman -->
      <div class="px-4 py-2 bg-white flex items-center justify-center min-w-[70px]">
        <span class="font-semibold text-blue-600 transition-all duration-200 transform scale-110">{{ currentPage }}</span>
        <span class="mx-1 text-gray-400">/</span>
        <span class="text-gray-700">{{ totalPages }}</span>
      </div>
      
      <!-- Tombol Next -->
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