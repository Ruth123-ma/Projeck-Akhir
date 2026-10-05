<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class MaterialInventoryTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
    }

    public function test_creating_material_records_its_opening_stock(): void
    {
        $code = 'TEST-' . Str::upper(Str::random(12));

        $response = $this->post(route('material.store'), [
            'kode_barang' => $code,
            'nama_material' => 'Material Uji',
            'satuan' => 'PCS',
            'min_stok' => 10,
            'stok_awal' => 5,
        ]);

        $material = Material::where('kode_barang', $code)->firstOrFail();
        $response->assertRedirect(route('daftar-stok'));
        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'kode_barang' => $code,
            'jumlah' => 5,
            'stok_akhir' => 5,
            'status' => 'KRITIS',
        ]);
        $this->assertDatabaseHas('material_transactions', [
            'material_id' => $material->id,
            'type' => 'IN',
            'quantity' => 5,
            'notes' => 'Stok awal',
        ]);
    }

    public function test_receiving_and_issuing_material_updates_stock_and_history(): void
    {
        $material = $this->createMaterial(5);

        $this->post(route('input-material.store'), [
            'material_id' => $material->id,
            'type' => 'IN',
            'quantity' => 3,
        ])->assertRedirect();

        $this->post(route('output-material.store'), [
            'material_id' => $material->id,
            'type' => 'OUT',
            'quantity' => 2,
        ])->assertRedirect();

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'jumlah' => 6,
            'stok_akhir' => 6,
        ]);
        $this->assertSame(2, $material->transactions()->count());
    }

    public function test_material_in_entry_uses_its_selected_date_in_the_report(): void
    {
        $material = $this->createMaterial(0);

        $this->postJson(route('material-masuk.store'), [
            'material_id' => $material->id,
            'type' => 'IN',
            'quantity' => 25,
            'nama' => $material->nama_material,
            'satuan' => $material->satuan,
            'tanggal' => '2026-09-03',
            'jam' => '07:04:39',
            'bulan' => 'September',
        ])->assertOk();

        $this->postJson(route('material-masuk.store'), [
            'material_id' => $material->id,
            'type' => 'OUT',
            'quantity' => 7,
            'nama' => $material->nama_material,
            'satuan' => $material->satuan,
            'tanggal' => '2026-09-04',
            'jam' => '08:00:00',
            'bulan' => 'September',
        ])->assertOk();

        $this->assertDatabaseHas('material_transactions', [
            'material_id' => $material->id,
            'type' => 'OUT',
            'quantity' => 7,
            'created_at' => '2026-09-04 08:00:00',
        ]);

        $this->get(route('laporan-material', ['year' => 2026, 'month' => 9]))
            ->assertOk()
            ->assertSee($material->kode_barang)
            ->assertSee('7.00')
            ->assertDontSee('25.00')
            ->assertDontSee('Transaksi Masuk');
    }

    public function test_output_page_shows_september_stock_balance_from_transaction_history(): void
    {
        $material = $this->createMaterial(0);

        $material->transactions()->create([
            'type' => 'IN',
            'quantity' => 25,
            'user_id' => 'test',
            'notes' => 'Stok awal',
        ])->forceFill([
            'created_at' => '2026-09-03 07:04:39',
            'updated_at' => '2026-09-03 07:04:39',
        ])->save();

        $material->transactions()->create([
            'type' => 'OUT',
            'quantity' => 7,
            'user_id' => 'test',
            'notes' => 'Pengeluaran uji',
        ])->forceFill([
            'created_at' => '2026-09-04 08:00:00',
            'updated_at' => '2026-09-04 08:00:00',
        ])->save();

        $this->get(route('output-material'))
            ->assertOk()
            ->assertSee('Laporan Saldo Stok Gudang')
            ->assertSee('September 2026')
            ->assertSee($material->kode_barang)
            ->assertSee('18.00');
    }

    public function test_separate_stock_report_page_shows_september_balance(): void
    {
        $material = $this->createMaterial(0);
        $material->transactions()->create([
            'type' => 'IN',
            'quantity' => 25,
            'user_id' => 'test',
            'notes' => 'Stok awal',
        ])->forceFill([
            'created_at' => '2026-09-03 07:04:39',
            'updated_at' => '2026-09-03 07:04:39',
        ])->save();
        $material->transactions()->create([
            'type' => 'OUT',
            'quantity' => 7,
            'user_id' => 'test',
            'notes' => 'Pengeluaran uji',
        ])->forceFill([
            'created_at' => '2026-09-04 08:00:00',
            'updated_at' => '2026-09-04 08:00:00',
        ])->save();

        $this->get(route('laporan-stok'))
            ->assertOk()
            ->assertSee('Laporan Stok Gudang')
            ->assertSee('September 2026')
            ->assertSee($material->kode_barang)
            ->assertSee('18.00');
    }

    public function test_updating_material_details_does_not_change_its_stock(): void
    {
        $material = $this->createMaterial(20);

        $response = $this->put(route('material.update', $material), [
            'kode_barang' => $material->kode_barang,
            'nama_material' => 'Nama Diperbarui',
            'satuan' => 'BOX',
            'min_stok' => 25,
        ]);

        $response->assertRedirect(route('daftar-stok'));
        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'nama_material' => 'Nama Diperbarui',
            'satuan' => 'BOX',
            'stok_akhir' => 20,
            'status' => 'KRITIS',
        ]);
    }

    public function test_issuing_more_than_available_stock_is_rejected(): void
    {
        $material = $this->createMaterial(2);

        $response = $this->from(route('output-material'))->post(route('output-material.store'), [
            'material_id' => $material->id,
            'type' => 'OUT',
            'quantity' => 3,
        ]);

        $response->assertRedirect(route('output-material'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'stok_akhir' => 2,
        ]);
        $this->assertSame(0, $material->transactions()->count());
    }

    private function createMaterial(float $stock): Material
    {
        return Material::create([
            'kode_barang' => 'TEST-' . Str::upper(Str::random(12)),
            'nama_material' => 'Material Uji',
            'jumlah' => $stock,
            'stok_akhir' => $stock,
            'min_stok' => 1,
            'satuan' => 'PCS',
            'status' => 'NORMAL',
            'tanggal_transaksi' => today(),
            'jam_input' => now()->format('H:i:s'),
            'bulan_simpanan' => now()->format('F'),
        ]);
    }
}
