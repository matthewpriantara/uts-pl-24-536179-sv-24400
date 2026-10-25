<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin User for API and Frontend Authentication
        User::firstOrCreate(
            ['email' => 'admin@kepl.test'],
            [
                'name' => 'Admin Gudang',
                'password' => Hash::make('password123'),
            ]
        );

        // Initial Inventory Items
        $items = [
            [
                'name' => 'Laptop ASUS ROG Strix G16',
                'sku' => 'BRG-001',
                'category' => 'Elektronik',
                'stock' => 15,
                'price' => 24500000.00,
                'description' => 'Intel Core i9-13980HX, RTX 4070 8GB, 32GB RAM DDR5, 1TB NVMe SSD.',
            ],
            [
                'name' => 'Meja Kerja Ergonomis Standing Desk',
                'sku' => 'BRG-002',
                'category' => 'Furniture',
                'stock' => 30,
                'price' => 1850000.00,
                'description' => 'Motorized height adjustable table 140x60cm, memory preset, cable management.',
            ],
            [
                'name' => 'Kursi Ergonomis Mesh Premium',
                'sku' => 'BRG-003',
                'category' => 'Furniture',
                'stock' => 25,
                'price' => 2750000.00,
                'description' => '3D armrest, lumbar support dinamis, breathable mesh fabric.',
            ],
            [
                'name' => 'Monitor Dell UltraSharp 27 4K U2723QE',
                'sku' => 'BRG-004',
                'category' => 'Elektronik',
                'stock' => 20,
                'price' => 8900000.00,
                'description' => 'IPS Black panel 4K UHD, 98% DCI-P3, USB-C 90W Power Delivery hub.',
            ],
            [
                'name' => 'Barcode Scanner Wireless 2D Handheld',
                'sku' => 'BRG-005',
                'category' => 'Perkakas',
                'stock' => 50,
                'price' => 750000.00,
                'description' => '2.4G & Bluetooth connectivity, support QR Code, Data Matrix, GS1 Databar.',
            ],
        ];

        foreach ($items as $item) {
            Item::firstOrCreate(['sku' => $item['sku']], $item);
        }
    }
}
