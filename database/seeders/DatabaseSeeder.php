<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'Bàn phím' => [
                ['Bàn phím cơ Mini 68', 790000, 18],
                ['Bàn phím văn phòng không dây', 390000, 32],
                ['Bàn phím cơ RGB TKL', 1090000, 14],
            ],
            'Chuột máy tính' => [
                ['Chuột không dây Silent', 280000, 45],
                ['Chuột gaming 6 nút', 490000, 27],
                ['Chuột Bluetooth Dual Mode', 410000, 20],
            ],
            'Âm thanh' => [
                ['Tai nghe chụp tai Studio', 890000, 16],
                ['Tai nghe có dây Type-C', 190000, 50],
                ['Loa Bluetooth Compact', 590000, 22],
            ],
            'Phụ kiện' => [
                ['Đế laptop nhôm gấp gọn', 320000, 24],
                ['Cáp USB-C 100W 2m', 150000, 60],
                ['Hub USB-C 5 trong 1', 650000, 19],
                ['Webcam Full HD', 720000, 12],
                ['Lót chuột XL', 130000, 35],
                ['Ổ cứng SSD di động 512GB', 1290000, 10],
            ],
        ];

        foreach ($catalog as $categoryName => $products) {
            $category = Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)],
                ['name' => $categoryName, 'description' => 'Sản phẩm ' . mb_strtolower($categoryName) . ' chính hãng.'],
            );

            foreach ($products as [$name, $price, $stock]) {
                Product::firstOrCreate(
                    ['slug' => Str::slug($name)],
                    [
                        'category_id' => $category->id,
                        'name' => $name,
                        'description' => 'Thiết kế hiện đại, dễ sử dụng. Bảo hành theo chính sách của cửa hàng.',
                        'price' => $price,
                        'stock' => $stock,
                        'is_active' => true,
                    ],
                );
            }
        }

        // Set both variables in .env for a LOCAL DEMO only; no hard-coded default credentials.
        $email = env('SEED_ADMIN_EMAIL');
        $password = env('SEED_ADMIN_PASSWORD');
        if ($email && $password && strlen($password) >= 12) {
            User::updateOrCreate(
                ['email' => $email],
                ['name' => 'Quản trị OpenShop', 'role' => 'admin', 'password' => Hash::make($password)],
            );
        } else {
            $this->command?->warn('Chưa tạo Admin. Đặt SEED_ADMIN_EMAIL và SEED_ADMIN_PASSWORD (>=12 ký tự) trong .env rồi chạy php artisan db:seed.');
        }
    }
}
