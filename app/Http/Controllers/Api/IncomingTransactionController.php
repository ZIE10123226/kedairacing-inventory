<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\IncomingTransaction;
use App\Models\Sparepart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomingTransactionController extends Controller {
    public function index() {
        return response()->json(IncomingTransaction::with(['sparepart', 'supplier', 'user'])->latest()->paginate(10));
    }

    public function store(Request $request) {
        $request->validate([
            'nomor_transaksi' => 'required|string|unique:incoming_transactions',
            'tanggal' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'sparepart_id' => 'required|exists:spareparts,id',
            'jumlah' => 'required|integer|min:1',
            'harga_beli' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $transaction = IncomingTransaction::create([
                'nomor_transaksi' => $request->nomor_transaksi,
                'tanggal' => $request->tanggal,
                'supplier_id' => $request->supplier_id,
                'sparepart_id' => $request->sparepart_id,
                'jumlah' => $request->jumlah,
                'harga_beli' => $request->harga_beli,
                'user_id' => auth()->id(),
            ]);

            // Tambah Stok
            $sparepart = Sparepart::findOrFail($request->sparepart_id);
            $sparepart->increment('stok', $request->jumlah);

            DB::commit();
            return response()->json(['message' => 'Barang masuk berhasil dicatat', 'data' => $transaction], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mencatat transaksi: ' . $e->getMessage()], 500);
        }
    }
}
