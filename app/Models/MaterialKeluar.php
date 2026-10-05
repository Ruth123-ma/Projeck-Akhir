<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialKeluar extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terhubung dengan model.
     *
     * @var string
     */
    protected $table = 'material_keluar';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
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
     * Tipe data khusus / casting atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
    ];
}