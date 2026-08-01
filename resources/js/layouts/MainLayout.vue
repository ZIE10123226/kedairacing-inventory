<template>
    <div class="min-h-screen flex" style="background-color: #EEE0CC;">

        <!-- ── Mobile Overlay Backdrop ── -->
        <div v-if="sidebarOpen"
            class="fixed inset-0 z-40 bg-black bg-opacity-50 lg:hidden"
            @click="sidebarOpen = false">
        </div>

        <!-- ── Sidebar ── -->
        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col transform transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            style="background-color: #607456;">

            <!-- Logo & Close Button -->
            <div class="flex items-center justify-between px-4 py-4 shrink-0" style="border-bottom: 1px solid #4e5f45;">
                <div class="flex items-center">
                    <img src="/logo.png" alt="KSR" class="h-10 w-10 rounded-full shadow-md mr-3 object-cover border-2 border-white">
                    <div>
                        <p class="text-white font-bold text-sm leading-tight">KSR Inventory</p>
                        <p class="text-xs" style="color: #c8d8c0;">Kedai Speed Racing</p>
                    </div>
                </div>
                <!-- Close button (mobile only) -->
                <button @click="sidebarOpen = false"
                    class="lg:hidden text-white/70 hover:text-white p-1 rounded-md hover:bg-white/10 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                <router-link to="/"
                    @click="closeSidebarMobile"
                    class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                    :class="isActive('/') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                    :style="isActive('/') ? 'background-color: #4e5f45;' : ''">
                    <span class="mr-2.5">📊</span> Dashboard
                </router-link>

                <!-- Master Data (Admin only) -->
                <div v-if="authStore.isAdmin" class="pt-3 mt-3" style="border-top: 1px solid #4e5f45;">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-2" style="color: #a8c098;">Master Data</p>
                    <router-link to="/sparepart" @click="closeSidebarMobile"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive('/sparepart') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                        :style="isActive('/sparepart') ? 'background-color: #4e5f45;' : ''">
                        <span class="mr-2.5">🔧</span> Sparepart
                    </router-link>
                    <router-link to="/kategori" @click="closeSidebarMobile"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive('/kategori') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                        :style="isActive('/kategori') ? 'background-color: #4e5f45;' : ''">
                        <span class="mr-2.5">🗂️</span> Kategori
                    </router-link>
                    <router-link to="/supplier" @click="closeSidebarMobile"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive('/supplier') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                        :style="isActive('/supplier') ? 'background-color: #4e5f45;' : ''">
                        <span class="mr-2.5">🏭</span> Supplier
                    </router-link>
                    <router-link to="/approvals" @click="closeSidebarMobile"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive('/approvals') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                        :style="isActive('/approvals') ? 'background-color: #4e5f45;' : ''">
                        <span class="mr-2.5">✅</span> Persetujuan Akun
                    </router-link>
                </div>

                <!-- Transaksi -->
                <div class="pt-3 mt-3" style="border-top: 1px solid #4e5f45;">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-2" style="color: #a8c098;">Transaksi</p>
                    <router-link to="/barang-masuk" @click="closeSidebarMobile"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive('/barang-masuk') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                        :style="isActive('/barang-masuk') ? 'background-color: #4e5f45;' : ''">
                        <span class="mr-2.5">📥</span> Barang Masuk
                    </router-link>
                    <router-link to="/barang-keluar" @click="closeSidebarMobile"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive('/barang-keluar') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                        :style="isActive('/barang-keluar') ? 'background-color: #4e5f45;' : ''">
                        <span class="mr-2.5">📤</span> Barang Keluar
                    </router-link>
                </div>

                <!-- Laporan -->
                <div class="pt-3 mt-3" style="border-top: 1px solid #4e5f45;">
                    <p class="px-3 text-xs font-semibold uppercase tracking-wider mb-2" style="color: #a8c098;">Laporan</p>
                    <router-link to="/laporan" @click="closeSidebarMobile"
                        class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors"
                        :class="isActive('/laporan') ? 'text-white font-semibold' : 'text-green-100 hover:text-white hover:bg-white/10'"
                        :style="isActive('/laporan') ? 'background-color: #4e5f45;' : ''">
                        <span class="mr-2.5">📄</span> Cetak Laporan
                    </router-link>
                </div>
            </nav>

            <!-- User info (bottom of sidebar) -->
            <div class="px-4 py-4 shrink-0" style="border-top: 1px solid #4e5f45;">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white shrink-0"
                        style="background-color: #BA6A4C;">
                        {{ authStore.user?.name?.charAt(0)?.toUpperCase() }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-white truncate">{{ authStore.user?.name }}</p>
                        <p class="text-xs" style="color: #a8c098;">{{ authStore.user?.role?.name }}</p>
                    </div>
                    <button @click="logout" title="Logout"
                        class="text-white/60 hover:text-white p-1 rounded-md hover:bg-white/10 transition-colors shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- ── Main Content ── -->
        <main class="flex-1 flex flex-col min-w-0 lg:ml-0">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 h-14 flex items-center justify-between px-4 sm:px-6 shadow-sm" style="background-color: #607456;">
                <div class="flex items-center space-x-3">
                    <!-- Hamburger (mobile) -->
                    <button @click="sidebarOpen = true"
                        class="lg:hidden text-white p-2 rounded-lg hover:bg-white/10 transition-colors -ml-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="text-white font-semibold text-base truncate">{{ pageTitle }}</span>
                </div>

                <!-- Right: user badge (desktop) -->
                <div class="hidden sm:flex items-center space-x-3">
                    <span class="text-sm text-green-100">{{ authStore.user?.name }}</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium text-white"
                        style="background-color: #BA6A4C;">
                        {{ authStore.user?.role?.name }}
                    </span>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-4 sm:p-6">
                <router-view></router-view>
            </div>

            <!-- Mobile Bottom Nav (shortcut) -->
            <nav class="lg:hidden sticky bottom-0 flex border-t shadow-lg" style="background-color: #607456; border-color: #4e5f45;">
                <router-link to="/" @click="closeSidebarMobile"
                    class="flex-1 flex flex-col items-center py-2 text-xs transition-colors"
                    :class="isActive('/') ? 'text-white font-semibold' : 'text-green-200'">
                    <span class="text-lg leading-none">📊</span>
                    <span class="mt-0.5">Dashboard</span>
                </router-link>
                <router-link to="/barang-masuk" @click="closeSidebarMobile"
                    class="flex-1 flex flex-col items-center py-2 text-xs transition-colors"
                    :class="isActive('/barang-masuk') ? 'text-white font-semibold' : 'text-green-200'">
                    <span class="text-lg leading-none">📥</span>
                    <span class="mt-0.5">Masuk</span>
                </router-link>
                <router-link to="/barang-keluar" @click="closeSidebarMobile"
                    class="flex-1 flex flex-col items-center py-2 text-xs transition-colors"
                    :class="isActive('/barang-keluar') ? 'text-white font-semibold' : 'text-green-200'">
                    <span class="text-lg leading-none">📤</span>
                    <span class="mt-0.5">Keluar</span>
                </router-link>
                <router-link to="/laporan" @click="closeSidebarMobile"
                    class="flex-1 flex flex-col items-center py-2 text-xs transition-colors"
                    :class="isActive('/laporan') ? 'text-white font-semibold' : 'text-green-200'">
                    <span class="text-lg leading-none">📄</span>
                    <span class="mt-0.5">Laporan</span>
                </router-link>
                <button @click="sidebarOpen = true"
                    class="flex-1 flex flex-col items-center py-2 text-xs text-green-200">
                    <span class="text-lg leading-none">☰</span>
                    <span class="mt-0.5">Menu</span>
                </button>
            </nav>
        </main>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();
const sidebarOpen = ref(false);

const isActive = (path) => {
    if (path === '/') return route.path === '/';
    return route.path.startsWith(path);
};

const closeSidebarMobile = () => {
    // Only close on mobile (sidebar overlays content)
    if (window.innerWidth < 1024) sidebarOpen.value = false;
};

const pageTitles = {
    '/': 'Dashboard',
    '/sparepart': 'Master Sparepart',
    '/kategori': 'Master Kategori',
    '/supplier': 'Master Supplier',
    '/approvals': 'Persetujuan Akun',
    '/barang-masuk': 'Barang Masuk',
    '/barang-keluar': 'Barang Keluar',
    '/laporan': 'Cetak Laporan',
};

const pageTitle = computed(() => pageTitles[route.path] || 'KSR Inventory');

const logout = async () => {
    await authStore.logout();
    router.push('/login');
};
</script>
