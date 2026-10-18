<?php

namespace App\Console\Commands;

use App\Support\DestinationImporter;
use Illuminate\Console\Command;
use Throwable;

class ImportDestinations extends Command
{
    protected $signature = 'destinations:import {file? : Path to the destinations CSV file}';

    protected $description = 'Import destinations from a CSV file (existing slugs are skipped).';

    public function handle(DestinationImporter $importer): int
    {
        $file = $this->argument('file') ?? database_path('data/destinations-indonesia.csv');

        try {
            $result = $importer->import($file);
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Imported {$result['created']} destinations from {$file}.");
        $this->line("Skipped {$result['skipped']} destinations that already exist.");
        $this->line("Available categories: {$result['categories']}.");

        return self::SUCCESS;
    }
}
