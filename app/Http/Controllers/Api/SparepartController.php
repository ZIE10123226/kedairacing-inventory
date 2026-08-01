<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller {
    public function index(Request $request) {
        $query = Sparepart::with(['category', 'supplier']);
        if ($request->search) {
            $query->where('nama_barang', 'like', '%'.$request->search.'%')->orWhere('kode_barang', 'like', '%'.$request->search.'%');
        }
        // Pagination
        return response()->json($query->paginate(10));
    }

    public function store(Request $request) {
        $request->validate([
            'kode_barang' => 'required|string|unique:spareparts',
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:categories,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id',
            'stok_minimum' => 'nullable|integer|min:0',
        ]);
        $sparepart = Sparepart::create($request->all());
        return response()->json(['message' => 'Sparepart berhasil ditambahkan', 'data' => $sparepart], 201);
    }

    public function show($id) {
        return response()->json(Sparepart::with(['category', 'supplier'])->findOrFail($id));
    }

    public function update(Request $request, $id) {
        $sparepart = Sparepart::findOrFail($id);
        $request->validate([
            'kode_barang' => 'required|string|unique:spareparts,kode_barang,'.$id,
            'nama_barang' => 'required|string|max:255',
            'kategori_id' => 'required|exists:categories,id',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'supplier_id' => 'required|exists:suppliers,id'
        ]);
        $sparepart->update($request->all());
        return response()->json(['message' => 'Sparepart berhasil diupdate', 'data' => $sparepart]);
    }

    public function destroy($id) {
        Sparepart::findOrFail($id)->delete();
        return response()->json(['message' => 'Sparepart berhasil dihapus']);
    }
}
