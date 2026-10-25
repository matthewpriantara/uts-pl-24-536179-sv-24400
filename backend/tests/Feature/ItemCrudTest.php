<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ItemCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Sanctum::actingAs($this->user);
    }

    public function test_authenticated_user_can_get_all_items(): void
    {
        Item::factory()->count(3)->create();

        $response = $this->getJson('/api/items');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'name', 'sku', 'category', 'stock', 'price', 'description', 'created_at', 'updated_at'],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_authenticated_user_can_create_new_item_with_201_status(): void
    {
        $payload = [
            'name' => 'Meja Ergonomis Otomatis',
            'sku' => 'MEJA-001',
            'category' => 'Furniture',
            'stock' => 10,
            'price' => 2500000.00,
            'description' => 'Meja kerja adjustable height motorized.',
        ];

        $response = $this->postJson('/api/items', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Barang berhasil ditambahkan ke inventaris.',
                'data' => [
                    'name' => 'Meja Ergonomis Otomatis',
                    'sku' => 'MEJA-001',
                    'category' => 'Furniture',
                    'stock' => 10,
                ],
            ]);

        $this->assertDatabaseHas('items', [
            'sku' => 'MEJA-001',
            'name' => 'Meja Ergonomis Otomatis',
        ]);
    }

    public function test_creating_item_with_invalid_data_returns_422_status(): void
    {
        $response = $this->postJson('/api/items', [
            'name' => '',
            'sku' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'sku', 'category', 'stock', 'price']);
    }

    public function test_authenticated_user_can_view_single_item(): void
    {
        $item = Item::factory()->create([
            'name' => 'Laptop Gaming ROG',
            'sku' => 'ROG-001',
        ]);

        $response = $this->getJson("/api/items/{$item->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Detail barang berhasil diambil.',
                'data' => [
                    'id' => $item->id,
                    'sku' => 'ROG-001',
                ],
            ]);
    }

    public function test_authenticated_user_can_update_item(): void
    {
        $item = Item::factory()->create([
            'name' => 'Laptop Lama',
            'stock' => 5,
        ]);

        $updatePayload = [
            'name' => 'Laptop Baru Diperbarui',
            'sku' => $item->sku,
            'category' => $item->category,
            'stock' => 15,
            'price' => 15000000.00,
            'description' => 'Spesifikasi telah diperbarui.',
        ];

        $response = $this->putJson("/api/items/{$item->id}", $updatePayload);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Barang berhasil diperbarui.',
                'data' => [
                    'name' => 'Laptop Baru Diperbarui',
                    'stock' => 15,
                ],
            ]);

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Laptop Baru Diperbarui',
            'stock' => 15,
        ]);
    }

    public function test_authenticated_user_can_delete_item(): void
    {
        $item = Item::factory()->create();

        $response = $this->deleteJson("/api/items/{$item->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Barang berhasil dihapus dari inventaris.',
            ]);

        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
        ]);
    }
}
