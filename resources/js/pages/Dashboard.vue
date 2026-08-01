<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold" style="color: #607456;">Dashboard</h1>
                <p class="text-sm text-gray-500 mt-1">{{ todayFormatted }}</p>
            </div>
            <button @click="loadData" class="text-sm px-3 py-1.5 rounded-lg border transition-colors"
                style="border-color: #607456; color: #607456;">
                🔄 Refresh
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="text-center py-20 text-gray-400">Memuat data dashboard...</div>

        <template v-else>
            <!-- ── Row 1: Inventory Stats ── -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4" style="border-left-color: #607456;">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Sparepart</p>
                    <p class="text-3xl font-bold mt-1" style="color: #607456;">{{ stats.total_sparepart }}</p>
                    <p class="text-xs text-gray-400 mt-1">jenis barang terdaftar</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4" style="border-left-color: #607456;">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Nilai Stok</p>
                    <p class="text-xl font-bold mt-1" style="color: #607456;">{{ formatRp(stats.total_stock_value) }}</p>
                    <p class="text-xs text-gray-400 mt-1">estimasi nilai gudang</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-l-green-400">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Masuk Hari Ini</p>
                    <p class="text-3xl font-bold text-green-600 mt-1">{{ stats.incoming_today }}</p>
                    <p class="text-xs text-gray-400 mt-1">transaksi barang masuk</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm p-5 border-l-4" :style="stats.low_stock_count > 0 ? 'border-left-color: #7B2525;' : 'border-left-color: #BA6A4C;'">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Stok Menipis</p>
                    <p class="text-3xl font-bold mt-1" :style="stats.low_stock_count > 0 ? 'color: #7B2525;' : 'color: #BA6A4C;'">{{ stats.low_stock_count }}</p>
                    <p class="text-xs text-gray-400 mt-1">barang di bawah minimum</p>
                </div>
            </div>

            <!-- ── Row 2: Financial Summary ── -->
            <div>
                <h2 class="text-base font-bold mb-3" style="color: #607456;">📊 Laporan Keuangan</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Modal / Pembelian -->
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pembelian</p>
                                <p class="text-sm text-gray-400">(Modal Masuk)</p>
                            </div>
                            <span class="text-2xl">🛒</span>
                        </div>
                        <p class="text-2xl font-bold mt-3" style="color: #607456;">{{ formatRp(stats.modal_bulan) }}</p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-sm font-semibold text-gray-600">{{ formatRp(stats.modal_tahun) }}</p>
                            <p class="text-xs text-gray-400">Total tahun ini</p>
                        </div>
                    </div>

                    <!-- Omzet / Penjualan -->
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Estimasi Omzet</p>
                                <p class="text-sm text-gray-400">(Penjualan Keluar)</p>
                            </div>
                            <span class="text-2xl">💰</span>
                        </div>
                        <p class="text-2xl font-bold mt-3 text-green-600">{{ formatRp(stats.omzet_bulan) }}</p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <p class="text-sm font-semibold text-gray-600">{{ formatRp(stats.omzet_tahun) }}</p>
                            <p class="text-xs text-gray-400">Total tahun ini</p>
                        </div>
                    </div>

                    <!-- Laba Kotor -->
                    <div class="bg-white rounded-xl shadow-sm p-5" :style="stats.laba_kotor_bulan >= 0 ? '' : 'border: 1px solid #fca5a5;'">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Estimasi Laba Kotor</p>
                                <p class="text-sm text-gray-400">(Omzet − Modal)</p>
                            </div>
                            <span class="text-2xl">📈</span>
                        </div>
                        <p class="text-2xl font-bold mt-3"
                            :style="stats.laba_kotor_bulan >= 0 ? 'color: #607456;' : 'color: #7B2525;'">
                            {{ formatRp(stats.laba_kotor_bulan) }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="flex items-center space-x-1">
                                <span class="text-xs font-semibold"
                                    :style="stats.laba_kotor_bulan >= 0 ? 'color: #607456;' : 'color: #7B2525;'">
                                    {{ stats.omzet_bulan > 0 ? ((stats.laba_kotor_bulan / stats.omzet_bulan) * 100).toFixed(1) + '%' : '0%' }}
                                </span>
                                <span class="text-xs text-gray-400">margin keuntungan</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Row 3: Chart 7 Hari + Top Sparepart ── -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Aktivitas 7 Hari -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">📅 Aktivitas 7 Hari Terakhir</h3>
                    <div class="space-y-2">
                        <div v-for="day in last7Days" :key="day.date" class="flex items-center space-x-3">
                            <span class="text-xs text-gray-500 w-16 shrink-0">{{ day.label }}</span>
                            <div class="flex-1 flex space-x-1 items-center">
                                <!-- Bar Masuk -->
                                <div class="h-4 rounded-sm transition-all" style="background-color: #607456; opacity: 0.8;"
                                    :style="`width: ${day.inPercent}%; min-width: ${day.inVal > 0 ? 4 : 0}px`"></div>
                                <!-- Bar Keluar -->
                                <div class="h-4 rounded-sm transition-all" style="background-color: #BA6A4C; opacity: 0.8;"
                                    :style="`width: ${day.outPercent}%; min-width: ${day.outVal > 0 ? 4 : 0}px`"></div>
                            </div>
                            <div class="flex space-x-2 text-xs shrink-0">
                                <span style="color: #607456;" class="font-semibold w-4 text-right">{{ day.inVal }}</span>
                                <span class="text-gray-300">/</span>
                                <span style="color: #BA6A4C;" class="font-semibold w-4">{{ day.outVal }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-4 mt-4 pt-3 border-t border-gray-100 text-xs text-gray-400">
                        <span><span class="inline-block w-3 h-3 rounded-sm mr-1" style="background-color: #607456;"></span>Barang Masuk</span>
                        <span><span class="inline-block w-3 h-3 rounded-sm mr-1" style="background-color: #BA6A4C;"></span>Barang Keluar</span>
                    </div>
                </div>

                <!-- Top 5 Sparepart Terlaris -->
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <h3 class="text-sm font-bold text-gray-700 mb-4">🏆 Top 5 Sparepart Terlaris (Bulan Ini)</h3>
                    <div v-if="stats.top_sparepart?.length > 0" class="space-y-3">
                        <div v-for="(item, idx) in stats.top_sparepart" :key="item.kode_barang" class="flex items-center space-x-3">
                            <span class="text-lg font-bold w-6 shrink-0"
                                :style="idx === 0 ? 'color: #f59e0b;' : idx === 1 ? 'color: #9ca3af;' : idx === 2 ? 'color: #BA6A4C;' : 'color: #d1d5db;'">
                                {{ idx + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ item.nama_barang }}</p>
                                <p class="text-xs text-gray-400">{{ item.kode_barang }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold" style="color: #607456;">{{ item.total_keluar }} pcs</p>
                                <p class="text-xs text-gray-400">{{ formatRp(item.total_omzet) }}</p>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-center py-8 text-gray-400 text-sm">
                        Belum ada transaksi keluar bulan ini.
                    </div>
                </div>
            </div>

            <!-- ── Row 4: Stok Menipis Alert ── -->
            <div v-if="stats.low_stock_count > 0" class="bg-white rounded-xl shadow-sm p-5 border-l-4" style="border-left-color: #7B2525;">
                <div class="flex items-center mb-3">
                    <span class="text-xl mr-2">⚠️</span>
                    <h3 class="text-sm font-bold" style="color: #7B2525;">{{ stats.low_stock_count }} Barang Stok Menipis — Segera Restock!</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    <div v-for="item in stats.low_stock_items" :key="item.id"
                        class="flex items-center justify-between px-3 py-2 rounded-lg text-sm"
                        style="background-color: #fff5f5;">
                        <div>
                            <p class="font-semibold text-gray-800">{{ item.nama_barang }}</p>
                            <p class="text-xs text-gray-400">{{ item.kode_barang }}</p>
                        </div>
                        <div class="text-right">
                            <span class="font-bold" style="color: #7B2525;">{{ item.stok }}</span>
                            <span class="text-gray-400 text-xs"> / {{ item.stok_minimum }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../services/api';

const loading = ref(true);
const stats = ref({
    total_sparepart: 0, incoming_today: 0, outgoing_today: 0,
    low_stock_count: 0, low_stock_items: [], total_stock_value: 0,
    modal_bulan: 0, modal_tahun: 0, omzet_bulan: 0, omzet_tahun: 0,
    laba_kotor_bulan: 0, incoming_chart: [], outgoing_chart: [], top_sparepart: []
});

const formatRp = (val) => {
    const num = Number(val) || 0;
    if (num >= 1_000_000) return 'Rp ' + (num / 1_000_000).toFixed(1) + ' Jt';
    if (num >= 1_000) return 'Rp ' + (num / 1_000).toFixed(0) + ' Rb';
    return 'Rp ' + num.toLocaleString('id-ID');
};

const todayFormatted = computed(() => {
    return new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
});

// Build 7-day chart data
const last7Days = computed(() => {
    const days = [];
    const inMap = {};
    const outMap = {};
    (stats.value.incoming_chart || []).forEach(d => { inMap[d.date] = d.total_trx; });
    (stats.value.outgoing_chart || []).forEach(d => { outMap[d.date] = d.total_trx; });
    const maxVal = Math.max(1, ...Object.values(inMap), ...Object.values(outMap));

    for (let i = 6; i >= 0; i--) {
        const d = new Date();
        d.setDate(d.getDate() - i);
        const key = d.toISOString().split('T')[0];
        const label = d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric' });
        const inVal = inMap[key] || 0;
        const outVal = outMap[key] || 0;
        days.push({ date: key, label, inVal, outVal, inPercent: (inVal / maxVal) * 60, outPercent: (outVal / maxVal) * 60 });
    }
    return days;
});

const loadData = async () => {
    loading.value = true;
    try {
        const res = await api.get('/dashboard/stats');
        stats.value = res.data;
    } catch (e) {
        console.error('Failed to load stats', e);
    } finally {
        loading.value = false;
    }
};

onMounted(loadData);
</script>
