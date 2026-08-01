<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller {
    public function index() {
        return response()->json(Supplier::all());
    }

    public function store(Request $request) {
        $request->validate(['nama' => 'required|string|max:255', 'alamat' => 'nullable|string', 'telepon' => 'nullable|string', 'email' => 'nullable|email']);
        $supplier = Supplier::create($request->all());
        return response()->json(['message' => 'Supplier berhasil ditambahkan', 'data' => $supplier], 201);
    }

    public function show($id) {
        return response()->json(Supplier::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $supplier = Supplier::findOrFail($id);
        $request->validate(['nama' => 'required|string|max:255', 'alamat' => 'nullable|string', 'telepon' => 'nullable|string', 'email' => 'nullable|email']);
        $supplier->update($request->all());
        return response()->json(['message' => 'Supplier berhasil diupdate', 'data' => $supplier]);
    }

    public function destroy($id) {
        Supplier::findOrFail($id)->delete();
        return response()->json(['message' => 'Supplier berhasil dihapus']);
    }
}
