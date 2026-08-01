<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\OutgoingTransaction;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutgoingTransactionController extends Controller {
    public function index() {
        return response()->json(OutgoingTransaction::with(['sparepart', 'user'])->latest()->paginate(10));
    }

    public function store(Request $request) {
        $request->validate([
            'nomor_transaksi' => 'required|string|unique:outgoing_transactions',
            'tanggal' => 'required|date',
            'sparepart_id' => 'required|exists:spareparts,id',
            'jumlah' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $sparepart = Sparepart::findOrFail($request->sparepart_id);
            if ($sparepart->stok < $request->jumlah) {
                return response()->json(['message' => 'Stok tidak mencukupi'], 400);
            }

            $transaction = OutgoingTransaction::create([
                'nomor_transaksi' => $request->nomor_transaksi,
                'tanggal' => $request->tanggal,
                'sparepart_id' => $request->sparepart_id,
                'jumlah' => $request->jumlah,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            // Kurangi Stok
            $sparepart->decrement('stok', $request->jumlah);

            DB::commit();
            return response()->json(['message' => 'Barang keluar berhasil dicatat', 'data' => $transaction], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mencatat transaksi: ' . $e->getMessage()], 500);
        }
    }
}
