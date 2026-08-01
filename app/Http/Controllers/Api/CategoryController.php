<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() {
        return response()->json(Category::all());
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255|unique:categories', 'description' => 'nullable|string']);
        $category = Category::create($request->all());
        return response()->json(['message' => 'Kategori berhasil ditambahkan', 'data' => $category], 201);
    }

    public function show($id) {
        return response()->json(Category::findOrFail($id));
    }

    public function update(Request $request, $id) {
        $category = Category::findOrFail($id);
        $request->validate(['name' => 'required|string|max:255|unique:categories,name,'.$id, 'description' => 'nullable|string']);
        $category->update($request->all());
        return response()->json(['message' => 'Kategori berhasil diupdate', 'data' => $category]);
    }

    public function destroy($id) {
        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
