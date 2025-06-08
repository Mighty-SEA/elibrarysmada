<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { LayoutGrid, Book, BookOpen, Users, BookMarked, Clock, CheckCircle2, AlertTriangle } from 'lucide-vue-next';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Dashboard Administrasi</h1>
            
            <!-- Stats Cards -->
            <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
                <!-- Total Buku -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Total Buku</h3>
                        <Book class="h-6 w-6 text-blue-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">120</p>
                    <p class="text-sm text-gray-500 mt-2">Tersedia di perpustakaan</p>
                </div>
                
                <!-- Total Peminjaman -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Total Peminjaman</h3>
                        <BookOpen class="h-6 w-6 text-green-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">45</p>
                    <p class="text-sm text-gray-500 mt-2">Peminjaman bulan ini</p>
                </div>
                
                <!-- Menunggu Persetujuan -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Permintaan Baru</h3>
                        <Clock class="h-6 w-6 text-yellow-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">12</p>
                    <p class="text-sm text-gray-500 mt-2">Menunggu persetujuan</p>
                </div>
                
                <!-- Total Pengguna -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-4 shadow-sm flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Total Pengguna</h3>
                        <Users class="h-6 w-6 text-purple-500" />
                    </div>
                    <p class="text-3xl font-bold text-gray-900">78</p>
                    <p class="text-sm text-gray-500 mt-2">Terdaftar di sistem</p>
                </div>
            </div>
            
            <!-- Quick Access Section -->
            <div class="mt-6 grid gap-4 grid-cols-1 md:grid-cols-2">
                <!-- Manajemen Peminjaman -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Manajemen Peminjaman</h2>
                        <BookMarked class="h-7 w-7 text-blue-500" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                        <Link 
                            :href="route('loans.pending')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <Clock class="h-5 w-5 text-yellow-500" />
                                <h3 class="font-medium text-gray-900">Permintaan Peminjaman</h3>
                            </div>
                            <p class="text-sm text-gray-600">Peminjaman yang menunggu persetujuan</p>
                        </Link>
                        
                        <Link 
                            :href="route('loans.active')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <BookOpen class="h-5 w-5 text-blue-500" />
                                <h3 class="font-medium text-gray-900">Buku Dipinjam</h3>
                            </div>
                            <p class="text-sm text-gray-600">Buku yang sedang dipinjam</p>
                        </Link>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <Link 
                            :href="route('loans.index', {status: 'terlambat'})"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <AlertTriangle class="h-5 w-5 text-red-500" />
                                <h3 class="font-medium text-gray-900">Terlambat</h3>
                            </div>
                            <p class="text-sm text-gray-600">Peminjaman yang terlambat dikembalikan</p>
                        </Link>
                        
                        <Link 
                            :href="route('loans.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <CheckCircle2 class="h-5 w-5 text-green-500" />
                                <h3 class="font-medium text-gray-900">Semua Peminjaman</h3>
                            </div>
                            <p class="text-sm text-gray-600">Lihat semua riwayat peminjaman</p>
                        </Link>
                    </div>
                </div>
                
                <!-- Manajemen Lainnya -->
                <div class="bg-white rounded-xl border border-sidebar-border/70 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Manajemen Lainnya</h2>
                        <LayoutGrid class="h-7 w-7 text-blue-500" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <Link 
                            :href="route('books.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <Book class="h-5 w-5 text-blue-500" />
                                <h3 class="font-medium text-gray-900">Manajemen Buku</h3>
                            </div>
                            <p class="text-sm text-gray-600">Kelola data buku perpustakaan</p>
                        </Link>
                        
                        <Link 
                            :href="route('user-management.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow-md transition-all"
                        >
                            <div class="flex items-center gap-2">
                                <Users class="h-5 w-5 text-purple-500" />
                                <h3 class="font-medium text-gray-900">Manajemen Pengguna</h3>
                            </div>
                            <p class="text-sm text-gray-600">Kelola pengguna perpustakaan</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
