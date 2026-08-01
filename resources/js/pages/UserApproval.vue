<template>
    <div>
        <div class="sm:flex sm:items-center mb-6">
            <div class="sm:flex-auto">
                <h1 class="text-2xl font-bold text-gray-900">Persetujuan Akun</h1>
                <p class="mt-2 text-sm text-gray-700">Daftar karyawan baru yang mendaftar dan menunggu persetujuan akses sistem.</p>
            </div>
        </div>

        <div class="mt-4 overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
            <table class="min-w-full divide-y divide-gray-300">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nama Lengkap</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tanggal Daftar</th>
                        <th class="relative py-3.5 pl-3 pr-4 sm:pr-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="user in pendingUsers" :key="user.id">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ user.name }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user.email }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ new Date(user.created_at).toLocaleString('id-ID') }}</td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-center text-sm font-medium sm:pr-6">
                            <button @click="approveUser(user.id)" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md mr-2">Terima</button>
                            <button @click="rejectUser(user.id)" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded-md">Tolak</button>
                        </td>
                    </tr>
                    <tr v-if="pendingUsers.length === 0">
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada antrean pendaftar.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';

const pendingUsers = ref([]);

const fetchPendingUsers = async () => {
    try {
        const res = await api.get('/admin/approvals');
        pendingUsers.value = res.data;
    } catch (e) {
        console.error('Failed to load pending users');
    }
};

const approveUser = async (id) => {
    if (confirm('Setujui akses karyawan ini?')) {
        await api.put(`/admin/approvals/${id}/approve`);
        fetchPendingUsers();
    }
};

const rejectUser = async (id) => {
    if (confirm('Tolak dan hapus pendaftaran karyawan ini?')) {
        await api.delete(`/admin/approvals/${id}/reject`);
        fetchPendingUsers();
    }
};

onMounted(fetchPendingUsers);
</script>
