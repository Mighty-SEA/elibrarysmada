<script setup lang="ts">
// Komponen untuk banner hero di halaman utama
</script>

<template>
  <div class="relative bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
    <div class="container mx-auto px-4 py-16 md:py-24">
      <div class="flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 mb-8 md:mb-0">
          <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mb-4">
            Temukan Buku Favoritmu
          </h1>
          <p class="text-lg md:text-xl mb-6 text-blue-100">
            Perpustakaan digital dengan ribuan koleksi buku yang bisa kamu pinjam dengan mudah
          </p>
          <div class="flex flex-wrap gap-4">
            <button class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-medium transition-colors">
              Jelajahi Katalog
            </button>
            <button class="bg-transparent border border-white text-white hover:bg-white/10 px-6 py-3 rounded-lg font-medium transition-colors">
              Cara Meminjam
            </button>
          </div>
        </div>
        <div class="md:w-1/2 flex justify-center">
          <img src="https://picsum.photos/seed/books/800/600" alt="Koleksi Buku" class="max-w-full h-auto rounded-lg shadow-lg" />
        </div>
      </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
  </div>
</template>