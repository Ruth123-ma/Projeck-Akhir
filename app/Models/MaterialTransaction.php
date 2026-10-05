<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialTransaction extends Model
{
    use HasFactory;

    protected $table = 'material_transactions';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'material_id',
        'type',
        'quantity',
        'user_id',
        'notes',
    ];

    /**
     * Transaksi adalah arsip laporan audit dan tidak boleh dihapus dari aplikasi.
     */
    protected static function booted(): void
    {
        static::deleting(function (): void {
            throw new \LogicException('Riwayat transaksi material bersifat permanen dan tidak dapat dihapus.');
        });
    }

    /**
     * Relasi balik ke Model Material.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
