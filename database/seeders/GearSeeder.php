<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Gear::create([
            'name' => 'Osprey Aether 65',
            'description' => 'The Aether 65 is the perfect gear-hauling solution when you need a pack that can carry heavy loads for a week or more.',
            'category' => 'Hiking',
            'price_per_day' => 12000,
            'stock' => 10,
        ]);

        \App\Models\Gear::create([
            'name' => 'The North Face Stormbreak 2',
            'description' => 'The Stormbreak 2 is our classic two-person camping tent.',
            'category' => 'Camping',
            'price_per_day' => 25000,
            'stock' => 5,
        ]);

        \App\Models\Gear::create([
            'name' => 'Black Diamond Momentum',
            'description' => 'The Momentum harness features a pre-threaded Speed Adjust waistbelt buckle.',
            'category' => 'Climbing',
            'price_per_day' => 8000,
            'stock' => 15,
        ]);
    }
}
