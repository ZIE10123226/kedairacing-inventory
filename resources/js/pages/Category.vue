<template>
    <div>
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold" style="color: #607456;">Master Kategori</h1>
                <p class="mt-1 text-sm text-gray-600">Manajemen kategori suku cadang.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                <button @click="openModal()" type="button"
                    class="inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm"
                    style="background-color: #607456;">
                    + Tambah Kategori
                </button>
            </div>
        </div>

        <div v-if="loadingPage" class="text-center py-10 text-gray-500">Memuat data...</div>

        <div v-else class="overflow-hidden rounded-xl shadow-sm border" style="border-color: #dfd0b8;">
            <table class="min-w-full divide-y" style="border-color: #dfd0b8;">
                <thead style="background-color: #607456;">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-6">Nama Kategori</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-white">Deskripsi</th>
                        <th class="px-3 py-3.5 text-center text-sm font-semibold text-white">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y bg-white" style="border-color: #EEE0CC;">
                    <tr v-for="item in categories" :key="item.id" class="hover:bg-amber-50 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-semibold text-gray-900 sm:pl-6">{{ item.name }}</td>
                        <td class="px-3 py-4 text-sm text-gray-600">{{ item.description }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-center text-sm">
                            <button @click="openModal(item)" class="font-medium mr-3 hover:underline" style="color: #607456;">Edit</button>
                            <button @click="deleteCategory(item.id)" class="font-medium hover:underline" style="color: #7B2525;">Hapus</button>
                        </td>
                    </tr>
                    <tr v-if="categories.length === 0">
                        <td colspan="3" class="px-6 py-10 text-center text-sm text-gray-400">Belum ada kategori. Klik tombol tambah.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Modal via Teleport -->
        <teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center">
                <div class="absolute inset-0 bg-black bg-opacity-50" @click="closeModal"></div>
                <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-md mx-4" style="z-index: 51;">
                    <form @submit.prevent="saveCategory">
                        <div class="px-6 pt-5 pb-3" style="background-color: #607456; border-radius: 12px 12px 0 0;">
                            <h3 class="text-lg font-bold text-white">{{ form.id ? 'Edit' : 'Tambah' }} Kategori</h3>
                        </div>
                        <div class="px-6 py-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                                <input v-model="form.name" type="text" required class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea v-model="form.description" rows="3" class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2"></textarea>
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

const categories = ref([]);
const showModal = ref(false);
const loadingPage = ref(true);
const saving = ref(false);
const form = ref({ id: null, name: '', description: '' });

const loadPageData = async () => {
    loadingPage.value = true;
    try {
        const res = await api.get('/categories');
        categories.value = res.data || [];
    } catch (e) {
        console.error('Gagal memuat kategori:', e);
    } finally {
        loadingPage.value = false;
    }
};

const openModal = (item = null) => {
    form.value = item ? { ...item } : { id: null, name: '', description: '' };
    showModal.value = true;
};

const closeModal = () => { showModal.value = false; };

const saveCategory = async () => {
    saving.value = true;
    try {
        if (form.value.id) {
            await api.put(`/admin/categories/${form.value.id}`, form.value);
        } else {
            await api.post('/admin/categories', form.value);
        }
        closeModal();
        await loadPageData();
    } catch (e) {
        const errors = e.response?.data?.errors;
        alert(errors ? Object.values(errors).flat().join('\n') : (e.response?.data?.message || 'Gagal menyimpan'));
    } finally {
        saving.value = false;
    }
};

const deleteCategory = async (id) => {
    if (confirm('Yakin ingin menghapus kategori ini?')) {
        try {
            await api.delete(`/admin/categories/${id}`);
            await loadPageData();
        } catch (e) {
            alert(e.response?.data?.message || 'Gagal menghapus');
        }
    }
};

onMounted(loadPageData);
</script>
