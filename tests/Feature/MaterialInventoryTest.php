<?php

namespace Tests\Feature;

use App\Models\Material;
use App\Models\MaterialMasuk;
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
        $code = 'TEST-'.Str::upper(Str::random(12));

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
        $this->assertDatabaseHas('material_masuks', [
            'kode_barang' => $material->kode_barang,
            'tanggal_transaksi' => '2026-09-03',
            'jumlah' => 25,
        ]);

        $this->get(route('laporan-material', ['year' => 2026, 'month' => 9]))
            ->assertOk()
            ->assertSee($material->kode_barang)
            ->assertSee('7.00')
            ->assertSee('25.00')
            ->assertSee('Riwayat Transaksi Masuk');
    }

    public function test_material_in_history_can_be_searched_and_exported_to_excel(): void
    {
        $searchTerm = 'SEARCH-'.Str::upper(Str::random(12));
        MaterialMasuk::create([
            'kode_barang' => 'TEST-UREA-01',
            'nama_material' => 'Pupuk Urea '.$searchTerm,
            'jumlah' => 12.5,
            'satuan' => 'KG',
            'tanggal_transaksi' => '2026-09-03',
            'jam_input' => '07:04:39',
            'bulan_simpanan' => 'September',
        ]);
        MaterialMasuk::create([
            'kode_barang' => 'TEST-KCL-01',
            'nama_material' => 'Pupuk KCL',
            'jumlah' => 8,
            'satuan' => 'KG',
            'tanggal_transaksi' => '2026-09-04',
            'jam_input' => '08:00:00',
            'bulan_simpanan' => 'September',
        ]);

        $this->getJson(route('material-masuk.index', ['bulan' => 'September', 'cari' => $searchTerm]))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.kode_barang', 'TEST-UREA-01');

        $response = $this->get(route('material-masuk.export.excel', [
            'bulan' => 'September',
            'cari' => $searchTerm,
        ]));

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_output_page_shows_transaction_history_without_stock_balance_report(): void
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
            ->assertSee('Riwayat Transaksi Material Keluar')
            ->assertDontSee('Laporan Saldo Stok Gudang')
            ->assertSee($material->kode_barang)
            ->assertSee('Pengeluaran uji');
    }

    public function test_output_history_can_be_searched_filtered_and_exported(): void
    {
        $matchingMaterial = $this->createMaterial(0);
        $matchingMaterial->update(['nama_material' => 'Material Terpilih']);
        $matchingMaterial->transactions()->create([
            'type' => 'OUT',
            'quantity' => 4,
            'user_id' => 'test',
            'notes' => 'Transaksi bulan September',
        ])->forceFill([
            'created_at' => '2026-09-04 08:00:00',
            'updated_at' => '2026-09-04 08:00:00',
        ])->save();

        $otherMaterial = $this->createMaterial(0);
        $otherMaterial->update(['nama_material' => 'Material Terpilih']);
        $otherMaterial->transactions()->create([
            'type' => 'OUT',
            'quantity' => 2,
            'user_id' => 'test',
            'notes' => 'Transaksi bulan Oktober',
        ])->forceFill([
            'created_at' => '2026-10-04 08:00:00',
            'updated_at' => '2026-10-04 08:00:00',
        ])->save();

        $filters = ['bulan' => '2026-09', 'cari' => 'Terpilih'];

        $this->get(route('output-material', $filters))
            ->assertOk()
            ->assertSee('Cari Material')
            ->assertSee('Filter Bulan')
            ->assertSee('Transaksi bulan September')
            ->assertDontSee('Transaksi bulan Oktober');

        $this->get(route('output-material.export.pdf', $filters))
            ->assertOk()
            ->assertSee($matchingMaterial->kode_barang)
            ->assertDontSee($otherMaterial->kode_barang);

        $response = $this->get(route('output-material.export.excel', $filters));
        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_stock_list_can_be_searched_filtered_and_exported(): void
    {
        $matchingMaterial = $this->createMaterial(12);
        $matchingMaterial->update([
            'nama_material' => 'Stok Material Terpilih',
            'tanggal_transaksi' => '2026-09-04',
        ]);
        $otherMaterial = $this->createMaterial(8);
        $otherMaterial->update([
            'nama_material' => 'Stok Material Terpilih',
            'tanggal_transaksi' => '2026-10-04',
        ]);

        $filters = ['bulan' => '2026-09', 'cari' => 'Terpilih'];

        $this->get(route('daftar-stok', $filters))
            ->assertOk()
            ->assertSee('Cari Material')
            ->assertSee('Filter Bulan')
            ->assertSee($matchingMaterial->kode_barang)
            ->assertDontSee($otherMaterial->kode_barang);

        $this->get(route('daftar-stok.export.pdf', $filters))
            ->assertOk()
            ->assertSee($matchingMaterial->kode_barang)
            ->assertDontSee($otherMaterial->kode_barang);

        $response = $this->get(route('daftar-stok.export.excel', $filters));
        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $this->assertStringStartsWith('PK', $response->streamedContent());
    }

    public function test_material_report_searches_and_exports_incoming_and_outgoing_transactions(): void
    {
        $matchingMaterial = $this->createMaterial(0);
        $matchingMaterial->update(['nama_material' => 'Material Rekap Terpilih']);
        $matchingMaterial->transactions()->create([
            'type' => 'IN',
            'quantity' => 11,
            'user_id' => 'test',
            'notes' => 'Transaksi masuk terpilih',
        ])->forceFill([
            'created_at' => '2026-09-03 07:04:39',
            'updated_at' => '2026-09-03 07:04:39',
        ])->save();
        $matchingMaterial->transactions()->create([
            'type' => 'OUT',
            'quantity' => 3,
            'user_id' => 'test',
            'notes' => 'Transaksi keluar terpilih',
        ])->forceFill([
            'created_at' => '2026-09-04 08:00:00',
            'updated_at' => '2026-09-04 08:00:00',
        ])->save();

        $otherMaterial = $this->createMaterial(0);
        $otherMaterial->update(['nama_material' => 'Material Lainnya']);
        $otherMaterial->transactions()->create([
            'type' => 'IN',
            'quantity' => 99,
            'user_id' => 'test',
            'notes' => 'Transaksi tidak cocok',
        ])->forceFill([
            'created_at' => '2026-09-05 08:00:00',
            'updated_at' => '2026-09-05 08:00:00',
        ])->save();

        $filters = ['year' => 2026, 'month' => 9, 'cari' => 'Terpilih'];
        $this->get(route('laporan-material', $filters))
            ->assertOk()
            ->assertSee('Filter Bulan')
            ->assertSee('Cari Material')
            ->assertSee('Jenis Barang Masuk')
            ->assertDontSee('Jumlah Total Masuk')
            ->assertDontSee('Jumlah Total Keluar')
            ->assertSee('Transaksi masuk terpilih')
            ->assertSee('Transaksi keluar terpilih')
            ->assertDontSee('Transaksi tidak cocok');

        $this->get(route('laporan-material.export.pdf', $filters))
            ->assertOk()
            ->assertSee('Transaksi masuk terpilih')
            ->assertSee('Transaksi keluar terpilih')
            ->assertDontSee('Transaksi tidak cocok');

        $response = $this->get(route('laporan-material.export.excel', $filters));
        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $this->assertStringStartsWith('PK', $response->streamedContent());
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

    public function test_stock_report_can_be_searched_and_exported_with_selected_month(): void
    {
        $matchingMaterial = $this->createMaterial(0);
        $matchingMaterial->update(['nama_material' => 'Stok Rekap Terpilih']);
        $matchingMaterial->transactions()->create([
            'type' => 'IN',
            'quantity' => 25,
            'user_id' => 'test',
        ])->forceFill([
            'created_at' => '2026-09-03 07:04:39',
            'updated_at' => '2026-09-03 07:04:39',
        ])->save();
        $matchingMaterial->transactions()->create([
            'type' => 'OUT',
            'quantity' => 7,
            'user_id' => 'test',
        ])->forceFill([
            'created_at' => '2026-09-04 08:00:00',
            'updated_at' => '2026-09-04 08:00:00',
        ])->save();

        $otherMaterial = $this->createMaterial(0);
        $otherMaterial->update(['nama_material' => 'Stok Material Lain']);

        $filters = ['year' => 2026, 'month' => 9, 'cari' => 'Terpilih'];
        $this->get(route('laporan-stok', $filters))
            ->assertOk()
            ->assertSee('Cari Material')
            ->assertSee('Filter Bulan')
            ->assertSee($matchingMaterial->kode_barang)
            ->assertSee('18.00')
            ->assertDontSee($otherMaterial->kode_barang);

        $this->get(route('laporan-stok.export.pdf', $filters))
            ->assertOk()
            ->assertSee($matchingMaterial->kode_barang)
            ->assertSee('18.00')
            ->assertDontSee($otherMaterial->kode_barang);

        $response = $this->get(route('laporan-stok.export.excel', $filters));
        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
        $this->assertStringStartsWith('PK', $response->streamedContent());
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
            'kode_barang' => 'TEST-'.Str::upper(Str::random(12)),
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
