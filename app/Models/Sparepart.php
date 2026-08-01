<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sparepart extends Model {
    use SoftDeletes;
    protected $fillable = [
        'kode_barang', 'nama_barang', 'kategori_id', 'merk',
        'stok', 'stok_minimum', 'harga_beli', 'harga_jual', 
        'lokasi_rak', 'supplier_id', 'foto_barang'
    ];

    public function category() { return $this->belongsTo(Category::class, 'kategori_id'); }
    public function supplier() { return $this->belongsTo(Supplier::class, 'supplier_id'); }
}
