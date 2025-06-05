<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Keranjang',
        href: '/cart',
    },
];
</script>

<template>
    <Head title="Keranjang" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <h1 class="text-2xl font-bold mb-4">Keranjang Saya</h1>
                <div class="text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="mt-4 text-gray-500">Keranjang Anda masih kosong</p>
                    <p class="text-gray-500">Silakan tambahkan buku ke keranjang untuk melanjutkan</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template> 