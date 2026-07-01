<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateSeedersFromDb extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'boost:generate-seeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate seeders from current database state';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating seeders...');

        $this->generateAuthSeeder();
        $this->generateDocsSeeder();

        $this->info('Seeders generated successfully!');
    }

    private function generateAuthSeeder()
    {
        $users = \App\Models\User::all()->makeVisible(['password', 'remember_token'])->toArray();
        $roles = \App\Models\Role::all()->toArray();
        $permissions = \App\Models\Permission::all()->toArray();

        $rolePermissions = DB::table('role_permissions')->get()->map(function ($item) {
            return (array) $item;
        })->toArray();

        $userRoles = DB::table('user_roles')->get()->map(function ($item) {
            return (array) $item;
        })->toArray();

        $adminProfiles = \App\Models\AdminProfile::all()->toArray();
        $customerProfiles = \App\Models\CustomerProfile::all()->toArray();

        $className = 'AuthModuleSeeder';

        $content = "<?php\n\nnamespace Database\Seeders\Exported;\n\n";
        $content .= "use Illuminate\Database\Seeder;\n";
        $content .= "use Illuminate\Support\Facades\DB;\n";
        $content .= "use App\Models\User;\n";
        $content .= "use App\Models\Role;\n";
        $content .= "use App\Models\Permission;\n";
        $content .= "use App\Models\AdminProfile;\n";
        $content .= "use App\Models\CustomerProfile;\n\n";

        $content .= "class $className extends Seeder\n{\n";
        $content .= "    public function run(): void\n    {\n";
        $content .= "        DB::transaction(function () {\n";

        // Permissions
        $content .= "            // Permissions\n";
        $content .= '            $permissions = '.$this->exportArray($permissions).";\n";
        $content .= "            foreach (\$permissions as \$permission) {\n";
        $content .= "                Permission::firstOrCreate(['id' => \$permission['id']], \$permission);\n";
        $content .= "            }\n\n";

        // Roles
        $content .= "            // Roles\n";
        $content .= '            $roles = '.$this->exportArray($roles).";\n";
        $content .= "            foreach (\$roles as \$role) {\n";
        $content .= "                Role::firstOrCreate(['id' => \$role['id']], \$role);\n";
        $content .= "            }\n\n";

        // Role Permissions
        $content .= "            // Role Permissions\n";
        $content .= '            $rolePermissions = '.$this->exportArray($rolePermissions).";\n";
        $content .= "            DB::table('role_permissions')->insertOrIgnore(\$rolePermissions);\n\n";

        // Users
        $content .= "            // Users\n";
        $content .= '            $users = '.$this->exportArray($users).";\n";
        $content .= "            foreach (\$users as \$user) {\n";
        $content .= "                User::firstOrCreate(['id' => \$user['id']], \$user);\n";
        $content .= "            }\n\n";

        // User Roles
        $content .= "            // User Roles\n";
        $content .= '            $userRoles = '.$this->exportArray($userRoles).";\n";
        $content .= "            DB::table('user_roles')->insertOrIgnore(\$userRoles);\n\n";

        // Admin Profiles
        $content .= "            // Admin Profiles\n";
        $content .= '            $adminProfiles = '.$this->exportArray($adminProfiles).";\n";
        $content .= "            foreach (\$adminProfiles as \$profile) {\n";
        $content .= "                AdminProfile::firstOrCreate(['id' => \$profile['id']], \$profile);\n";
        $content .= "            }\n\n";

        // Customer Profiles
        $content .= "            // Customer Profiles\n";
        $content .= '            $customerProfiles = '.$this->exportArray($customerProfiles).";\n";
        $content .= "            foreach (\$customerProfiles as \$profile) {\n";
        $content .= "                CustomerProfile::firstOrCreate(['id' => \$profile['id']], \$profile);\n";
        $content .= "            }\n";

        $content .= "        });\n    }\n}\n";

        $this->writeSeederFile($className, $content);
    }

    private function generateDocsSeeder()
    {
        $projects = \App\Models\DocProject::all()->toArray();
        $versions = \App\Models\DocVersion::all()->toArray();
        $categories = \App\Models\DocCategory::all()->toArray();
        $pages = \App\Models\DocPage::all()->toArray();
        $revisions = \App\Models\DocRevision::all()->toArray();

        $className = 'DocsModuleSeeder';

        $content = "<?php\n\nnamespace Database\Seeders\Exported;\n\n";
        $content .= "use Illuminate\Database\Seeder;\n";
        $content .= "use Illuminate\Support\Facades\DB;\n";
        $content .= "use App\Models\DocProject;\n";
        $content .= "use App\Models\DocVersion;\n";
        $content .= "use App\Models\DocCategory;\n";
        $content .= "use App\Models\DocPage;\n";
        $content .= "use App\Models\DocRevision;\n\n";

        $content .= "class $className extends Seeder\n{\n";
        $content .= "    public function run(): void\n    {\n";
        $content .= "        DB::transaction(function () {\n";

        // Projects
        $content .= "            // Projects\n";
        $content .= '            $projects = '.$this->exportArray($projects).";\n";
        $content .= "            foreach (\$projects as \$project) {\n";
        $content .= "                DocProject::firstOrCreate(['id' => \$project['id']], \$project);\n";
        $content .= "            }\n\n";

        // Versions
        $content .= "            // Versions\n";
        $content .= '            $versions = '.$this->exportArray($versions).";\n";
        $content .= "            foreach (\$versions as \$version) {\n";
        $content .= "                DocVersion::firstOrCreate(['id' => \$version['id']], \$version);\n";
        $content .= "            }\n\n";

        // Categories
        $content .= "            // Categories\n";
        $content .= '            $categories = '.$this->exportArray($categories).";\n";
        $content .= "            foreach (\$categories as \$category) {\n";
        $content .= "                DocCategory::firstOrCreate(['id' => \$category['id']], \$category);\n";
        $content .= "            }\n\n";

        // Pages
        $content .= "            // Pages\n";
        $content .= '            $pages = '.$this->exportArray($pages).";\n";
        $content .= "            foreach (\$pages as \$page) {\n";
        $content .= "                DocPage::firstOrCreate(['id' => \$page['id']], \$page);\n";
        $content .= "            }\n\n";

        // Revisions
        $content .= "            // Revisions\n";
        $content .= '            $revisions = '.$this->exportArray($revisions).";\n";
        $content .= "            foreach (\$revisions as \$revision) {\n";
        $content .= "                DocRevision::firstOrCreate(['id' => \$revision['id']], \$revision);\n";
        $content .= "            }\n";

        $content .= "        });\n    }\n}\n";

        $this->writeSeederFile($className, $content);
    }

    private function exportArray(array $data)
    {
        return var_export($data, true);
    }

    private function writeSeederFile($className, $content)
    {
        $directory = database_path('seeders/Exported');
        if (! file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $path = $directory.'/'.$className.'.php';
        file_put_contents($path, $content);

        $this->info("Generated $className.php at $path");
    }
}
