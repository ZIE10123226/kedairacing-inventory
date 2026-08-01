<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller {
    public function stats() {
        $today  = Carbon::today();
        $month  = Carbon::now()->startOfMonth();
        $year   = Carbon::now()->startOfYear();

        // ── Inventory Stats ──────────────────────────
        $totalSparepart  = Sparepart::count();
        $incomingToday   = IncomingTransaction::whereDate('tanggal', $today)->count();
        $outgoingToday   = OutgoingTransaction::whereDate('tanggal', $today)->count();
        $lowStockCount   = Sparepart::whereColumn('stok', '<=', 'stok_minimum')->count();
        $lowStockItems   = Sparepart::whereColumn('stok', '<=', 'stok_minimum')
                                    ->select('id', 'nama_barang', 'kode_barang', 'stok', 'stok_minimum')
                                    ->take(5)->get();
        $totalStockValue = Sparepart::sum(DB::raw('stok * harga_beli'));

        // ── Financial Stats ───────────────────────────
        // Total modal (nilai pembelian seluruh transaksi masuk)
        $totalModalBulan  = IncomingTransaction::where('tanggal', '>=', $month)
                            ->sum(DB::raw('jumlah * harga_beli'));
        $totalModalTahun  = IncomingTransaction::where('tanggal', '>=', $year)
                            ->sum(DB::raw('jumlah * harga_beli'));

        // Estimasi omzet (barang keluar × harga jual sparepart)
        $omzetBulan = OutgoingTransaction::where('outgoing_transactions.tanggal', '>=', $month)
                        ->join('spareparts', 'outgoing_transactions.sparepart_id', '=', 'spareparts.id')
                        ->sum(DB::raw('outgoing_transactions.jumlah * spareparts.harga_jual'));
        $omzetTahun = OutgoingTransaction::where('outgoing_transactions.tanggal', '>=', $year)
                        ->join('spareparts', 'outgoing_transactions.sparepart_id', '=', 'spareparts.id')
                        ->sum(DB::raw('outgoing_transactions.jumlah * spareparts.harga_jual'));

        // Estimasi laba kotor bulan ini
        $labaKotorBulan = $omzetBulan - $totalModalBulan;

        // Transaksi masuk 7 hari terakhir (untuk mini chart)
        $incomingChart = IncomingTransaction::select(
                            DB::raw('DATE(tanggal) as date'),
                            DB::raw('COUNT(*) as total_trx'),
                            DB::raw('SUM(jumlah * harga_beli) as total_nilai')
                         )
                         ->where('tanggal', '>=', Carbon::now()->subDays(6))
                         ->groupBy(DB::raw('DATE(tanggal)'))
                         ->orderBy('date')
                         ->get();

        // Transaksi keluar 7 hari terakhir
        $outgoingChart = OutgoingTransaction::join('spareparts', 'outgoing_transactions.sparepart_id', '=', 'spareparts.id')
                         ->select(
                            DB::raw('DATE(outgoing_transactions.tanggal) as date'),
                            DB::raw('COUNT(*) as total_trx'),
                            DB::raw('SUM(outgoing_transactions.jumlah * spareparts.harga_jual) as total_nilai')
                         )
                         ->where('outgoing_transactions.tanggal', '>=', Carbon::now()->subDays(6))
                         ->groupBy(DB::raw('DATE(outgoing_transactions.tanggal)'))
                         ->orderBy('date')
                         ->get();

        // Top 5 sparepart terlaris bulan ini
        $topSparepart = OutgoingTransaction::where('outgoing_transactions.tanggal', '>=', $month)
                        ->join('spareparts', 'outgoing_transactions.sparepart_id', '=', 'spareparts.id')
                        ->select(
                            'spareparts.nama_barang',
                            'spareparts.kode_barang',
                            DB::raw('SUM(outgoing_transactions.jumlah) as total_keluar'),
                            DB::raw('SUM(outgoing_transactions.jumlah * spareparts.harga_jual) as total_omzet')
                        )
                        ->groupBy('spareparts.id', 'spareparts.nama_barang', 'spareparts.kode_barang')
                        ->orderByDesc('total_keluar')
                        ->take(5)->get();

        return response()->json([
            // Inventory
            'total_sparepart'   => $totalSparepart,
            'incoming_today'    => $incomingToday,
            'outgoing_today'    => $outgoingToday,
            'low_stock_count'   => $lowStockCount,
            'low_stock_items'   => $lowStockItems,
            'total_stock_value' => $totalStockValue,
            // Financial
            'modal_bulan'       => $totalModalBulan,
            'modal_tahun'       => $totalModalTahun,
            'omzet_bulan'       => $omzetBulan,
            'omzet_tahun'       => $omzetTahun,
            'laba_kotor_bulan'  => $labaKotorBulan,
            // Charts
            'incoming_chart'    => $incomingChart,
            'outgoing_chart'    => $outgoingChart,
            'top_sparepart'     => $topSparepart,
        ]);
    }
}
