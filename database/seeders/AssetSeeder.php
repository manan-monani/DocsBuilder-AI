<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sourceDir = database_path('seeders/assets');
        $destinationDir = storage_path('app/public');

        if (!File::exists($sourceDir)) {
            $this->command->warn("Assets directory not found at: $sourceDir");
            return;
        }

        // Ensure storage link exists
        if (!file_exists(public_path('storage'))) {
            Artisan::call('storage:link');
        }

        // Clean up default favicon files to avoid conflicts with dynamic branding
        if (file_exists(public_path('favicon.ico'))) {
            unlink(public_path('favicon.ico'));
        }
        if (file_exists(public_path('favicon.svg'))) {
            unlink(public_path('favicon.svg'));
        }

        $this->command->info('Copying assets from seeders/assets to storage/app/public...');

        File::copyDirectory($sourceDir, $destinationDir);

        $this->command->info('Assets copied successfully.');
    }
}
