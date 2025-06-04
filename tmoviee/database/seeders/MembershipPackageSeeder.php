<?php

namespace Database\Seeders;

use App\Models\MembershipPackage;
use Illuminate\Database\Seeder;

class MembershipPackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Gói Cơ Bản',
                'description' => 'Gói thành viên cơ bản với các tính năng thiết yếu',
                'price' => 99000,
                'duration_days' => 30,
                'features' => [
                    'Xem phim không quảng cáo',
                    'Chất lượng HD',
                    'Tải phim về xem offline'
                ],
                'is_active' => true
            ],
            [
                'name' => 'Gói Premium',
                'description' => 'Gói thành viên cao cấp với nhiều tính năng đặc biệt',
                'price' => 199000,
                'duration_days' => 30,
                'features' => [
                    'Tất cả tính năng của gói Cơ Bản',
                    'Chất lượng 4K',
                    'Xem phim sớm hơn',
                    'Hỗ trợ đa thiết bị'
                ],
                'is_active' => true
            ],
            [
                'name' => 'Gói Gia Đình',
                'description' => 'Gói thành viên dành cho gia đình',
                'price' => 299000,
                'duration_days' => 30,
                'features' => [
                    'Tất cả tính năng của gói Premium',
                    'Tối đa 4 tài khoản',
                    'Hồ sơ riêng cho từng thành viên',
                    'Kiểm soát nội dung theo độ tuổi'
                ],
                'is_active' => true
            ]
        ];

        foreach ($packages as $package) {
            MembershipPackage::create($package);
        }
    }
} 