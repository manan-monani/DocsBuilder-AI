<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'business_name' => 'Docs',
            'logo_url' => 'branding/wU1Yc9Yt76d9IGwBed5Wxc61z.png',
            'favicon_url' => null, // Ensure favicon falls back to logo as per previous task
            'business_logo' => null, // Ensure fallback logic works
            'timezone' => 'UTC',
            'language' => 'en',
        ];

        foreach ($settings as $key => $value) {
            \Illuminate\Support\Facades\DB::table('business_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        \Illuminate\Support\Facades\Cache::forget('business_settings_all');
    }
}
