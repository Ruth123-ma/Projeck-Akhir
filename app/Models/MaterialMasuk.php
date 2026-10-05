<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialMasuk extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_barang',
        'nama_material',
        'jumlah',
        'satuan',
        'tanggal_transaksi',
        'jam_input',
        'bulan_simpanan',
    ];

    /**
     * Arsip material masuk merupakan bagian dari laporan dan tidak dapat dihapus.
     */
    protected static function booted(): void
    {
        static::deleting(function (): void {
            throw new \LogicException('Riwayat material masuk bersifat permanen dan tidak dapat dihapus.');
        });
    }
}
