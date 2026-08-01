<template>
    <div>
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold" style="color: #607456;">Barang Keluar</h1>
                <p class="mt-1 text-sm text-gray-600">Catat pengeluaran atau penjualan suku cadang bengkel.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <button @click="openModal" type="button"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors"
                    style="background-color: #607456;"
                    onmouseover="this.style.backgroundColor='#4e5f45'"
                    onmouseout="this.style.backgroundColor='#607456'">
                    + Input Barang Keluar
                </button>
            </div>
        </div>

        <div v-if="loadingPage" class="text-center py-10 text-gray-500">Memuat data...</div>

        <div v-else class="overflow-hidden rounded-xl shadow-sm border" style="border-color: #dfd0b8;">
            <table class="min-w-full divide-y" style="border-color: #dfd0b8;">
                <thead style="background-color: #607456;">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">No Transaksi</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Tanggal</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Sparepart</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Jumlah</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Keterangan</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Pencatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y bg-white" style="border-color: #EEE0CC;">
                    <tr v-for="item in transactions" :key="item.id" class="hover:bg-amber-50 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ item.nomor_transaksi }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.tanggal }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.sparepart?.nama_barang }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-bold" style="color: #7B2525;">-{{ item.jumlah }}</td>
                        <td class="px-3 py-4 text-sm text-gray-600">{{ item.keterangan }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.user?.name }}</td>
                    </tr>
                    <tr v-if="transactions.length === 0">
                        <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-400">Belum ada data transaksi barang keluar.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal (via Teleport ke body agar tidak terpengaruh CSS parent) -->
        <teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4" style="z-index: 51;">
                    <form @submit.prevent="saveTransaction">
                        <div class="px-6 pt-5 pb-2" style="background-color: #607456; border-radius: 12px 12px 0 0;">
                            <h3 class="text-lg font-bold text-white mb-1">Input Barang Keluar</h3>
                            <p class="text-xs text-green-100 mb-4">Stok akan otomatis berkurang setelah disimpan</p>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Transaksi</label>
                                <input v-model="form.nomor_transaksi" type="text" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2" style="focus:ring-color: #607456;">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input v-model="form.tanggal" type="date" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sparepart</label>
                                <select v-model="form.sparepart_id" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                    <option value="">-- Pilih Sparepart --</option>
                                    <option v-for="sp in spareparts" :key="sp.id" :value="sp.id">
                                        {{ sp.kode_barang }} - {{ sp.nama_barang }} (Stok: {{ sp.stok }})
                                    </option>
                                </select>
                                <p v-if="spareparts.length === 0" class="text-xs text-red-500 mt-1">Belum ada sparepart tersedia.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Keluar</label>
                                <input v-model.number="form.jumlah" type="number" min="1" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Mekanik / Tujuan)</label>
                                <textarea v-model="form.keterangan" rows="2" class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2"></textarea>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                            <button @click="closeModal" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Batal</button>
                            <button type="submit" :disabled="loading" class="px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors disabled:opacity-50"
                                style="background-color: #607456;"
                                onmouseover="this.style.backgroundColor='#4e5f45'"
                                onmouseout="this.style.backgroundColor='#607456'">
                                {{ loading ? 'Menyimpan...' : 'Simpan Transaksi' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </teleport>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const transactions = ref([]);
const spareparts = ref([]);
const showModal = ref(false);
const loading = ref(false);
const loadingPage = ref(true);
const form = ref({ nomor_transaksi: '', tanggal: '', sparepart_id: '', jumlah: 1, keterangan: '' });

const loadPageData = async () => {
    loadingPage.value = true;
    try {
        const [resTrx, resSP] = await Promise.all([
            api.get('/outgoing-transactions'),
            api.get('/spareparts')
        ]);
        transactions.value = resTrx.data.data || [];
        spareparts.value = resSP.data.data || resSP.data || [];
    } catch (e) {
        console.error('Gagal memuat halaman barang keluar:', e);
    } finally {
        loadingPage.value = false;
    }
};

const openModal = () => {
    form.value = {
        nomor_transaksi: 'TRX-OUT-' + Date.now().toString().slice(-6),
        tanggal: new Date().toISOString().split('T')[0],
        sparepart_id: '', jumlah: 1, keterangan: ''
    };
    showModal.value = true;
};

const closeModal = () => { showModal.value = false; };

const saveTransaction = async () => {
    loading.value = true;
    try {
        await api.post('/outgoing-transactions', form.value);
        closeModal();
        await loadPageData();
        alert('Transaksi berhasil disimpan. Stok otomatis berkurang.');
    } catch (e) {
        alert(e.response?.data?.message || 'Gagal menyimpan transaksi. Pastikan stok mencukupi.');
    } finally {
        loading.value = false;
    }
};

onMounted(loadPageData);
</script>
