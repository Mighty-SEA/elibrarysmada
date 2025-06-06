<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import BookCatalog from '@/components/BookCatalog.vue';
import SearchBar from '@/components/SearchBar.vue';
import AppFooter from '@/components/AppFooter.vue';
import { ShoppingCart } from 'lucide-vue-next';

// Tambahkan pengecekan route register
const hasRegisterRoute = typeof route === 'function' && route.has ? route.has('register') : false;

const page = usePage();
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
              <!-- Tombol Dashboard hanya untuk admin -->
              <Link
                v-if="$page.props.auth.user.role === 'administrasi'"
                :href="route('dashboard')"
                class="inline-block rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
              >
                Dashboard
              </Link>
              
              <!-- Tombol Keranjang untuk murid dan guru -->
              <Link
                v-else
                :href="route('cart')"
                class="inline-flex items-center gap-2 rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
              >
                <ShoppingCart class="h-4 w-4" />
                Keranjang
              </Link>
            </template>
            <template v-else>
              <Link
                :href="route('login')"
                class="inline-block rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 hover:text-blue-700 transition-colors"
              >
                Masuk
              </Link>
              <Link
                v-if="hasRegisterRoute"
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
          <BookCatalog :books="page.props.books" />
        </div>
      </section>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template> 