<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class LegalPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'privacy_policy',
                'value' => '<h1>Privacy Policy</h1><p><strong>Effective Date: ' . date('F d, Y') . '</strong></p><p>At DocsBuilder AI, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website and use our documentation platform.</p><h2>1. Information We Collect</h2><p>We may collect information about you in a variety of ways. The information we may collect includes:</p><ul><li><strong>Personal Data:</strong> Personally identifiable information, such as your name, shipping address, email address, and telephone number, and demographic information, such as your age, gender, hometown, and interests, that you voluntarily give to us when you register with the Site or when you choose to participate in various activities related to the Site and our mobile application.</li><li><strong>Derivative Data:</strong> Information our servers automatically collect when you access the Site, such as your IP address, your browser type, your operating system, your access times, and the pages you have viewed directly before and after accessing the Site.</li></ul><h2>2. Use of Your Information</h2><p>Having accurate information about you permits us to provide you with a smooth, efficient, and customized experience. We may use information collected about you via the Site to:</p><ul><li>Create and manage your account.</li><li>Email you regarding your account or order.</li><li>Fulfill and manage purchases, orders, payments, and other transactions related to the Site.</li><li>Increase the efficiency and operation of the Site.</li><li>Monitor and analyze usage and trends to improve your experience with the Site.</li></ul><h2>3. Disclosure of Your Information</h2><p>We may share information we have collected about you in certain situations. Your information may be disclosed as follows:</p><ul><li><strong>By Law or to Protect Rights:</strong> If we believe the release of information about you is necessary to respond to legal process, to investigate or remedy potential violations of our policies, or to protect the rights, property, and safety of others, we may share your information as permitted or required by any applicable law, rule, or regulation.</li><li><strong>Third-Party Service Providers:</strong> We may share your information with third parties that perform services for us or on our behalf, including payment processing, data analysis, email delivery, hosting services, customer service, and marketing assistance.</li></ul><h2>4. Security of Your Information</h2><p>We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that despite our efforts, no security measures are perfect or impenetrable, and no method of data transmission can be guaranteed against any interception or other type of misuse.</p><h2>5. Contact Us</h2><p>If you have questions or comments about this Privacy Policy, please contact us at support@docsbuilder.ai.</p>',
            ],
            [
                'key' => 'terms_and_conditions',
                'value' => '<h1>Terms and Conditions</h1><p><strong>Last Updated: ' . date('F d, Y') . '</strong></p><p>Welcome to DocsBuilder AI!</p><p>These terms and conditions outline the rules and regulations for the use of DocsBuilder AI\'s Website and Services.</p><h2>1. Agreement to Terms</h2><p>By accessing or using our services, you agree to be bound by these Terms and Conditions. If you disagree with any part of the terms, then you may not access the Service.</p><h2>2. Intellectual Property Rights</h2><p>Unless otherwise stated, DocsBuilder AI and/or its licensors own the intellectual property rights for all material on DocsBuilder AI. All intellectual property rights are reserved. You may access this from DocsBuilder AI for your own personal use subjected to restrictions set in these terms and conditions.</p><h2>3. User Responsibilities</h2><p>You agree not to use the Service for any purpose that is unlawful or prohibited by these Terms. You verify that you are at least 18 years of age and have the legal authority to accept these Terms.</p><h2>4. Limitation of Liability</h2><p>In no event shall DocsBuilder AI, nor any of its officers, directors, and employees, be held liable for anything arising out of or in any way connected with your use of this Website whether such liability is under contract. DocsBuilder AI, including its officers, directors, and employees shall not be held liable for any indirect, consequential, or special liability arising out of or in any way related to your use of this Website.</p><h2>5. Termination</h2><p>We may terminate or suspend access to our Service immediately, without prior notice or liability, for any reason whatsoever, including without limitation if you breach the Terms.</p><h2>6. Governing Law</h2><p>These Terms shall be governed and construed in accordance with the laws of the jurisdiction in which DocsBuilder AI operates, without regard to its conflict of law provisions.</p><h2>7. Changes to Terms</h2><p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. By continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms.</p>',
            ],
            [
                'key' => 'about_us',
                'value' => '<h1>About DocsBuilder AI</h1><p>Welcome to DocsBuilder AI, the intelligent platform designed to revolutionize how developers and businesses create, manage, and share documentation.</p><h2>Our Mission</h2><p>Our mission is to empower teams to build beautiful, comprehensive documentation effortlessly using the power of AI. We believe that great documentation is the bridge between complex technology and successful adoption.</p><h2>What We Do</h2><p>DocsBuilder AI provides a seamless interface for generating, organizing, and versioning documentation. Whether you are documenting an API, a software library, or internal processes, our tools are built to enhance clarity and collaboration.</p><h2>Our Story</h2><p>Born from the frustration of outdated and cumbersome documentation tools, DocsBuilder AI was founded in 2024 by a team of passionate developers. We saw the need for a solution that adapts to modern development workflows and leverages artificial intelligence to reduce the manual burden of writing and maintaining docs.</p><h2>Why Choose Us?</h2><ul><li><strong>AI-Powered:</strong> Leverage advanced AI to generate content and improve clarity.</li><li><strong>Developer-Centric:</strong> Built by developers, for developers, with strong API support and markdown compatibility.</li><li><strong>Secure & Scalable:</strong> Enterprise-grade security and scalability to grow with your business.</li></ul><p>Join us on our journey to make documentation better for everyone.</p>',
            ],
            [
                'key' => 'contact_email',
                'value' => 'support@docsbuilder.ai',
            ],
            [
                'key' => 'contact_phone',
                'value' => '+1 (555) 123-4567',
            ],
            [
                'key' => 'contact_address',
                'value' => '123 Innovation Drive, Tech Valley, CA 94043',
            ],
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/docsbuilder',
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/docsbuilder',
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/docsbuilder',
            ],
        ];

        foreach ($settings as $setting) {
            BusinessSetting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
