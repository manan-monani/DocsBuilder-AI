<?php

namespace Database\Seeders;

use App\Models\DocProject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MartCMSDocsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Project
        $project = DocProject::firstOrCreate(
            ['slug' => 'mart-cms'],
            [
                'name' => 'MartCMS',
                'description' => 'Comprehensive documentation for MartCMS version 2.0.',
                'is_active' => true,
            ]
        );

        // 2. Create Version
        $version = $project->versions()->firstOrCreate(
            ['slug' => '2.0'],
            [
                'name' => 'Version 2.0',
                'is_default' => true,
                'is_archived' => false,
            ]
        );

        // 3. Define Categories
        $categories = [
            'Getting Started',
            'Restaurant & Branch Setup',
            'Food Management',
            'Menu Management',
            'Inventory & Recipe',
            'POS',
            'Website (Store Front)',
            'QR Code Menu',
            'Promotion Management',
            'Table & Reservation',
            'Order Management',
            'Employee Management',
            'Delivery Management',
            'Customer Management',
            'Supplier Management',
            'Reports & Analytics',
            'Taxes/VAT',
            'General Settings',
        ];

        // 4. Define Pages (Sample Data based on scraping)
        $pagesData = [
            'Getting Started' => [
                'Introduction',
                'How to Sign Up for a New Account',
                'How to Set Up Your Business Information in Menumium',
                'How to Log In for Existing Users',
                'How to Reset Your Password',
                'How to Sign In as an Employee',
            ],
            'Food Management' => [
                'How to Manage Default Labels',
                'How to Manage Customized Labels',
                'How to Create a Menu Type' => [
                    'content' => '
                        <h2>Step 1: Navigate to Menu Type</h2>
                        <p>Go to the Food Management section in the sidebar and click on <strong>Menu Type</strong>.</p>

                        <h2>Step 2: Create a New Menu Type</h2>
                        <p>Click on the <strong>Add New</strong> button to create a new menu type.</p>

                        <h2>Step 3: Fill in Menu Type Details</h2>
                        <p>Enter the <strong>Name</strong> of the menu type (e.g., "Breakfast", "Lunch").</p>
                        <p>Select the <strong>Status</strong> (Active/Inactive).</p>
                        <p>Click <strong>Save</strong>.</p>

                        <h2>Step 4: View the New Menu Type</h2>
                        <p>The new menu type will appear in the list below.</p>
                    ',
                ],
                'How to Manage a Menu Type',
                'How to Assign a Menu Type to a Food Item',
                'How to Delete and Restore a Menu Type',
                'How to Create a Category',
                'How to Get a Quick View of a Category',
                'How to Edit a Category in Food Management',
                'How to Delete a Category in Food Management',
                'How to Filter or Export Category in Food Management',
                'How to Give Basic Food Setup in Food Management',
                'How to Set Up Advanced Food Options in Food Management',
                'How to Update a Food Item in Food Management',
                'How to Manage Food List in Food Management',
                'How to Create a Cuisine',
                'How to Manage a Cuisine',
                'How to Create a Label',
            ],
            'Menu Management' => [
                'How to Create a New Menu',
                'How to Edit Menu Food',
                'How to Manage a Menu',
                'How to Delete a Menu',
            ],
            'Order Management' => [
                'How to View and Manage Point of Sale (POS) Orders',
                'How to Update POS Order Status',
                'How to View & Manage Online Orders',
                'How to Update an Online Order Status',
                'How Can Staff Download the Invoice of Online Orders',
                'How to Edit the Food of an Order',
                'How to Assign a Delivery Man to an Order',
                'How to Get Branch-Specific Order Reports?',
            ],
            'Employee Management' => [
                'How to Set Up Employee and Role in Menumium',
                'How to Edit Employee Roles in Menumium',
                'How to Disable or Enable a User Role in Menumium',
                'How to Invite an Employee for a Role in Menumium',
                'How to Complete the Employee Registration Form',
                'Where Can I Find the Employee List?',
                'How to Manage Employee Lists',
                'How to View the Invited Employee List and Manage It',
            ],
            'Delivery Management' => [
                'How to Create a New Delivery Man',
                'How to Quickly Access Deliveryman Details',
                'How to Update a Deliveryman’s Account Details',
                'How to Assign a New Order',
                'How to See a Deliveryman’s Status',
                'How to Temporarily Delete a Deliveryman',
                'How to Restore a Delivery Man',
            ],
            'Inventory & Recipe' => [
                'How to Set Up an Ingredient Category',
                'How to Edit an Ingredient Category in Menumium',
                'How to Filter Ingredients Categories in Menumium',
                'How to Restore Deleted Inventory Categories',
            ],
        ];

        foreach ($categories as $index => $catName) {
            $category = $version->categories()->updateOrCreate(
                ['name' => $catName],
                [
                    'slug' => Str::slug($catName),
                    'order' => $index + 1,
                    // 'icon' => 'folder', // Optional if icon column is nullable or has default
                ]
            );

            // Add pages if available, otherwise just one placeholder
            if (isset($pagesData[$catName])) {
                foreach ($pagesData[$catName] as $key => $pageInfo) {
                    $title = is_string($key) ? $key : $pageInfo;

                    if (is_array($pageInfo) && isset($pageInfo['content'])) {
                        $content = $pageInfo['content'];
                    } else {
                        $content = $this->generateDummyContent($title);
                    }

                    $this->createPage($category, $title, $content, $loop->iteration ?? 1);
                }
            } else {
                $this->createPage($category, "Introduction to $catName", $this->generateDummyContent("Introduction to $catName"), 1);
            }
        }
    }

    private function generateDummyContent($title)
    {
        return '
            <p>Welcome to the documentation for <strong>'.$title.'</strong>. This guide will help you understand the core concepts and configuration options available in MartCMS.</p>

            <h2>Overview</h2>
            <p>The '.$title.' feature allows administrators to efficiently manage their platform. By leveraging this functionality, you can streamline operations and enhance user experience.</p>

            <h2>Key Features</h2>
            <ul>
                <li><strong>Easy Configuration:</strong> Simple tools to set up and manage settings.</li>
                <li><strong>Real-time Updates:</strong> Changes reflect immediately across the system.</li>
                <li><strong>Comprehensive Reporting:</strong> Track performance and usage statistics.</li>
            </ul>

            <h2>Step-by-Step Guide</h2>
            <p>Follow these steps to get started with '.$title.':</p>

            <h3>1. Accessing the Feature</h3>
            <p>Navigate to the relevant section in the admin panel. Look for the '.$title.' link in the sidebar.</p>

            <h3>2. Configuration</h3>
            <p>Adjust the settings according to your business requirements. Ensure all mandatory fields are filled out correctly.</p>

            <h3>3. Saving Changes</h3>
            <p>Once configured, click the "Save" button to apply your changes. You will receive a success notification.</p>

            <h2>Troubleshooting</h2>
            <p>If you encounter issues, check the following:</p>
            <ul>
                <li>Ensure you have the necessary permissions.</li>
                <li>Verify your internet connection.</li>
                <li>Clear your browser cache if updates do not appear.</li>
            </ul>

            <h2>Frequently Asked Questions</h2>
            <h3>Can I revert changes?</h3>
            <p>Yes, most settings allow you to revert to previous configurations or default values.</p>

            <h3>Is this feature available on mobile?</h3>
            <p>Yes, the admin panel is fully responsive and supports mobile devices.</p>
        ';
    }

    private function createPage($category, $title, $contentHtml, $order)
    {
        $page = $category->pages()->firstOrCreate(
            ['slug' => Str::slug($title)],
            [
                'title' => $title,
                'content_html' => $contentHtml,
                'content_json' => json_encode(['type' => 'doc', 'content' => []]), // Placeholder
                'order' => $order,
                'status' => 'published',
            ]
        );

        // Update content if it was a placeholder/generated before
        if ($page->wasRecentlyCreated === false) {
            $page->update(['content_html' => $contentHtml]);
        }

        // Create revision if not exists
        if ($page->revisions()->count() === 0) {
            $page->revisions()->create([
                'user_id' => \App\Models\User::first()?->id ?? 1,
                'content_json' => $page->content_json,
                'content_html' => $page->content_html,
                'change_summary' => 'Initial import',
                'is_snapshot' => true,
            ]);
        }
    }
}
