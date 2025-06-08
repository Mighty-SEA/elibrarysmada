<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useEventBus } from '@/composables/useEventBus';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();
const searchQuery = ref('');

// Cek jika ada query pencarian dari server
onMounted(() => {
  const pageProps = page.props as any;
  if (pageProps.searchQuery) {
    searchQuery.value = pageProps.searchQuery;
    // Jika ada query pencarian, langsung trigger pencarian
    handleSearch(false);
  }
});

const eventBus = useEventBus();

// Cek jika sedang berada di halaman detail buku
const isBookDetailPage = window.location.pathname.includes('/book/');

function scrollToKatalogIfNeeded() {
  const katalogEl = document.getElementById('katalog');
  if (katalogEl) {
    console.log('search: Selalu scroll ke katalog.');
    katalogEl.scrollIntoView({ behavior: 'smooth' });
  } else {
    console.log('search: Elemen katalog tidak ditemukan.');
  }
}

function handleSearch(navigate = true) {
  if (isBookDetailPage && navigate) {
    // Jika di halaman detail buku, redirect ke home dengan query pencarian
    router.visit(route('home', { search: searchQuery.value }), {
      preserveState: true,
      preserveScroll: true, // Pastikan posisi scroll dipertahankan
      only: ['books', 'searchQuery'],
      onSuccess: () => {
        // Setelah navigasi ke halaman home, emit event search untuk memulai pencarian
        setTimeout(() => {
          eventBus.emit('search', searchQuery.value);
          scrollToKatalogIfNeeded();
        }, 100);
      }
    });
  } else {
    // Update URL dengan query pencarian
    if (navigate) {
      router.get(route('home', { search: searchQuery.value }), {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['books', 'searchQuery'],
        replace: true,
      });
    }
    // Gunakan event bus untuk pencarian
    eventBus.emit('search', searchQuery.value);
    scrollToKatalogIfNeeded();
  }
}
</script>

<template>
  <div class="w-full">
    <div class="relative">
      <input
        v-model="searchQuery"
        @keyup.enter="handleSearch()"
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
        @click="handleSearch()"
        class="absolute right-2 top-1 bottom-1 px-3 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm"
      >
        Cari
      </button>
    </div>
  </div>
</template> 