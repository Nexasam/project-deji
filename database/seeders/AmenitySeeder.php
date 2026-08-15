<?php

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $catalog = [
            'essentials' => [
                'wifi' => 'Wi-Fi',
                'air-conditioning' => 'Air conditioning',
                'kitchen' => 'Kitchen',
                'washing-machine' => 'Washing machine',
                'water-heater' => 'Water heater',
                'power-backup' => 'Power backup / Generator',
            ],
            'entertainment' => [
                'smart-tv' => 'Smart TV',
                'gaming-console' => 'Gaming console',
                'sound-system' => 'Sound system',
                'streaming-services' => 'Streaming services',
            ],
            'leisure' => [
                'swimming-pool' => 'Swimming pool',
                'gym' => 'Gym',
                'bbq-garden' => 'BBQ / Garden',
                'balcony-terrace' => 'Balcony / Terrace',
            ],
            'safety_security' => [
                'security' => '24/7 security',
                'cctv' => 'CCTV',
                'smoke-detector' => 'Smoke detector',
                'free-parking' => 'Free parking',
            ],
        ];

        foreach ($catalog as $category => $amenities) {
            foreach ($amenities as $code => $name) {
                $amenity = Amenity::query()->firstOrNew(['code' => $code]);
                $amenity->id ??= (string) Str::uuid();
                $amenity->fill(['name' => $name, 'category' => $category, 'status' => 'active'])->save();
            }
        }
    }
}
