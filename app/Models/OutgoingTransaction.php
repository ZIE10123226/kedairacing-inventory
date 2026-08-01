<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OutgoingTransaction extends Model {
    use SoftDeletes;
    protected $fillable = [
        'nomor_transaksi', 'tanggal', 'sparepart_id',
        'jumlah', 'keterangan', 'user_id'
    ];
    public function sparepart() { return $this->belongsTo(Sparepart::class); }
    public function user() { return $this->belongsTo(User::class); }
}
