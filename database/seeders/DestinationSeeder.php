<?php

namespace Database\Seeders;

use App\Support\DestinationImporter;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        $result = (new DestinationImporter)->import(
            database_path('data/destinations-indonesia.csv')
        );

        $this->command?->info(
            "Destinations: {$result['created']} created, {$result['skipped']} skipped."
        );
    }
}
