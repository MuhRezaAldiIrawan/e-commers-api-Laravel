<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Headset Gaming HyperX',
                'description' => 'Headset gaming dengan mic noise cancelling dan sound quality premium',
                'price' => 500000,
                'stock' => 40,
            ],
            [
                'name' => 'Webcam Logitech C920',
                'description' => 'Webcam Full HD 1080p untuk streaming dan video conference',
                'price' => 1200000,
                'stock' => 25,
            ],
            [
                'name' => 'SSD Samsung 1TB',
                'description' => 'SSD NVMe M.2 dengan kecepatan read/write hingga 3500MB/s',
                'price' => 1800000,
                'stock' => 35,
            ],
            [
                'name' => 'RAM Corsair Vengeance 16GB',
                'description' => 'RAM DDR4 16GB (2x8GB) 3200MHz untuk gaming dan multitasking',
                'price' => 950000,
                'stock' => 45,
            ],
            [
                'name' => 'Mousepad Gaming XL',
                'description' => 'Mousepad gaming ukuran besar dengan permukaan halus',
                'price' => 150000,
                'stock' => 100,
            ],
            [
                'name' => 'Microphone USB Condenser',
                'description' => 'Microphone USB untuk streaming, podcasting, dan recording',
                'price' => 850000,
                'stock' => 20,
            ],
        ];

        foreach ($products as $product) {
            \App\Models\Product::create($product);
        }
    }
}
