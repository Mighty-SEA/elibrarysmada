<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { LayoutGrid, Book, BookOpen, Users, BookMarked, Clock, CheckCircle2, AlertTriangle, Calendar } from 'lucide-vue-next';
import { ref, onMounted, watch } from 'vue';
import { BarChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

interface ChartData {
    [key: string]: number;
}

const page = usePage();
const totalBooks = page.props.totalBooks ?? 0;
const totalLoans = page.props.totalLoans ?? 0;
const pendingRequests = page.props.pendingRequests ?? 0;
const totalUsers = page.props.totalUsers ?? 0;
const booksAdded = page.props.booksAdded;
const booksDeleted = page.props.booksDeleted as number | null;
const showMonthlyCharts = page.props.showMonthlyCharts as boolean;
const loanChartMonthly = page.props.loanChartMonthly as ChartData || {};
const userChartMonthly = page.props.userChartMonthly as ChartData || {};

const startDate = ref(page.props.startDate || '');
const endDate = ref(page.props.endDate || '');

// Ambil filter dari localStorage saat halaman dibuka
onMounted(() => {
    const savedStart = localStorage.getItem('dashboard_startDate');
    const savedEnd = localStorage.getItem('dashboard_endDate');
    if (savedStart !== null) startDate.value = savedStart;
    if (savedEnd !== null) endDate.value = savedEnd;
    
    // Otomatis terapkan filter jika ada nilai disimpan di localStorage
    if ((savedStart !== null || savedEnd !== null) && 
        (savedStart !== page.props.startDate || savedEnd !== page.props.endDate)) {
        filterByDate();
    }
});

// Simpan filter ke localStorage setiap kali berubah
watch([startDate, endDate], ([newStart, newEnd]) => {
    localStorage.setItem('dashboard_startDate', newStart);
    localStorage.setItem('dashboard_endDate', newEnd);
});

const chartOptions = {
    responsive: true,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: {
            ticks: {
                callback: function(value: number) {
                    if (Number.isInteger(value)) {
                        return value;
                    }
                    return '';
                },
                stepSize: 1,
            },
            beginAtZero: true,
            precision: 0,
        }
    }
};

// Data chart bulanan
const loanChartMonthlyLabels = Object.keys(loanChartMonthly);
const userChartMonthlyLabels = Object.keys(userChartMonthly);

const loanChartMonthlyData = {
    labels: loanChartMonthlyLabels,
    datasets: [
        {
            label: 'Jumlah Peminjam per Bulan',
            data: Object.values(loanChartMonthly).map(v => parseInt(v)),
            backgroundColor: '#10b981', // Warna hijau untuk membedakan dari chart utama
        },
    ],
};

const userChartMonthlyData = {
    labels: userChartMonthlyLabels,
    datasets: [
        {
            label: 'Jumlah Anggota Baru per Bulan',
            data: Object.values(userChartMonthly).map(v => parseInt(v)),
            backgroundColor: '#8b5cf6', // Warna ungu untuk membedakan dari chart utama
        },
    ],
};

function filterByDate() {
    router.get(route('dashboard'), {
        start: startDate.value as string,
        end: endDate.value as string,
    });
}

function resetDateFilter() {
    startDate.value = '';
    endDate.value = '';
    localStorage.removeItem('dashboard_startDate');
    localStorage.removeItem('dashboard_endDate');
    router.get(route('dashboard'));
}

function exportPDF() {
    // Kirim filter tanggal ke halaman export
    const params = [];
    if (startDate.value) params.push(`start=${encodeURIComponent(startDate.value)}`);
    if (endDate.value) params.push(`end=${encodeURIComponent(endDate.value)}`);
    const url = params.length > 0 ? `${route('dashboard.export')}?${params.join('&')}` : route('dashboard.export');
    window.location.href = url; // Menggunakan window.location.href alih-alih window.open
}
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Administrasi</h1>
                <button @click="exportPDF" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition">
                    Cetak
                </button>
            </div>
            <!-- Area yang akan di-export ke PDF -->
            <div id="dashboard-export-area">
                <!-- Filter Tanggal -->
                <div class="flex flex-wrap items-center gap-2 mb-4 bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 shadow-sm">
                    <div class="flex-1 flex items-center gap-3">
                        <Calendar class="h-5 w-5 text-blue-600" />
                        <span class="font-medium text-blue-800">Filter Data:</span>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 whitespace-nowrap">Dari:</span>
                            <input 
                                type="date" 
                                v-model="startDate" 
                                class="border border-blue-200 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 bg-white shadow-sm" 
                            />
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 whitespace-nowrap">Sampai:</span>
                            <input 
                                type="date" 
                                v-model="endDate" 
                                class="border border-blue-200 rounded px-2 py-1.5 text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 bg-white shadow-sm" 
                            />
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <button 
                                @click="filterByDate" 
                                class="inline-flex items-center gap-1 bg-blue-600 hover:bg-blue-700 text-white rounded px-3 py-1.5 text-sm font-medium transition shadow-sm"
                            >
                                <span>Terapkan Filter</span>
                            </button>
                            
                            <button 
                                @click="resetDateFilter" 
                                class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded px-3 py-1.5 text-sm font-medium transition shadow-sm border border-gray-300"
                            >
                                <span>Reset</span>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="grid gap-4 grid-cols-1 md:grid-cols-4">
                    <!-- Total Buku -->
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-medium text-gray-700">Total Buku</h3>
                            <Book class="h-6 w-6 text-blue-500" />
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ totalBooks }}</p>
                        <p class="text-sm text-gray-500 mt-2">Tersedia di perpustakaan</p>
                        <template v-if="startDate || endDate">
                            <p class="text-xs mt-1 text-gray-600">
                                <span v-if="booksAdded !== null">+ {{ booksAdded }} buku ditambah</span>
                                <span v-if="booksDeleted !== null && booksDeleted > 0">, - {{ booksDeleted }} buku dihapus</span>
                            </p>
                        </template>
                    </div>
                    
                    <!-- Total Peminjaman -->
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-medium text-gray-700">Total Peminjaman</h3>
                            <BookOpen class="h-6 w-6 text-green-500" />
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ totalLoans }}</p>
                        <p class="text-sm text-gray-500 mt-2">
                            <template v-if="startDate || endDate">
                                Peminjaman pada rentang tanggal
                            </template>
                            <template v-else>
                                Total peminjaman
                            </template>
                        </p>
                    </div>
                    
                    <!-- Menunggu Persetujuan -->
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-medium text-gray-700">Permintaan Baru</h3>
                            <Clock class="h-6 w-6 text-yellow-500" />
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ pendingRequests }}</p>
                        <p class="text-sm text-gray-500 mt-2">Menunggu persetujuan</p>
                    </div>
                    
                    <!-- Total Anggota -->
                    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col hover:shadow-md transition">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-lg font-medium text-gray-700">Total Anggota</h3>
                            <Users class="h-6 w-6 text-purple-500" />
                        </div>
                        <p class="text-3xl font-bold text-gray-900">{{ totalUsers }}</p>
                        <p class="text-sm text-gray-500 mt-2">Terdaftar di sistem</p>
                    </div>
                </div>

                <!-- Chart Bulanan (muncul hanya jika rentang waktu >1 bulan) -->
                <template v-if="showMonthlyCharts">
                    <div class="grid gap-4 grid-cols-1 md:grid-cols-2 mt-4">
                        <!-- Card Chart Peminjam per Bulan -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col items-center justify-center min-h-[220px] w-full">
                            <h2 class="text-lg font-semibold mb-2">Peminjam per Bulan</h2>
                            <BarChart :chartData="loanChartMonthlyData" :options="chartOptions" style="width:100%;max-width:500px" />
                        </div>
                        <!-- Card Chart User per Bulan -->
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col items-center justify-center min-h-[220px] w-full">
                            <h2 class="text-lg font-semibold mb-2">Anggota Baru per Bulan</h2>
                            <BarChart :chartData="userChartMonthlyData" :options="chartOptions" style="width:100%;max-width:500px" />
                        </div>
                    </div>
                </template>

                <!-- Chart per Jurusan dihapus -->
            </div>
            
            <!-- Quick Access Section -->
            <div class="mt-6 grid gap-4 grid-cols-1 md:grid-cols-2">
                <!-- Manajemen Peminjaman -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Manajemen Peminjaman</h2>
                        <BookMarked class="h-7 w-7 text-blue-500" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-4">
                        <Link 
                            :href="route('loans.pending')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow transition-all bg-white"
                        >
                            <div class="flex items-center gap-2">
                                <Clock class="h-5 w-5 text-yellow-500" />
                                <h3 class="font-medium text-gray-900">Permintaan Peminjaman</h3>
                            </div>
                            <p class="text-sm text-gray-600">Peminjaman yang menunggu persetujuan</p>
                        </Link>
                        
                        <Link 
                            :href="route('loans.active')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow transition-all bg-white"
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
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow transition-all bg-white"
                        >
                            <div class="flex items-center gap-2">
                                <AlertTriangle class="h-5 w-5 text-red-500" />
                                <h3 class="font-medium text-gray-900">Terlambat</h3>
                            </div>
                            <p class="text-sm text-gray-600">Peminjaman yang terlambat dikembalikan</p>
                        </Link>
                        
                        <Link 
                            :href="route('loans.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow transition-all bg-white"
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
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Manajemen Lainnya</h2>
                        <LayoutGrid class="h-7 w-7 text-blue-500" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <Link 
                            :href="route('books.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow transition-all bg-white"
                        >
                            <div class="flex items-center gap-2">
                                <Book class="h-5 w-5 text-blue-500" />
                                <h3 class="font-medium text-gray-900">Manajemen Buku</h3>
                            </div>
                            <p class="text-sm text-gray-600">Kelola data buku perpustakaan</p>
                        </Link>
                        
                        <Link 
                            :href="route('user-management.index')"
                            class="flex flex-col gap-2 p-4 border rounded-lg hover:border-blue-400 hover:shadow transition-all bg-white"
                        >
                            <div class="flex items-center gap-2">
                                <Users class="h-5 w-5 text-purple-500" />
                                <h3 class="font-medium text-gray-900">Manajemen Anggota</h3>
                            </div>
                            <p class="text-sm text-gray-600">Kelola anggota perpustakaan</p>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
