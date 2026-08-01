<template>
    <div>
        <div class="sm:flex sm:items-center mb-6 no-print">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold text-gray-900">Laporan Transaksi</h1>
                <p class="mt-2 text-sm text-gray-700">Filter dan export laporan barang masuk dan keluar.</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-100 mb-6 no-print">
            <form @submit.prevent="fetchReport" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Transaksi</label>
                    <select v-model="filter.type" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="masuk">Barang Masuk</option>
                        <option value="keluar">Barang Keluar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Periode</label>
                    <select v-model="filter.period" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                        <option value="harian">Harian</option>
                        <option value="bulanan">Bulanan</option>
                        <option value="tahunan">Tahunan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal/Waktu</label>
                    <input v-if="filter.period === 'harian'" v-model="filter.date" type="date" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    <input v-if="filter.period === 'bulanan'" v-model="filter.date" type="month" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                    <input v-if="filter.period === 'tahunan'" v-model="filter.date" type="number" placeholder="YYYY" required min="2000" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm">
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-600">
                        Tampilkan
                    </button>
                    <button @click="printReport" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">
                        Cetak PDF
                    </button>
                </div>
            </form>
        </div>

        <div id="printArea" class="bg-white p-8 border border-gray-200" v-if="reportData">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold uppercase">{{ reportData.title }}</h2>
                <p class="text-gray-600">Periode: {{ filter.date }}</p>
            </div>
            
            <table class="min-w-full divide-y divide-gray-300 border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-4 py-2 text-left text-sm font-semibold text-gray-900">Tanggal</th>
                        <th class="border px-4 py-2 text-left text-sm font-semibold text-gray-900">No Transaksi</th>
                        <th class="border px-4 py-2 text-left text-sm font-semibold text-gray-900">Barang</th>
                        <th class="border px-4 py-2 text-left text-sm font-semibold text-gray-900">Jumlah</th>
                        <th class="border px-4 py-2 text-left text-sm font-semibold text-gray-900" v-if="filter.type === 'masuk'">Supplier</th>
                        <th class="border px-4 py-2 text-left text-sm font-semibold text-gray-900" v-if="filter.type === 'keluar'">Keterangan</th>
                        <th class="border px-4 py-2 text-left text-sm font-semibold text-gray-900">Pencatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="row in reportData.data" :key="row.id">
                        <td class="border px-4 py-2 text-sm text-gray-900">{{ row.tanggal }}</td>
                        <td class="border px-4 py-2 text-sm text-gray-900">{{ row.nomor_transaksi }}</td>
                        <td class="border px-4 py-2 text-sm text-gray-900">{{ row.sparepart?.nama_barang }}</td>
                        <td class="border px-4 py-2 text-sm text-gray-900 font-semibold">{{ row.jumlah }}</td>
                        <td class="border px-4 py-2 text-sm text-gray-900" v-if="filter.type === 'masuk'">{{ row.supplier?.nama }}</td>
                        <td class="border px-4 py-2 text-sm text-gray-900" v-if="filter.type === 'keluar'">{{ row.keterangan }}</td>
                        <td class="border px-4 py-2 text-sm text-gray-900">{{ row.user?.name }}</td>
                    </tr>
                    <tr v-if="reportData.data.length === 0">
                        <td :colspan="filter.type === 'masuk' ? 7 : 6" class="border px-4 py-2 text-center text-gray-500">
                            Tidak ada transaksi pada periode ini.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import api from '../services/api';

const filter = ref({
    type: 'masuk',
    period: 'harian',
    date: new Date().toISOString().split('T')[0]
});

const reportData = ref(null);

const fetchReport = async () => {
    try {
        const res = await api.get('/reports', { params: filter.value });
        reportData.value = res.data;
    } catch (e) {
        alert('Gagal mengambil laporan');
    }
};

const printReport = () => {
    window.print();
};
</script>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .no-print {
        display: none !important;
    }
}
</style>
