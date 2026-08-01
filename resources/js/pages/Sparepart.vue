<template>
    <div>
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold" style="color: #607456;">Master Sparepart</h1>
                <p class="mt-1 text-sm text-gray-600">Daftar semua suku cadang yang tersedia di bengkel.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none" v-if="authStore.isAdmin">
                <button @click="openModal()" type="button"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm"
                    style="background-color: #607456;">
                    + Tambah Sparepart
                </button>
            </div>
        </div>

        <div class="mb-4">
            <input v-model="searchQuery" @input="fetchSpareparts" type="text"
                placeholder="Cari kode atau nama barang..."
                class="w-full md:w-1/3 px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2"
                style="border-color: #dfd0b8;">
        </div>

        <div v-if="loadingPage" class="text-center py-10 text-gray-500">Memuat data...</div>

        <div v-else class="overflow-hidden rounded-xl shadow-sm border" style="border-color: #dfd0b8;">
            <table class="min-w-full divide-y" style="border-color: #dfd0b8;">
                <thead style="background-color: #607456;">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Kode</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Nama Barang</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Kategori</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Stok</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Harga Jual</th>
                        <th v-if="authStore.isAdmin" class="px-3 py-3.5 text-center text-sm font-semibold text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y bg-white" style="border-color: #EEE0CC;">
                    <tr v-for="item in spareparts" :key="item.id" class="hover:bg-amber-50 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ item.kode_barang }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-700">{{ item.nama_barang }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">{{ item.category?.name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-bold"
                            :style="item.stok <= item.stok_minimum ? 'color: #7B2525;' : 'color: #607456;'">
                            {{ item.stok }}
                            <span v-if="item.stok <= item.stok_minimum" class="ml-1 text-xs">⚠️ Menipis</span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">Rp {{ Number(item.harga_jual).toLocaleString('id-ID') }}</td>
                        <td v-if="authStore.isAdmin" class="whitespace-nowrap px-3 py-4 text-center text-sm">
                            <button @click="openModal(item)" class="font-medium mr-3 hover:underline" style="color: #607456;">Edit</button>
                            <button @click="deleteSparepart(item.id)" class="font-medium hover:underline" style="color: #7B2525;">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="spareparts.length === 0">
                        <td :colspan="authStore.isAdmin ? 6 : 5" class="px-6 py-10 text-center text-sm text-gray-400">Data tidak ditemukan.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal via Teleport -->
        <teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-lg mx-4" style="z-index: 51;">
                    <form @submit.prevent="saveSparepart">
                        <div class="px-6 pt-5 pb-3" style="background-color: #607456; border-radius: 12px 12px 0 0;">
                            <h3 class="text-lg font-bold text-white">{{ form.id ? 'Edit' : 'Tambah' }} Sparepart</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4 max-h-[70vh] overflow-y-auto">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode Barang</label>
                                    <input v-model="form.kode_barang" type="text" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                                    <select v-model="form.kategori_id" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                        <option value="">-- Pilih Kategori --</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                                <select v-model="form.supplier_id" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                    <option value="">-- Pilih Supplier --</option>
                                    <option v-for="sup in suppliers" :key="sup.id" :value="sup.id">{{ sup.nama }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                                <input v-model="form.nama_barang" type="text" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea v-model="form.deskripsi" rows="2" class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Awal</label>
                                    <input v-model.number="form.stok" type="number" min="0" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Stok Minimum</label>
                                    <input v-model.number="form.stok_minimum" type="number" min="0" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli (Rp)</label>
                                    <input v-model.number="form.harga_beli" type="number" min="0" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Jual (Rp)</label>
                                    <input v-model.number="form.harga_jual" type="number" min="0" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                                </div>
                            </div>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end space-x-3">
                            <button @click="closeModal" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Batal</button>
                            <button type="submit" :disabled="saving" class="px-4 py-2 text-sm font-medium text-white rounded-lg disabled:opacity-50" style="background-color: #607456;">
                                {{ saving ? 'Menyimpan...' : 'Simpan' }}
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
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const spareparts = ref([]);
const categories = ref([]);
const suppliers = ref([]);
const searchQuery = ref('');
const showModal = ref(false);
const loadingPage = ref(true);
const saving = ref(false);
const form = ref({ id: null, kode_barang: '', nama_barang: '', deskripsi: '', kategori_id: '', supplier_id: '', stok: 0, stok_minimum: 5, harga_beli: 0, harga_jual: 0 });

const fetchSpareparts = async () => {
    try {
        const res = await api.get('/spareparts', { params: { search: searchQuery.value } });
        spareparts.value = res.data.data || res.data || [];
    } catch (e) {
        console.error('Error fetching spareparts:', e);
    }
};

const loadPageData = async () => {
    loadingPage.value = true;
    try {
        const [resSP, resCat, resSup] = await Promise.all([
            api.get('/spareparts'),
            api.get('/categories'),
            api.get('/suppliers')
        ]);
        spareparts.value = resSP.data.data || resSP.data || [];
        categories.value = resCat.data || [];
        suppliers.value = resSup.data || [];
    } catch (e) {
        console.error('Gagal memuat data:', e);
    } finally {
        loadingPage.value = false;
    }
};

const openModal = (item = null) => {
    if (item) {
        form.value = { ...item, kategori_id: item.kategori_id, supplier_id: item.supplier_id };
    } else {
        form.value = { id: null, kode_barang: '', nama_barang: '', deskripsi: '', kategori_id: '', supplier_id: '', stok: 0, stok_minimum: 5, harga_beli: 0, harga_jual: 0 };
    }
    showModal.value = true;
};

const closeModal = () => { showModal.value = false; };

const saveSparepart = async () => {
    saving.value = true;
    try {
        if (form.value.id) {
            await api.put(`/admin/spareparts/${form.value.id}`, form.value);
        } else {
            await api.post('/admin/spareparts', form.value);
        }
        closeModal();
        await loadPageData();
    } catch (e) {
        const errors = e.response?.data?.errors;
        if (errors) {
            alert(Object.values(errors).flat().join('\n'));
        } else {
            alert(e.response?.data?.message || 'Gagal menyimpan data');
        }
    } finally {
        saving.value = false;
    }
};

const deleteSparepart = async (id) => {
    if (confirm('Yakin ingin menghapus sparepart ini?')) {
        try {
            await api.delete(`/admin/spareparts/${id}`);
            await loadPageData();
        } catch (e) {
            alert(e.response?.data?.message || 'Gagal menghapus data');
        }
    }
};

onMounted(loadPageData);
</script>
