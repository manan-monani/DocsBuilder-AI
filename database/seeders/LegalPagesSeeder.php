<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class LegalPagesSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'key' => 'privacy_policy',
                'value' => '<h1>Privacy Policy</h1><p>Welcome to our Privacy Policy. Your privacy is critically important to us.</p><h2>1. Information We Collect</h2><p>We collect information to provide better services to all our users. This includes information you provide to us directly and information we collect automatically.</p><h2>2. How We Use Information</h2><p>We use the information we collect to operate and improve our services, communicate with you, and ensure security.</p><h2>3. Information Sharing</h2><p>We do not share your personal information with companies, organizations, or individuals outside of our company except in the following cases: with your consent, for legal reasons, or with our trusted service providers.</p><h2>4. Data Security</h2><p>We work hard to protect our users from unauthorized access to or unauthorized alteration, disclosure, or destruction of information we hold.</p><h2>5. Changes</h2><p>Our Privacy Policy may change from time to time. We will post any privacy policy changes on this page.</p>',
                'type' => 'text',
            ],
            [
                'key' => 'terms_and_conditions',
                'value' => '<h1>Terms and Conditions</h1><p>Welcome to our Terms and Conditions. Please read these terms carefully before using our services.</p><h2>1. Acceptance of Terms</h2><p>By accessing or using our services, you agree to be bound by these terms. If you do not agree to all the terms and conditions, then you may not access the service.</p><h2>2. Use License</h2><p>Permission is granted to temporarily download one copy of the materials (information or software) on our website for personal, non-commercial transitory viewing only.</p><h2>3. Disclaimer</h2><p>The materials on our website are provided on an "as is" basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p><h2>4. Limitations</h2><p>In no event shall we or our suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on our website.</p><h2>5. Governing Law</h2><p>These terms and conditions are governed by and construed in accordance with the laws and you irrevocably submit to the exclusive jurisdiction of the courts in that location.</p>',
                'type' => 'text',
            ],
            [
                'key' => 'about_us',
                'value' => '<h1>About Us</h1><p>We are a dedicated team passionate about delivering the best software solutions to our customers.</p><h2>Our Mission</h2><p>Our mission is to empower businesses with innovative technology that drives growth and efficiency. We believe in simplicity, reliability, and continuous improvement.</p><h2>Our Story</h2><p>Founded with a vision to streamline complex processes, we started as a small team of developers and have grown into a leading provider of SaaS solutions. Our journey is defined by our commitment to our users and our relentless pursuit of excellence.</p><h2>Our Team</h2><p>Our team consists of experienced professionals from diverse backgrounds, all united by a common goal: to build software that makes a difference.</p><h2>Contact Us</h2><p>If you have any questions or would like to learn more about us, please do not hesitate to contact us.</p>',
                'type' => 'text',
            ],
        ];

        foreach ($settings as $setting) {
            BusinessSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                ]
            );
        }
    }
}
