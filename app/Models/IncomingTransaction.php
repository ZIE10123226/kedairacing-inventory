<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IncomingTransaction extends Model {
    use SoftDeletes;
    protected $fillable = [
        'nomor_transaksi', 'tanggal', 'supplier_id', 'sparepart_id',
        'jumlah', 'harga_beli', 'user_id'
    ];
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function user() { return $this->belongsTo(User::class); }
}
