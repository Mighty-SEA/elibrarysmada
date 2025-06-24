<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { BarChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';

// Pastikan Chart.js teregistrasi dengan benar
Chart.register(...registerables);

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
const showMonthlyCharts = page.props.showMonthlyCharts ?? false;
const loanChartMonthly = page.props.loanChartMonthly || {} as Record<string, string>;
const userChartMonthly = page.props.userChartMonthly || {} as Record<string, string>;

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

// Data chart bulanan
const loanChartMonthlyLabels = Object.keys(loanChartMonthly);
const userChartMonthlyLabels = Object.keys(userChartMonthly);

const loanChartMonthlyValues = Object.values(loanChartMonthly).map(value => {
    const numValue = parseInt(value as string);
    return isNaN(numValue) ? 0 : numValue;
});

const userChartMonthlyValues = Object.values(userChartMonthly).map(value => {
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
            backgroundColor: '#4b72b0'
        },
    ]
} as any;

const userChartData = {
    labels: userChartLabels,
    datasets: [
        {
            label: 'Jumlah User',
            data: userChartValues,
            backgroundColor: '#c97b63'
        },
    ]
} as any;

const loanChartMonthlyData = {
    labels: loanChartMonthlyLabels,
    datasets: [
        {
            label: 'Jumlah Peminjam per Bulan',
            data: loanChartMonthlyValues,
            backgroundColor: '#10b981'
        },
    ]
} as any;

const userChartMonthlyData = {
    labels: userChartMonthlyLabels,
    datasets: [
        {
            label: 'Jumlah User per Bulan',
            data: userChartMonthlyValues,
            backgroundColor: '#8b5cf6'
        },
    ]
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
                // Gunakan try-catch untuk mengatasi error window
                try {
                    // Hanya jalankan jika dalam browser (bukan SSR)
                    if (typeof window !== 'undefined') {
                        window.print();
                        
                        // Kembali ke dashboard setelah print dialog muncul
                        setTimeout(() => {
                            if (typeof window !== 'undefined') {
                                window.location.href = '/admin/dashboard';
                            }
                        }, 1000);
                    }
                } catch (error) {
                    console.error('Print error:', error);
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
                <div v-if="showMonthlyCharts" class="chart-grid">
                    <div class="chart-container">
                        <h2>Peminjam per Bulan</h2>
                        <div class="chart-wrapper">
                            <BarChart :chartData="loanChartMonthlyData" :options="{
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: false,
                                animations: {
                                    colors: false,
                                    x: false,
                                    y: false
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 0
                                        }
                                    }
                                },
                                scales: {
                                    y: { 
                                        beginAtZero: true
                                    }
                                }
                            }" />
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <h2>User Baru per Bulan</h2>
                        <div class="chart-wrapper">
                            <BarChart :chartData="userChartMonthlyData" :options="{
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: false,
                                animations: {
                                    colors: false,
                                    x: false,
                                    y: false
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 0
                                        }
                                    }
                                },
                                scales: {
                                    y: { 
                                        beginAtZero: true
                                    }
                                }
                            }" />
                        </div>
                    </div>
                </div>
                
                <div class="chart-grid">                    
                    <div class="chart-container">
                        <h2>Peminjam per Jurusan</h2>
                        <div class="chart-wrapper">
                            <!-- Menggunakan label bawaan dari Chart.js -->
                            <BarChart :chartData="loanChartData" :options="{
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: false,
                                animations: {
                                    colors: false,
                                    x: false,
                                    y: false
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 0
                                        }
                                    }
                                },
                                scales: {
                                    y: { 
                                        beginAtZero: true
                                    }
                                }
                            }" />
                        </div>
                    </div>
                    
                    <div class="chart-container">
                        <h2>Anggota per Jurusan</h2>
                        <div class="chart-wrapper">
                            <!-- Menggunakan label bawaan dari Chart.js -->
                            <BarChart :chartData="userChartData" :options="{
                                responsive: true,
                                maintainAspectRatio: false,
                                animation: false,
                                animations: {
                                    colors: false,
                                    x: false,
                                    y: false
                                },
                                transitions: {
                                    active: {
                                        animation: {
                                            duration: 0
                                        }
                                    }
                                },
                                scales: {
                                    y: { 
                                        beginAtZero: true
                                    }
                                }
                            }" />
                        </div>
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

<style>
.print-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
    background-color: #fff;
    font-family: Arial, Helvetica, sans-serif;
}

.loading-container {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-color: rgba(255, 255, 255, 0.9);
    z-index: 9999;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    animation: spin 1s linear infinite;
    margin-bottom: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.print-controls {
    text-align: center;
    margin-bottom: 20px;
}

.print-button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.print-button:hover {
    background-color: #45a049;
}

.hidden {
    display: none;
}

.print-content {
    font-family: Arial, sans-serif;
    color: #333;
    margin-bottom: 20px;
}

.print-header {
    text-align: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1.5px solid #000;
}

.print-header h1 {
    font-size: 20px;
    font-weight: bold;
    margin: 0 0 5px 0;
}

.print-subtitle {
    font-size: 14px;
    margin: 5px 0;
}

.summary-section {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.summary-box {
    width: 30%;
    border: 1px solid #000;
    padding: 8px;
    text-align: center;
}

.summary-box h3 {
    margin: 0 0 8px 0;
    font-size: 14px;
    font-weight: normal;
}

.summary-value {
    font-size: 18px;
    font-weight: bold;
    margin: 0;
}

.chart-section {
    margin-bottom: 15px;
}

.chart-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
    margin-bottom: 15px;
}

.chart-container {
    border: 1px solid #000;
    padding: 6px;
    display: flex;
    flex-direction: column;
}

.chart-container h2 {
    font-size: 14px;
    margin: 0 0 8px 0;
    text-align: center;
    font-weight: normal;
}

.chart-wrapper {
    height: 350px;
    flex-grow: 1;
    display: flex;
    justify-content: center;
}

.chart-wrapper > div {
    height: 100% !important;
    width: 100% !important;
}

.chart-wrapper canvas {
    height: 100% !important;
}

.table-section {
    margin-bottom: 15px;
}

.table-section h2 {
    font-size: 15px;
    margin: 0 0 8px 0;
    font-weight: bold;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
    border: 1px solid #000;
}

.data-table th,
.data-table td {
    border: 1px solid #000;
    padding: 6px;
    text-align: left;
    font-size: 12px;
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
    margin-top: 15px;
    padding-top: 5px;
    border-top: 1px solid #000;
    text-align: right;
    font-size: 10px;
}

/* Page break class untuk mode normal (non-print) */
.page-break-before {
    margin-top: 50px;
    border-top: 1px dashed #ccc;
    padding-top: 30px;
}

/* Media queries untuk print */
@media print {
    @page {
        size: A4 portrait;
        margin: 7mm;
    }
    
    body {
        margin: 0;
        padding: 0;
        background: #fff;
        font-family: Arial, Helvetica, sans-serif;
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
    
    .chart-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
        page-break-inside: avoid;
        margin-bottom: 20px;
    }
    
    .chart-container {
        page-break-inside: avoid;
        width: 100%;
        border: 1px solid #000;
        padding: 6px;
        display: flex;
        flex-direction: column;
    }
    
    .chart-wrapper {
        height: 350px;
        flex-grow: 1;
        display: flex;
        justify-content: center;
    }
    
    .chart-wrapper > div {
        height: 100% !important;
        width: 100% !important;
    }
    
    .chart-wrapper canvas {
        height: 100% !important;
    }
    
    /* Pastikan semua konten masuk dalam satu halaman */
    .print-header, 
    .summary-section,
    .chart-section {
        page-break-inside: avoid;
        margin-bottom: 15px;
    }
    
    .table-section {
        page-break-inside: avoid;
    }

    /* Kembali aktifkan tabel untuk cetak */
    .data-table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
}

/* Tambahan CSS untuk chart size */
.chart-section canvas {
    max-width: 100%;
}
</style> 