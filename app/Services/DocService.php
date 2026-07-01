<?php

namespace App\Services;

class DocService
{
    use \App\Traits\LogsActivity;

    // --- Project Operations ---

    public function getAllProjects(array $filters = [], int $perPage = 10)
    {
        return \App\Models\DocProject::query()
            ->when(! empty($filters['search']), function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('slug', 'like', "%{$filters['search']}%");
            })
            ->withCount('versions')
            ->with('defaultVersion')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function createProject(array $data): \App\Models\DocProject
    {
        if (request()->hasFile('logo')) {
            $data['logo'] = request()->file('logo')->store('doc-projects', 'public');
        }

        $project = \App\Models\DocProject::create($data);
        $this->logActivity('create_doc_project', "Created documentation project: {$project->name}");

        return $project;
    }

    public function updateProject(\App\Models\DocProject $project, array $data): \App\Models\DocProject
    {
        if (request()->hasFile('logo')) {
            if ($project->logo) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($project->logo);
            }
            $data['logo'] = request()->file('logo')->store('doc-projects', 'public');
        } else {
            unset($data['logo']);
        }

        $project->update($data);
        $this->logActivity('update_doc_project', "Updated documentation project: {$project->name}");

        return $project;
    }

    public function deleteProject(\App\Models\DocProject $project): void
    {
        $this->logActivity('delete_doc_project', "Deleted documentation project: {$project->name}");
        $project->delete();
    }

    // --- Version Operations ---

    public function getVersionsForProject(\App\Models\DocProject $project)
    {
        return $project->versions()->withCount('categories')->latest()->get();
    }

    public function createVersion(array $data): \App\Models\DocVersion
    {
        if (! empty($data['is_default'])) {
            \App\Models\DocVersion::where('doc_project_id', $data['doc_project_id'])->update(['is_default' => false]);
        }

        /** @var \App\Models\DocVersion $version */
        $version = \App\Models\DocVersion::create($data);
        $this->logActivity('create_doc_version', "Created version {$version->name} for project ID {$version->doc_project_id}");

        // Automatically clone data from the previous latest version if it exists
        $previousVersion = \App\Models\DocVersion::where('doc_project_id', $version->doc_project_id)
            ->where('id', '!=', $version->id)
            ->latest()
            ->first();

        if ($previousVersion) {
            $this->cloneVersionData($previousVersion, $version);
        }

        return $version;
    }

    protected function cloneVersionData(\App\Models\DocVersion $source, \App\Models\DocVersion $target): void
    {
        // Clone Categories
        $categories = $source->categories()->orderBy('order')->get();

        foreach ($categories as $category) {
            $newCategory = $category->replicate(['doc_version_id', 'created_at', 'updated_at']);
            $newCategory->doc_version_id = $target->id;
            $newCategory->save();

            // Clone Pages for each Category
            $pages = $category->pages()->orderBy('order')->get();

            foreach ($pages as $page) {
                $newPage = $page->replicate(['doc_category_id', 'created_at', 'updated_at']);
                $newPage->doc_category_id = $newCategory->id;
                $newPage->save();

                // Create initial revision for the new page
                $newPage->revisions()->create([
                    'user_id' => \Illuminate\Support\Facades\Auth::id() ?? 1,
                    'content_json' => $newPage->content_json,
                    'content_html' => $newPage->content_html,
                    'change_summary' => "Cloned from version {$source->name}",
                    'is_snapshot' => true,
                ]);
            }
        }
    }

    public function updateVersion(\App\Models\DocVersion $version, array $data): \App\Models\DocVersion
    {
        if (! empty($data['is_default']) && ! $version->is_default) {
            \App\Models\DocVersion::where('doc_project_id', $version->doc_project_id)->update(['is_default' => false]);
        }

        $version->update($data);
        $this->logActivity('update_doc_version', "Updated version {$version->name}");

        return $version;
    }

    // --- Category Operations ---

    public function getCategoriesForVersion(\App\Models\DocVersion $version)
    {
        return $version->categories()->withCount('pages')->orderBy('order')->get();
    }

    public function createCategory(array $data): \App\Models\DocCategory
    {
        $category = \App\Models\DocCategory::create($data);

        return $category;
    }

    public function updateCategory(\App\Models\DocCategory $category, array $data): \App\Models\DocCategory
    {
        $category->update($data);

        return $category;
    }

    // --- Page Operations ---

    public function createPage(array $data): \App\Models\DocPage
    {
        /** @var \App\Models\DocPage $page */
        $page = \App\Models\DocPage::create($data);

        // Create initial revision
        $page->revisions()->create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'content_json' => $page->content_json,
            'content_html' => $page->content_html,
            'change_summary' => $data['change_summary'] ?? 'Initial creation',
            'is_snapshot' => true,
        ]);

        return $page;
    }

    public function updatePage(\App\Models\DocPage $page, array $data): \App\Models\DocPage
    {
        $page->update($data);

        // Create revision
        /** @var \App\Models\DocPage $page */
        $page->revisions()->create([
            'user_id' => \Illuminate\Support\Facades\Auth::id(),
            'content_json' => $page->content_json,
            'content_html' => $page->content_html,
            'change_summary' => $data['change_summary'] ?? 'Update',
            'is_snapshot' => ($data['status'] === 'published'),
        ]);

        return $page;
    }

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
}
