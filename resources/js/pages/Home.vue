<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import BookCatalog from '@/components/BookCatalog.vue';
import SearchBar from '@/components/SearchBar.vue';
import AppFooter from '@/components/AppFooter.vue';
import { ShoppingCart, BookOpen, Menu, X } from 'lucide-vue-next';
import { ref, computed } from 'vue';

// Cek apakah rute register tersedia
const hasRegisterRoute = computed(() => {
  try {
    return route().has('register');
  } catch (error) {
    return false;
  }
});

const page = usePage();
const isMobileMenuOpen = ref(false);

// Fungsi untuk membuka dan menutup mobile menu
function toggleMobileMenu() {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
}

// Fungsi helper untuk cek admin
function isAdmin() {
  return page.props.auth?.user?.role === 'administrasi';
}

// Fungsi untuk mendapatkan login URL
function getLoginUrl() {
  try {
    return route('login');
  } catch (error) {
    return '/login';
  }
}

// Fungsi untuk mendapatkan register URL
function getRegisterUrl() {
  try {
    return route('register');
  } catch (error) {
    return '/register';
  }
}
</script>

<template>
  <Head title="MyPerpus - Perpustakaan Digital">
    <meta name="description" content="Perpustakaan digital dengan ribuan koleksi buku yang bisa kamu pinjam dengan mudah" />
  </Head>

  <div class="min-h-screen bg-gray-50 flex flex-col">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm sticky top-0 z-20">
      <div class="container mx-auto px-4 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo & Menu Toggle -->
          <div class="flex items-center">
            <Link :href="route('home')" class="flex items-center gap-2">
              <BookOpen class="h-6 w-6 text-blue-600" />
              <div class="text-2xl font-bold text-blue-600">MyPerpus</div>
            </Link>
          </div>
          
          <!-- Desktop Search Bar -->
          <div class="flex-1 mx-6 max-w-xl hidden md:block">
            <SearchBar />
          </div>
          
          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center space-x-4">
            <template v-if="page.props.auth?.user">
              <div class="mr-4 text-gray-700">
                Halo, <span class="font-medium">{{ page.props.auth.user.name }}</span>
              </div>
              
              <!-- Tombol Dashboard hanya untuk admin -->
              <Link
                v-if="isAdmin()"
                :href="route('dashboard')"
                class="inline-flex items-center gap-2 rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 hover:bg-blue-50"
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
                :href="getLoginUrl()"
                class="inline-block rounded-md border border-blue-600 px-4 py-2 text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 hover:text-blue-700 transition-colors"
              >
                Masuk
              </Link>
              <Link
                v-if="hasRegisterRoute"
                :href="getRegisterUrl()"
                class="inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
              >
                Daftar
              </Link>
            </template>
          </div>
          
          <!-- Mobile Menu Button -->
          <button 
            @click="toggleMobileMenu" 
            class="md:hidden p-2 rounded-md text-gray-700 hover:bg-gray-100 transition-colors"
          >
            <Menu v-if="!isMobileMenuOpen" class="h-6 w-6" />
            <X v-else class="h-6 w-6" />
          </button>
        </div>
        
        <!-- Mobile Search Bar -->
        <div class="md:hidden mt-4">
          <SearchBar />
        </div>
        
        <!-- Mobile Navigation Menu -->
        <div 
          v-if="isMobileMenuOpen" 
          class="md:hidden mt-4 py-3 border-t border-gray-200 space-y-4"
        >
          <template v-if="page.props.auth?.user">
            <div class="px-2 py-1 text-gray-700">
              Halo, <span class="font-medium">{{ page.props.auth.user.name }}</span>
            </div>
            
            <Link
              v-if="isAdmin()"
              :href="route('dashboard')"
              class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md"
            >
              Dashboard
            </Link>
            
            <Link
              v-else
              :href="route('cart')"
              class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md flex items-center gap-2"
            >
              <ShoppingCart class="h-5 w-5" />
              Keranjang
            </Link>
          </template>
          
          <template v-else>
            <div class="flex flex-col space-y-2">
              <Link
                :href="getLoginUrl()"
                class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md"
              >
                Masuk
              </Link>
              <Link
                v-if="hasRegisterRoute"
                :href="getRegisterUrl()"
                class="block px-2 py-2 text-blue-600 hover:bg-blue-50 rounded-md"
              >
                Daftar
              </Link>
            </div>
          </template>
        </div>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="py-12 md:py-20">
      <div class="container mx-auto px-4">
        <div class="relative rounded-xl shadow-xl overflow-hidden border border-gray-300/20 transform transition-all hover:shadow-2xl">
          <!-- Background Image -->
          <div class="absolute inset-0 z-0">
            <img 
              src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?q=80&w=2400" 
              alt="Library Books" 
              class="h-full w-full object-cover"
            />
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-gray-900/70"></div>
          </div>
          
          <!-- Hero Content -->
          <div class="relative z-10 flex flex-col min-h-[550px]">
            <div class="flex-grow flex flex-col justify-center items-start p-8 md:p-16 max-w-3xl">
              <!-- Decorative Elements -->
              <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mt-16 -mr-16"></div>
              <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/5 rounded-full -mb-12 -ml-12"></div>
              
              <div class="relative z-10 text-white">
                <h1 class="text-3xl md:text-5xl font-bold mb-6 leading-tight">Selamat Datang di MyPerpus</h1>
                <p class="text-lg md:text-xl opacity-90 mb-6 max-w-2xl">
                  Jelajahi koleksi buku-buku berkualitas dan pinjam dengan mudah
                </p>
                <p class="text-sm md:text-base opacity-80 mb-10 max-w-xl">
                  Temukan buku favoritmu dengan menggunakan filter kategori atau pencarian di atas
                </p>
                
                <div class="flex flex-wrap gap-4">
                  <a 
                    href="#katalog" 
                    class="inline-flex items-center px-6 py-3 rounded-md bg-white text-gray-800 font-medium hover:bg-gray-100 transition-colors shadow-md hover:shadow-lg"
                  >
                    Lihat Katalog
                  </a>
                  <a 
                    v-if="!page.props.auth?.user"
                    :href="getRegisterUrl()"
                    class="inline-flex items-center px-6 py-3 rounded-md bg-gray-900 text-white font-medium border border-white/30 hover:bg-gray-800 transition-colors shadow-md hover:shadow-lg"
                  >
                    Daftar Sekarang
                  </a>
                </div>
              </div>
            </div>
            
            <!-- Statistik -->
            <div class="relative w-full bg-black/40 backdrop-blur-sm py-8 border-t border-white/10">
              <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <!-- Card Jumlah Buku -->
                  <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/10 hover:bg-white/15 transition-all transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-center text-white">
                      <p class="text-3xl font-bold text-white mb-1">{{ Array.isArray(page.props.books) ? page.props.books.length : 0 }}+</p>
                      <p class="text-gray-300 text-sm">Jumlah Buku</p>
                    </div>
                  </div>
                  
                  <!-- Card Kategori -->
                  <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/10 hover:bg-white/15 transition-all transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-center text-white">
                      <p class="text-3xl font-bold text-white mb-1">10+</p>
                      <p class="text-gray-300 text-sm">Kategori</p>
                    </div>
                  </div>
                  
                  <!-- Card Akses Online -->
                  <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/10 hover:bg-white/15 transition-all transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-center text-white">
                      <p class="text-3xl font-bold text-white mb-1">24/7</p>
                      <p class="text-gray-300 text-sm">Akses Online</p>
                    </div>
                  </div>
                  
                  <!-- Card Gratis -->
                  <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 border border-white/10 hover:bg-white/15 transition-all transform hover:-translate-y-1 hover:shadow-lg">
                    <div class="text-center text-white">
                      <p class="text-3xl font-bold text-white mb-1">100%</p>
                      <p class="text-gray-300 text-sm">Gratis</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <main class="flex-grow">
      <!-- Book Catalog -->
      <section id="katalog" class="py-8 md:py-12">
        <div class="container mx-auto px-4">
          <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Katalog Buku</h2>
          <BookCatalog :books="page.props.books || []" />
        </div>
      </section>
    </main>

    <!-- Footer -->
    <AppFooter />
  </div>
</template>

<style scoped>
/* Animasi untuk hero section */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes floatBubble {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
  100% {
    transform: translateY(0px);
  }
}

h1, p {
  animation: fadeIn 0.8s ease-out forwards;
}

h1 {
  animation-delay: 0.2s;
}

p:nth-of-type(1) {
  animation-delay: 0.4s;
}

p:nth-of-type(2) {
  animation-delay: 0.6s;
}

.absolute {
  animation: floatBubble 6s ease-in-out infinite;
}

.absolute:nth-of-type(2) {
  animation-delay: 2s;
}
</style> 