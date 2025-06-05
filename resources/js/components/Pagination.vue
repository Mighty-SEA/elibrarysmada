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

// Menghitung halaman yang akan ditampilkan (maksimal 5 halaman)
const getPageNumbers = () => {
  const pages = [];
  const maxPagesToShow = 5;
  
  if (props.totalPages <= maxPagesToShow) {
    // Jika total halaman <= 5, tampilkan semua halaman
    for (let i = 1; i <= props.totalPages; i++) {
      pages.push(i);
    }
  } else {
    // Jika total halaman > 5, tampilkan halaman dengan logika khusus
    if (props.currentPage <= 3) {
      // Jika halaman saat ini <= 3, tampilkan halaman 1-5
      for (let i = 1; i <= 5; i++) {
        pages.push(i);
      }
    } else if (props.currentPage >= props.totalPages - 2) {
      // Jika halaman saat ini mendekati akhir, tampilkan 5 halaman terakhir
      for (let i = props.totalPages - 4; i <= props.totalPages; i++) {
        pages.push(i);
      }
    } else {
      // Tampilkan 2 halaman sebelum dan 2 halaman sesudah halaman saat ini
      for (let i = props.currentPage - 2; i <= props.currentPage + 2; i++) {
        pages.push(i);
      }
    }
  }
  
  return pages;
};
</script>

<template>
  <div class="flex justify-center mt-8">
    <nav class="flex items-center">
      <!-- Tombol Previous -->
      <button 
        @click="changePage(currentPage - 1)" 
        :disabled="currentPage === 1"
        class="px-3 py-1 mx-1 rounded border"
        :class="currentPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
      >
        &laquo;
      </button>
      
      <!-- Tombol halaman pertama jika tidak ditampilkan dalam range -->
      <button 
        v-if="getPageNumbers()[0] > 1" 
        @click="changePage(1)"
        class="px-3 py-1 mx-1 rounded border bg-gray-100 text-gray-700 hover:bg-gray-200"
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
        class="px-3 py-1 mx-1 rounded border"
        :class="page === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
      >
        {{ page }}
      </button>
      
      <!-- Ellipsis jika ada gap antara range yang ditampilkan dan halaman terakhir -->
      <span v-if="getPageNumbers()[getPageNumbers().length - 1] < totalPages - 1" class="px-2 text-gray-700">...</span>
      
      <!-- Tombol halaman terakhir jika tidak ditampilkan dalam range -->
      <button 
        v-if="getPageNumbers()[getPageNumbers().length - 1] < totalPages" 
        @click="changePage(totalPages)"
        class="px-3 py-1 mx-1 rounded border bg-gray-100 text-gray-700 hover:bg-gray-200"
      >
        {{ totalPages }}
      </button>
      
      <!-- Tombol Next -->
      <button 
        @click="changePage(currentPage + 1)" 
        :disabled="currentPage === totalPages"
        class="px-3 py-1 mx-1 rounded border"
        :class="currentPage === totalPages ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
      >
        &raquo;
      </button>
    </nav>
  </div>
</template> 