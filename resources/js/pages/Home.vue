<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import BookCatalog from '@/components/BookCatalog.vue';
import SearchBar from '@/components/SearchBar.vue';
import AppFooter from '@/components/AppFooter.vue';
</script>

<template>
  <Head title="MyPerpus - Perpustakaan Digital">
    <meta name="description" content="Perpustakaan digital dengan ribuan koleksi buku yang bisa kamu pinjam dengan mudah" />
  </Head>

  <div class="min-h-screen bg-white">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm">
      <div class="container mx-auto px-4 py-4">
        <div class="flex flex-wrap items-center justify-between">
          <div class="flex items-center">
            <div class="text-2xl font-bold text-blue-600 mr-4">MyPerpus</div>
          </div>
          
          <div class="flex-1 mx-4 max-w-xl hidden md:block">
            <SearchBar />
          </div>
          
          <div class="flex items-center space-x-4">
            <template v-if="$page.props.auth.user">
              <Link
                :href="route('dashboard')"
                class="inline-block rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
              >
                Dashboard
              </Link>
            </template>
            <template v-else>
              <Link
                :href="route('login')"
                class="text-gray-600 hover:text-gray-800 transition-colors"
              >
                Masuk
              </Link>
              <Link
                :href="route('register')"
                class="inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
              >
                Daftar
              </Link>
            </template>
          </div>
        </div>
        
        <div class="md:hidden mt-4">
          <SearchBar />
        </div>
      </div>
    </header>

    <main>
      <!-- Book Catalog -->
      <section class="py-16">
        <div class="container mx-auto px-4">
          <BookCatalog />
        </div>
      </section>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template> 