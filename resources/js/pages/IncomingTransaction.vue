<template>
    <div>
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold" style="color: #607456;">Barang Masuk</h1>
                <p class="mt-1 text-sm text-gray-600">Catat persediaan suku cadang yang baru masuk dari supplier.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <button @click="openModal" type="button"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors"
                    style="background-color: #607456;"
                    onmouseover="this.style.backgroundColor='#4e5f45'"
                    onmouseout="this.style.backgroundColor='#607456'">
                    + Input Barang Masuk
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
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Supplier</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Sparepart</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Jumlah</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Harga Beli</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Pencatat</th>
                    </tr>
                </thead>
                <tbody class="divide-y bg-white" style="border-color: #EEE0CC;">
                    <tr v-for="item in transactions" :key="item.id" class="hover:bg-amber-50 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ item.nomor_transaksi }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.tanggal }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.supplier?.nama }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.sparepart?.nama_barang }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-bold" style="color: #607456;">+{{ item.jumlah }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">Rp {{ Number(item.harga_beli).toLocaleString('id-ID') }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.user?.name }}</td>
                    </tr>
                    <tr v-if="transactions.length === 0">
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">Belum ada data transaksi barang masuk.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal via Teleport agar tidak terpengaruh CSS parent -->
        <teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4" style="z-index: 51;">
                    <form @submit.prevent="saveTransaction">
                        <div class="px-6 pt-5 pb-2" style="background-color: #607456; border-radius: 12px 12px 0 0;">
                            <h3 class="text-lg font-bold text-white mb-1">Input Barang Masuk</h3>
                            <p class="text-xs text-green-100 mb-4">Stok akan otomatis bertambah setelah disimpan</p>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Transaksi</label>
                                <input v-model="form.nomor_transaksi" type="text" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                                <input v-model="form.tanggal" type="date" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                <select v-model="form.supplier_id" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                    <option value="">-- Pilih Supplier --</option>
                                    <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.nama }}</option>
                                </select>
                                <p v-if="suppliers.length === 0" class="text-xs text-red-500 mt-1">Belum ada supplier. Tambah di menu Master Supplier dulu.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sparepart</label>
                                <select v-model="form.sparepart_id" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                    <option value="">-- Pilih Sparepart --</option>
                                    <option v-for="sp in spareparts" :key="sp.id" :value="sp.id">{{ sp.kode_barang }} - {{ sp.nama_barang }}</option>
                                </select>
                                <p v-if="spareparts.length === 0" class="text-xs text-red-500 mt-1">Belum ada sparepart. Tambah di menu Master Sparepart dulu.</p>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Masuk</label>
                                    <input v-model.number="form.jumlah" type="number" min="1" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp)</label>
                                    <input v-model.number="form.harga_beli" type="number" min="0" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                </div>
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
const suppliers = ref([]);
const showModal = ref(false);
const loading = ref(false);
const loadingPage = ref(true);
const form = ref({
    nomor_transaksi: '',
    tanggal: '',
    supplier_id: '',
    sparepart_id: '',
    jumlah: 1,
    harga_beli: 0
});

const loadPageData = async () => {
    loadingPage.value = true;
    try {
        const [resTrx, resSP, resSup] = await Promise.all([
            api.get('/incoming-transactions'),
            api.get('/spareparts'),
            api.get('/suppliers')
        ]);
        transactions.value = resTrx.data.data || [];
        spareparts.value = resSP.data.data || resSP.data || [];
        suppliers.value = resSup.data.data || resSup.data || [];
    } catch (e) {
        console.error('Gagal memuat halaman barang masuk:', e);
    } finally {
        loadingPage.value = false;
    }
};

const openModal = () => {
    form.value = {
        nomor_transaksi: 'TRX-IN-' + Date.now().toString().slice(-6),
        tanggal: new Date().toISOString().split('T')[0],
        supplier_id: '',
        sparepart_id: '',
        jumlah: 1,
        harga_beli: 0
    };
    showModal.value = true;
};

const closeModal = () => { showModal.value = false; };

const saveTransaction = async () => {
    loading.value = true;
    try {
        await api.post('/incoming-transactions', form.value);
        closeModal();
        await loadPageData();
        alert('Transaksi berhasil disimpan. Stok otomatis bertambah.');
    } catch (e) {
        alert(e.response?.data?.message || 'Gagal menyimpan transaksi');
    } finally {
        loading.value = false;
    }
};

onMounted(loadPageData);
</script>
