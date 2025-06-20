<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { BarChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(...registerables, ChartDataLabels);

import { onMounted, ref } from 'vue';
declare global {
  interface Window {
    print: () => void;
    location: Location;
  }
}

// Definisikan interface untuk tipe data loans dan users
interface User {
  id: number;
  name: string;
  username: string;
  jurusan?: string;
  jenis_kelamin?: string;
}

interface Book {
  judul: string;
}

interface Loan {
  id: number;
  user?: User;
  book?: Book;
  status: string;
}

const page = usePage();
const totalBooks = page.props.totalBooks ?? 0;
const totalLoans = page.props.totalLoans ?? 0;
const totalUsers = page.props.totalUsers ?? 0;
const loanChart = page.props.loanChart || {} as Record<string, string>;
const userChart = page.props.userChart || {} as Record<string, string>;
const loans = page.props.loans || [] as Loan[];
const users = page.props.users || [] as User[];
const autoDownload = page.props.autoDownload ?? false;
const isLoading = ref(true);

function formatLabel(label: string) {
    // Format: "Jenis Kelamin Jurusan" --> "Jurusan - JK"
    if (!label || label === '-') return 'Tidak Diketahui';
    
    const parts = label.split(' ');
    if (parts.length < 2) return label; // Jika format tidak sesuai, kembalikan label asli
    
    const jk = parts[0];
    const jurusan = parts.slice(1).join(' ');
    
    // Format jenis kelamin
    let jkLabel = jk;
    if (jk === 'Laki-laki') jkLabel = 'L';
    else if (jk === 'Perempuan') jkLabel = 'P';
    else if (jk === '-') jkLabel = 'Tidak Diketahui';
    
    // Format jurusan
    const jurusanLabel = jurusan === '-' ? 'Tidak Diketahui' : jurusan;
    
    // Mengembalikan format singkat agar lebih mudah ditampilkan di chart
    return jurusanLabel + '-' + jkLabel;
}

// Format label dengan fungsi formatLabel
const loanChartLabels = Object.keys(loanChart).map(formatLabel);
const userChartLabels = Object.keys(userChart).map(formatLabel);

// Ambil nilai dari loanChart dan userChart
const loanChartValues = Object.values(loanChart).map(value => {
    const numValue = parseInt(value as string);
    return isNaN(numValue) ? 0 : numValue;
});

const userChartValues = Object.values(userChart).map(value => {
    const numValue = parseInt(value as string);
    return isNaN(numValue) ? 0 : numValue;
});

// Gunakan warna sederhana untuk chart dengan data yang sudah sesuai
// TypeScript complains about datalabels in the chart, so we need to use "as any" to avoid type errors
const loanChartData = {
    labels: loanChartLabels,
    datasets: [
        {
            label: 'Jumlah Peminjam',
            data: loanChartValues,
            backgroundColor: '#4b72b0',
        },
    ],
} as any;

const userChartData = {
    labels: userChartLabels,
    datasets: [
        {
            label: 'Jumlah User',
            data: userChartValues,
            backgroundColor: '#c97b63',
        },
    ],
} as any;

// Debug labels
console.log('Label Peminjam:', loanChartLabels);
console.log('Label Anggota:', userChartLabels);

function formatDateRange(start: string | null, end: string | null) {
    if (!start && !end) return 'Semua Data';
    // Definisikan tipe yang benar untuk options
    const opts = { year: 'numeric', month: 'long', day: '2-digit' } as Intl.DateTimeFormatOptions;
    const s = start ? new Date(start).toLocaleDateString('id-ID', opts) : '';
    const e = end ? new Date(end).toLocaleDateString('id-ID', opts) : '';
    if (s && e) return `${s} s/d ${e}`;
    if (s) return `Mulai ${s}`;
    if (e) return `Sampai ${e}`;
    return '';
}
const startDate = page.props.startDate || '';
const endDate = page.props.endDate || '';

// Gunakan print browser untuk mencetak secara langsung
function printReport() {
    setTimeout(() => {
        isLoading.value = false;
        
        // Jika autoDownload aktif, langsung print
        if (autoDownload) {
            setTimeout(() => {
                // Print menggunakan window global
                if (typeof window !== 'undefined') {
                    window.print();
                    
                    // Kembali ke dashboard setelah print dialog muncul
                    setTimeout(() => {
                        window.location.href = '/admin/dashboard';
                    }, 1000);
                }
            }, 500);
        }
    }, 1000);
}

onMounted(() => {
    printReport();
});
</script>

<template>
    <div class="print-container">
        <!-- Loading Indicator -->
        <div v-if="isLoading" class="loading-container">
            <div class="loading-spinner"></div>
            <p>Mempersiapkan laporan...</p>
        </div>

        <!-- Tombol Print (hanya tampil jika tidak autoDownload) -->
        <div v-if="!autoDownload && !isLoading" class="print-controls no-print">
            <button @click="() => { if (typeof window !== 'undefined') window.print(); }" class="print-button">
                Cetak Laporan
            </button>
        </div>

        <!-- Konten yang akan dicetak -->
        <div class="print-content" :class="{ 'hidden': isLoading }">
            <!-- Header -->
            <div class="print-header">
                <h1>Laporan Perpustakaan</h1>
                <p class="print-subtitle">Periode: {{ formatDateRange(startDate, endDate) }}</p>
            </div>

            <!-- Ringkasan -->
            <div class="summary-section">
                <div class="summary-box">
                    <h3>Total Buku</h3>
                    <p class="summary-value">{{ totalBooks }}</p>
                </div>
                <div class="summary-box">
                    <h3>Total Peminjaman</h3>
                    <p class="summary-value">{{ totalLoans }}</p>
                </div>
                <div class="summary-box">
                    <h3>Total Pengguna</h3>
                    <p class="summary-value">{{ totalUsers }}</p>
                </div>
            </div>

            <!-- Chart Section -->
            <div class="chart-section">
                <div class="chart-container">
                    <h2>Peminjam per Jurusan</h2>
                    <div class="chart-wrapper">
                        <!-- Menggunakan label bawaan dari Chart.js -->
                        <BarChart :chartData="loanChartData" :options="{
                            maintainAspectRatio: false,
                            responsive: true,
                            layout: {
                                padding: {
                                    bottom: 60,
                                    left: 10,
                                    right: 10
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: true },
                                datalabels: {
                                    display: false,
                                    align: 'end',
                                    anchor: 'end'
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true,
                                    precision: 0,
                                    ticks: { 
                                        font: { size: 12 },
                                        color: '#000000'
                                    }
                                },
                                x: {
                                    display: true,
                                    ticks: {
                                        font: { size: 12, weight: 'bold' },
                                        color: '#000000',
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 30,
                                        padding: 5
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            animation: false
                        }" />
                    </div>
                </div>
                <div class="chart-container">
                    <h2>Anggota per Jurusan</h2>
                    <div class="chart-wrapper">
                        <!-- Menggunakan label bawaan dari Chart.js -->
                        <BarChart :chartData="userChartData" :options="{
                            maintainAspectRatio: false,
                            responsive: true,
                            layout: {
                                padding: {
                                    bottom: 60,
                                    left: 10,
                                    right: 10
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { enabled: true },
                                datalabels: {
                                    display: false,
                                    align: 'end',
                                    anchor: 'end'
                                }
                            },
                            scales: {
                                y: { 
                                    beginAtZero: true,
                                    precision: 0,
                                    ticks: { 
                                        font: { size: 12 },
                                        color: '#000000'
                                    }
                                },
                                x: {
                                    display: true,
                                    ticks: {
                                        font: { size: 12, weight: 'bold' },
                                        color: '#000000',
                                        autoSkip: false,
                                        maxRotation: 45,
                                        minRotation: 30,
                                        padding: 5
                                    },
                                    grid: {
                                        display: false
                                    }
                                }
                            },
                            animation: false
                        }" />
                    </div>
                </div>
            </div>

            <!-- Table Peminjam -->
            <div class="table-section">
                <h2>Daftar Peminjam</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Peminjam</th>
                            <th>Judul Buku</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(loan, i) in (loans as Loan[])" :key="loan.id">
                            <td>{{ i + 1 }}</td>
                            <td>{{ loan.user?.name || '-' }}</td>
                            <td>{{ loan.book?.judul || '-' }}</td>
                            <td class="capitalize">{{ loan.status }}</td>
                        </tr>
                        <tr v-if="(loans as Loan[]).length === 0">
                            <td colspan="4" class="text-center">Tidak ada data peminjam.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Users -->
            <div class="table-section">
                <h2>Daftar Anggota</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Username</th>
                            <th>Jurusan</th>
                            <th>Jenis Kelamin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, i) in (users as User[])" :key="user.id">
                            <td>{{ i + 1 }}</td>
                            <td>{{ user.name }}</td>
                            <td>{{ user.username }}</td>
                            <td>{{ user.jurusan || '-' }}</td>
                            <td>{{ user.jenis_kelamin || '-' }}</td>
                        </tr>
                        <tr v-if="(users as User[]).length === 0">
                            <td colspan="5" class="text-center">Tidak ada data anggota.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="print-footer">
                <p>Dicetak pada: {{ new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: '2-digit', hour: '2-digit', minute: '2-digit' } as Intl.DateTimeFormatOptions) }}</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Style khusus untuk halaman cetak */
.print-container {
    max-width: 100%;
    margin: 0 auto;
    font-family: Arial, sans-serif;
    color: #000;
    background: #fff;
}

.loading-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #fff;
    z-index: 9999;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.print-controls {
    padding: 20px;
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}

.print-button {
    padding: 10px 20px;
    background: #4b72b0;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
}

.print-content {
    padding: 20px;
}

.hidden {
    display: none;
}

.print-header {
    text-align: center;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 2px solid #000;
}

.print-header h1 {
    font-size: 24px;
    margin: 0 0 5px 0;
}

.print-subtitle {
    font-size: 16px;
    margin: 5px 0;
}

.summary-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}

.summary-box {
    width: 30%;
    border: 1px solid #000;
    padding: 15px;
    text-align: center;
}

.summary-box h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
}

.summary-value {
    font-size: 24px;
    font-weight: bold;
    margin: 0;
}

.chart-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 30px;
}

.chart-container {
    width: 48%;
    border: 1px solid #000;
    padding: 15px;
    overflow: hidden;
}

.chart-container h2 {
    font-size: 18px;
    margin: 0 0 15px 0;
    text-align: center;
}

.chart-wrapper {
    height: 350px;
    width: 100%;
    max-width: 100%;
    position: relative;
    overflow: visible;
    margin-bottom: 50px;
}

/* Label chart yang ditampilkan secara manual - dinonaktifkan untuk sementara */
.chart-labels {
    display: none; /* sembunyikan label */
}

.chart-label {
    display: none; /* sembunyikan label */
}

/* Hapus label manual karena sekarang menggunakan label bawaan Chart.js */

.table-section {
    margin-bottom: 30px;
}

.table-section h2 {
    font-size: 18px;
    margin: 0 0 10px 0;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
}

.data-table th,
.data-table td {
    border: 1px solid #000;
    padding: 8px;
    text-align: left;
}

.data-table th {
    background-color: #f2f2f2;
    font-weight: bold;
}

.data-table tr:nth-child(even) {
    background-color: #f9f9f9;
}

.text-center {
    text-align: center;
}

.capitalize {
    text-transform: capitalize;
}

.print-footer {
    margin-top: 30px;
    padding-top: 10px;
    border-top: 1px solid #000;
    text-align: right;
    font-size: 12px;
}

/* Media queries untuk print */
@media print {
    @page {
        size: A4 portrait;
        margin: 10mm;
    }
    
    body {
        margin: 0;
        padding: 0;
        background: #fff;
    }
    
    .no-print {
        display: none !important;
    }
    
    .print-container {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 0;
        background: #fff;
    }
    
    .print-content {
        padding: 0;
    }
    
    .chart-section {
        page-break-after: always;
        width: 100%;
        display: flex;
        justify-content: space-between;
    }
    
    .chart-container {
        overflow: hidden;
        page-break-inside: avoid;
    }
    
    .chart-wrapper {
        max-height: 350px;
        height: 350px;
        overflow: visible;
        margin-bottom: 50px;
    }
    
    .chart-labels {
        display: none; /* sembunyikan label di mode cetak */
    }
    
    .chart-label {
        display: none; /* sembunyikan label di mode cetak */
    }
    
    .table-section {
        page-break-inside: avoid;
    }
    
    .data-table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    /* Sudah menggunakan label bawaan Chart.js */
}
</style> 