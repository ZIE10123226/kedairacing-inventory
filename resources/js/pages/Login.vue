<template>
    <div class="min-h-screen flex" style="background-color: #EEE0CC;">
        <!-- Left decorative panel -->
        <div class="hidden lg:flex lg:w-1/2 flex-col items-center justify-center relative overflow-hidden" style="background-color: #607456;">
            <!-- Background image kedaitest.png -->
            <img src="/kedaitest.png" alt="Kedai Speed Racing" class="absolute inset-0 w-full h-full object-cover opacity-80">
            <!-- Overlay gradient -->
            <div class="absolute inset-0" style="background: linear-gradient(to bottom, rgba(96,116,86,0.55) 0%, rgba(40,55,35,0.75) 100%);"></div>
            <!-- Content on top -->
            <div class="relative z-10 flex flex-col items-center justify-center p-12 text-center">
                <img src="/logo.png" alt="KSR Logo" class="w-32 h-32 rounded-full shadow-2xl border-4 border-white object-cover mb-6">
                <h1 class="text-4xl font-extrabold text-white mb-4 drop-shadow-lg">Kedai Speed Racing</h1>
                <p class="text-green-100 text-center text-lg leading-relaxed max-w-sm drop-shadow">
                    Sistem Inventory Suku Cadang Motor Profesional
                </p>
                <div class="mt-10 flex space-x-6 text-white/80 text-sm">
                    <span>🔧 Sparepart</span>
                    <span>📦 Stok</span>
                    <span>📄 Laporan</span>
                </div>
            </div>
        </div>

        <!-- Right login form -->
        <div class="w-full lg:w-1/2 flex flex-col items-center justify-center px-6 py-12">
            <!-- Mobile logo -->
            <div class="lg:hidden mb-8 text-center">
                <img src="/logo.png" alt="KSR" class="w-24 h-24 rounded-full shadow-lg mx-auto mb-4 object-cover border-4" style="border-color: #607456;">
                <h2 class="text-2xl font-bold" style="color: #607456;">KSR Inventory</h2>
            </div>

            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl shadow-xl p-8 border" style="border-color: #dfd0b8;">
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold" style="color: #607456;">Selamat Datang!</h2>
                        <p class="text-sm text-gray-500 mt-1">Masuk ke sistem inventory KSR</p>
                    </div>

                    <form @submit.prevent="handleLogin" class="space-y-5">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                            <input v-model="email" id="email" type="email" required autocomplete="email"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 transition-colors"
                                style="border-color: #dfd0b8; focus:ring-color: #607456;"
                                placeholder="admin@kedairacing.com">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                            <input v-model="password" id="password" type="password" required autocomplete="current-password"
                                class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 transition-colors"
                                style="border-color: #dfd0b8;"
                                placeholder="••••••••">
                        </div>

                        <div v-if="error" class="flex items-center space-x-2 text-sm p-3 rounded-lg" style="background-color: #fef2f2; color: #7B2525; border: 1px solid #fca5a5;">
                            <span>⚠️</span>
                            <span>{{ error }}</span>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="w-full py-3 px-4 text-sm font-semibold text-white rounded-lg shadow-md transition-all disabled:opacity-60 flex items-center justify-center space-x-2"
                            style="background-color: #607456;"
                            onmouseover="this.style.backgroundColor='#4e5f45'"
                            onmouseout="this.style.backgroundColor='#607456'">
                            <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ loading ? 'Memproses...' : 'Masuk' }}</span>
                        </button>

                        <div class="text-center pt-2">
                            <router-link to="/register" class="text-sm font-medium transition-colors" style="color: #BA6A4C;">
                                Belum punya akun? Daftar sebagai Karyawan →
                            </router-link>
                        </div>
                    </form>
                </div>

                <p class="text-center text-xs text-gray-400 mt-6">
                    © 2024 Kedai Speed Racing · Sistem Inventory
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const email = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false);
const router = useRouter();
const authStore = useAuthStore();

const handleLogin = async () => {
    loading.value = true;
    error.value = '';
    try {
        await authStore.login(email.value, password.value);
        router.push('/');
    } catch (err) {
        error.value = err;
    } finally {
        loading.value = false;
    }
};
</script>
