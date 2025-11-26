<?php

namespace Database\Seeders;

use App\Models\Buyer;
use App\Models\Organizer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test buyer
        Buyer::factory()->create([
            'name' => 'Test Buyer',
            'email' => 'buyer@test.com',
        ]);

        // Create test organizer
        Organizer::factory()->create([
            'name' => 'Test Organizer',
            'email' => 'organizer@test.com',
        ]);
    }
}
