<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { BarChart } from 'vue-chart-3';
import { Chart, registerables } from 'chart.js';
// import html2pdf from 'html2pdf.js';
const html2pdf = window.html2pdf;
Chart.register(...registerables);

const page = usePage();
const totalBooks = page.props.totalBooks ?? 0;
const totalLoans = page.props.totalLoans ?? 0;
const totalUsers = page.props.totalUsers ?? 0;
const loanChart = page.props.loanChart || {};
const userChart = page.props.userChart || {};
const loans = page.props.loans || [];
const users = page.props.users || [];

function formatLabel(label: string) {
    const [jk, ...jurusanArr] = label.split(' ');
    const jkLabel = jk === '-' ? 'Tidak Diketahui' : (jk === 'Laki-laki' ? 'L' : (jk === 'Perempuan' ? 'P' : jk));
    const jurusanLabel = jurusanArr.join(' ') === '-' ? 'Tidak Diketahui' : jurusanArr.join(' ');
    return jurusanLabel + ' - ' + jkLabel;
}

const loanChartLabels = Object.keys(loanChart).map(formatLabel);
const userChartLabels = Object.keys(userChart).map(formatLabel);

const loanChartData = {
    labels: loanChartLabels,
    datasets: [
        {
            label: 'Jumlah Peminjam',
            data: Object.values(loanChart).map(v => parseInt(v)),
            backgroundColor: '#2563eb',
        },
    ],
};

const userChartData = {
    labels: userChartLabels,
    datasets: [
        {
            label: 'Jumlah User',
            data: Object.values(userChart).map(v => parseInt(v)),
            backgroundColor: '#f59e42',
        },
    ],
};

function formatDateRange(start, end) {
    if (!start && !end) return 'Semua Data';
    const opts = { year: 'numeric', month: 'long', day: '2-digit' };
    const s = start ? new Date(start).toLocaleDateString('id-ID', opts) : '';
    const e = end ? new Date(end).toLocaleDateString('id-ID', opts) : '';
    if (s && e) return `${s} s/d ${e}`;
    if (s) return `Mulai ${s}`;
    if (e) return `Sampai ${e}`;
    return '';
}
const startDate = page.props.startDate || '';
const endDate = page.props.endDate || '';

function downloadPDF() {
    console.log('Tombol download diklik');
    const element = document.getElementById('dashboard-export-area');
    console.log('element', element, 'html2pdf', html2pdf);
    if (element && typeof html2pdf === 'function') {
        html2pdf()
            .set({
                margin: 0.5,
                filename: 'dashboard.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'portrait' }
            })
            .from(element)
            .save()
            .then(() => { console.log('PDF download triggered'); });
    } else {
        alert('html2pdf atau element tidak ditemukan!');
    }
}
</script>

<template>
    <div class="p-6 bg-white min-h-screen">
        <div class="mb-4 flex justify-end">
            <button @click="downloadPDF" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md shadow hover:bg-red-700 transition">
                Download PDF
            </button>
        </div>
        <div id="dashboard-export-area">
            <h1 class="text-2xl font-bold text-gray-900 mb-4">
                Laporan - {{ formatDateRange(startDate, endDate) }}
            </h1>
            <div class="grid gap-4 grid-cols-1 md:grid-cols-3 mb-6">
                <div class="bg-gray-100 rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col items-center">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Total Buku</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ totalBooks }}</p>
                </div>
                <div class="bg-gray-100 rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col items-center">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Total Peminjaman</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ totalLoans }}</p>
                </div>
                <div class="bg-gray-100 rounded-xl border border-gray-200 p-4 shadow-sm flex flex-col items-center">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Total Pengguna</h3>
                    <p class="text-3xl font-bold text-gray-900">{{ totalUsers }}</p>
                </div>
            </div>
            <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col items-center justify-center min-h-[220px] w-full">
                    <h2 class="text-lg font-semibold mb-2">Peminjam</h2>
                    <BarChart :chartData="loanChartData" :options="{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,precision:0}}}" style="width:100%;max-width:500px" />
                </div>
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col items-center justify-center min-h-[220px] w-full">
                    <h2 class="text-lg font-semibold mb-2">Anggota</h2>
                    <BarChart :chartData="userChartData" :options="{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,precision:0}}}" style="width:100%;max-width:500px" />
                </div>
            </div>
            <!-- Tabel Daftar Peminjam -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-2">Daftar Peminjam</h2>
                <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 border-b">No</th>
                            <th class="px-3 py-2 border-b">Nama Peminjam</th>
                            <th class="px-3 py-2 border-b">Judul Buku</th>
                            <th class="px-3 py-2 border-b">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(loan, i) in loans" :key="loan.id">
                            <td class="px-3 py-2 border-b">{{ i + 1 }}</td>
                            <td class="px-3 py-2 border-b">{{ loan.user?.name || '-' }}</td>
                            <td class="px-3 py-2 border-b">{{ loan.book?.judul || '-' }}</td>
                            <td class="px-3 py-2 border-b capitalize">{{ loan.status }}</td>
                        </tr>
                        <tr v-if="loans.length === 0">
                            <td colspan="4" class="text-center py-4 text-gray-500">Tidak ada data peminjam.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Tabel Daftar Anggota -->
            <div class="mt-8">
                <h2 class="text-lg font-semibold mb-2">Daftar Anggota</h2>
                <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 border-b">No</th>
                            <th class="px-3 py-2 border-b">Nama</th>
                            <th class="px-3 py-2 border-b">Username</th>
                            <th class="px-3 py-2 border-b">Jurusan</th>
                            <th class="px-3 py-2 border-b">Jenis Kelamin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(user, i) in users" :key="user.id">
                            <td class="px-3 py-2 border-b">{{ i + 1 }}</td>
                            <td class="px-3 py-2 border-b">{{ user.name }}</td>
                            <td class="px-3 py-2 border-b">{{ user.username }}</td>
                            <td class="px-3 py-2 border-b">{{ user.jurusan || '-' }}</td>
                            <td class="px-3 py-2 border-b">{{ user.jenis_kelamin || '-' }}</td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="5" class="text-center py-4 text-gray-500">Tidak ada data anggota.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template> 