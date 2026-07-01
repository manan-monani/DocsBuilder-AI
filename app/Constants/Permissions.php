<?php

namespace App\Constants;

class Permissions
{
    // Section: System
    public const SECTION_SYSTEM = 'system_section';

    public const DASHBOARD_VIEW = 'dashboard_view';

    public const ACTIVITY_LOG = 'activity_log';

    // Section: Account
    public const SECTION_ACCOUNT = 'account_section';

    public const MEMBER_DIRECTORY = 'member_directory';

    public const ACCESS_ROLES = 'access_roles';

    // Section: Business
    public const SECTION_BUSINESS = 'business_settings';

    public const BUSINESS_BRANDING = 'business_branding';

    public const LEGAL_MANAGEMENT = 'legal_management';

    public const BUSINESS_LOGIC = 'business_logic';

    // Section: Documentation
    public const SECTION_DOCS = 'docs_section';

    public const DOCUMENTATION_MANAGEMENT = 'documentation_management';

    public const DOC_PROJECTS = 'doc_projects';

    public const DOC_VERSIONS = 'doc_versions';

    public const DOC_CATEGORIES = 'doc_categories';

    public const DOC_PAGES = 'doc_pages';

    public static function getAll(): array
    {
        return [
            self::SECTION_SYSTEM => [
                'label' => 'system_section',
                'icon' => 'LayoutDashboard',
                'sub_modules' => [
                    self::DASHBOARD_VIEW => [
                        'label' => 'Dashboard',
                        'description' => 'View system health and metrics',
                        'route' => 'admin.dashboard',
                        'icon' => 'Activity',
                    ],
                    self::DOC_PROJECTS => [
                        'label' => 'doc_projects',
                        'description' => 'Manage documentation projects',
                        'route' => 'admin.docs.projects.index',
                        'icon' => 'Book',
                    ],
                    self::DOC_VERSIONS => [
                        'label' => 'doc_versions',
                        'description' => 'Manage documentation versions',
                        'route' => 'admin.docs.versions.index',
                        'icon' => 'Layers',
                    ],
                    self::DOC_CATEGORIES => [
                        'label' => 'doc_categories',
                        'description' => 'Manage documentation categories',
                        'route' => 'admin.docs.categories.index',
                        'icon' => 'Folder',
                    ],
                    self::DOC_PAGES => [
                        'label' => 'doc_pages',
                        'description' => 'Manage documentation pages',
                        'route' => 'admin.docs.pages.index',
                        'icon' => 'FileText',
                    ],
                ],
            ],
            self::SECTION_ACCOUNT => [
                'label' => 'account', // Using 'account' as section label key, or management_section? Sidebar uses management_section for title, but account for rail tooltip.
                // Sidebar uses 'account' for rail tooltip, 'management_section' for Tier 2 title.
                // Permissions structure has label 'account'. I'll stick to 'account' and handle Tier 2 title logic in frontend or mapping.
                'icon' => 'Users',
                'sub_modules' => [
                    self::MEMBER_DIRECTORY => [
                        'label' => 'member_directory',
                        'description' => 'Manage member identities and access levels',
                        'route' => 'admin.users.index',
                        'icon' => 'Users',
                    ],
                    self::ACCESS_ROLES => [
                        'label' => 'access_roles',
                        'description' => 'Configure roles and security boundaries',
                        'route' => 'admin.roles.index',
                        'icon' => 'ShieldCheck',
                    ],
                ],
            ],
            self::SECTION_BUSINESS => [
                'label' => 'business_settings',
                'icon' => 'Briefcase',
                'sub_modules' => [
                    self::BUSINESS_BRANDING => [
                        'label' => 'business_branding',
                        'description' => 'Manage organization visual identity',
                        'route' => 'admin.business.branding', // Check route name
                        'icon' => 'Palette',
                    ],
                    self::LEGAL_MANAGEMENT => [
                        'label' => 'legal_management',
                        'description' => 'Manage legal documents (Privacy, Terms, etc.)',
                        'route' => 'admin.legal.index',
                        'icon' => 'Scale',
                    ],
                    self::BUSINESS_LOGIC => [
                        'label' => 'business_logic',
                        'description' => 'Manage business settings (Currency, Timezone, Country)',
                        'route' => 'admin.business.settings.index',
                        'icon' => 'Settings',
                    ],
                    self::ACTIVITY_LOG => [
                        'label' => 'activity_log',
                        'description' => 'View system activity logs and audit trail',
                        'route' => 'admin.activity_logs.index',
                        'icon' => 'History',
                    ],

                ],
            ],
        ];
    }
}
