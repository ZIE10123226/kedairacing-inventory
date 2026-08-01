<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\IncomingTransaction;
use App\Models\OutgoingTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function generate(Request $request) {
        $type = $request->type; // 'masuk' or 'keluar'
        $period = $request->period; // 'harian', 'bulanan', 'tahunan'
        $date = $request->date; // YYYY-MM-DD for harian, YYYY-MM for bulanan, YYYY for tahunan

        $query = $type === 'masuk' 
            ? IncomingTransaction::with(['sparepart', 'supplier', 'user'])
            : OutgoingTransaction::with(['sparepart', 'user']);

        if ($period === 'harian') {
            $query->whereDate('tanggal', $date);
        } elseif ($period === 'bulanan') {
            $yearMonth = explode('-', $date);
            if (count($yearMonth) == 2) {
                $query->whereYear('tanggal', $yearMonth[0])->whereMonth('tanggal', $yearMonth[1]);
            }
        } elseif ($period === 'tahunan') {
            $query->whereYear('tanggal', $date);
        }

        $data = $query->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'title' => 'Laporan Barang ' . ucfirst($type),
            'period' => $period,
            'data' => $data
        ]);
    }
}
