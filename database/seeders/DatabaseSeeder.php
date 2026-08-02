<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CustomerSeeder::class,
            FreezerIntakeSeeder::class,
            RepairSeeder::class,
            RepairReassignmentSeeder::class,
            CompletedRepairSeeder::class,
            ComponentSeeder::class,
        ]);
    }
}
