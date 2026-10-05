<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_material',
        'jumlah',
        'stok_akhir',
        'min_stok',
        'satuan',
        'status',
        'tanggal_transaksi',
        'jam_input',
        'bulan_simpanan',
    ];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'stok_akhir' => 'decimal:2',
        'min_stok' => 'decimal:2',
        'tanggal_transaksi' => 'date',
    ];

    public static function statusForStock(float $stock, float $minimumStock): string
    {
        if ($stock <= $minimumStock) {
            return 'KRITIS';
        }

        if ($stock <= $minimumStock * 1.5) {
            return 'RENDAH';
        }

        return 'NORMAL';
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MaterialTransaction::class, 'material_id');
    }
}