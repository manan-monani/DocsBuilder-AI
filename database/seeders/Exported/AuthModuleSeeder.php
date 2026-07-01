<?php

namespace Database\Seeders\Exported;

use App\Models\AdminProfile;
use App\Models\CustomerProfile;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthModuleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Permissions
            $permissions = [
                0 => [
                    'id' => 1,
                    'name' => 'dashboard_view',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                1 => [
                    'id' => 2,
                    'name' => 'doc_projects',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                2 => [
                    'id' => 3,
                    'name' => 'doc_versions',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                3 => [
                    'id' => 4,
                    'name' => 'doc_categories',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                4 => [
                    'id' => 5,
                    'name' => 'doc_pages',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                5 => [
                    'id' => 6,
                    'name' => 'member_directory',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                6 => [
                    'id' => 7,
                    'name' => 'access_roles',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                7 => [
                    'id' => 8,
                    'name' => 'business_branding',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                8 => [
                    'id' => 9,
                    'name' => 'legal_management',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                9 => [
                    'id' => 10,
                    'name' => 'business_logic',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
                10 => [
                    'id' => 11,
                    'name' => 'activity_log',
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
            ];
            foreach ($permissions as $permission) {
                Permission::firstOrCreate(['id' => $permission['id']], $permission);
            }

            // Roles
            $roles = [
                0 => [
                    'id' => 1,
                    'name' => 'Manager',
                    'description' => 'General Manager with full administrative access',
                    'slug' => 'manager',
                    'permissions' => null,
                    'created_at' => '2026-02-09T09:06:28.000000Z',
                    'updated_at' => '2026-02-09T09:06:28.000000Z',
                ],
            ];
            foreach ($roles as $role) {
                Role::firstOrCreate(['id' => $role['id']], $role);
            }

            // Role Permissions
            $rolePermissions = [
                0 => [
                    'role_id' => 1,
                    'permission_id' => 1,
                ],
                1 => [
                    'role_id' => 1,
                    'permission_id' => 2,
                ],
                2 => [
                    'role_id' => 1,
                    'permission_id' => 3,
                ],
                3 => [
                    'role_id' => 1,
                    'permission_id' => 4,
                ],
                4 => [
                    'role_id' => 1,
                    'permission_id' => 5,
                ],
                5 => [
                    'role_id' => 1,
                    'permission_id' => 6,
                ],
                6 => [
                    'role_id' => 1,
                    'permission_id' => 7,
                ],
                7 => [
                    'role_id' => 1,
                    'permission_id' => 8,
                ],
                8 => [
                    'role_id' => 1,
                    'permission_id' => 9,
                ],
                9 => [
                    'role_id' => 1,
                    'permission_id' => 10,
                ],
                10 => [
                    'role_id' => 1,
                    'permission_id' => 11,
                ],
            ];
            DB::table('role_permissions')->insertOrIgnore($rolePermissions);
        });
    }
}
