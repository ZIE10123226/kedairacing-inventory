<template>
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Daftar Karyawan Baru
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Pendaftaran akan dikaji oleh Admin sebelum Anda bisa Login.
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10 border border-gray-100">
                <form class="space-y-6" @submit.prevent="handleRegister">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input v-model="form.name" type="text" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Alamat Email</label>
                        <input v-model="form.email" type="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input v-model="form.password" type="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                        <input v-model="form.password_confirmation" type="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary sm:text-sm" />
                    </div>

                    <div v-if="error" class="text-red-500 text-sm text-center bg-red-50 p-2 rounded">
                        {{ error }}
                    </div>
                    <div v-if="successMsg" class="text-green-600 text-sm text-center bg-green-50 p-2 rounded">
                        {{ successMsg }}
                    </div>

                    <div>
                        <button type="submit" :disabled="loading" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            {{ loading ? 'Mendaftar...' : 'Daftar Sekarang' }}
                        </button>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <router-link to="/login" class="text-sm text-primary hover:text-blue-900 font-medium">Sudah punya akun? Login di sini</router-link>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const form = ref({ name: '', email: '', password: '', password_confirmation: '' });
const error = ref('');
const successMsg = ref('');
const loading = ref(false);

const handleRegister = async () => {
    loading.value = true;
    error.value = '';
    successMsg.value = '';
    try {
        const response = await authStore.register(form.value);
        successMsg.value = response.message;
        form.value = { name: '', email: '', password: '', password_confirmation: '' };
    } catch (err) {
        error.value = err;
    } finally {
        loading.value = false;
    }
};
</script>
