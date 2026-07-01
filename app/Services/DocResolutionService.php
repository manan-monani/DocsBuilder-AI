<?php

namespace App\Services;

class DocResolutionService
{
    public function resolvePage(string $projectSlug, string $versionSlug, string $categorySlug, string $pageSlug): array
    {
        /** @var \App\Models\DocProject $project */
        $project = \App\Models\DocProject::where('slug', $projectSlug)->where('is_active', true)->firstOrFail();

        /** @var \App\Models\DocVersion $version */
        $version = $project->versions()->where('slug', $versionSlug)->firstOrFail();

        /** @var \App\Models\DocCategory $category */
        $category = $version->categories()->where('slug', $categorySlug)->firstOrFail();

        /** @var \App\Models\DocPage $page */
        $page = $category->pages()->where('slug', $pageSlug)->where('status', 'published')->firstOrFail();

        return compact('project', 'version', 'category', 'page');
    }

    public function resolveInitialPage(string $projectSlug, ?string $versionSlug = null): ?array
    {
        /** @var \App\Models\DocProject $project */
        $project = \App\Models\DocProject::where('slug', $projectSlug)->where('is_active', true)->firstOrFail();

        /** @var \App\Models\DocVersion|null $version */
        $version = $versionSlug
            ? $project->versions()->where('slug', $versionSlug)->firstOrFail()
            : $project->versions()->where('is_default', true)->first() ?? $project->versions()->first();

        if (! $version) {
            return null;
        }

        // Find the first category with a published page in this version
        $firstCategory = null;
        $firstPage = null;

        foreach ($version->categories()->orderBy('order')->get() as $category) {
            $page = $category->pages()->where('status', 'published')->orderBy('order')->first();
            if ($page) {
                $firstCategory = $category;
                $firstPage = $page;
                break;
            }
        }

        // If no published page in selected version, try other versions
        if (! $firstPage && ! $versionSlug) {
            foreach ($project->versions as $fallbackVersion) {
                foreach ($fallbackVersion->categories()->orderBy('order')->get() as $category) {
                    $page = $category->pages()->where('status', 'published')->orderBy('order')->first();
                    if ($page) {
                        $version = $fallbackVersion;
                        $firstCategory = $category;
                        $firstPage = $page;
                        break 2;
                    }
                }
            }
        }

        if (! $firstCategory || ! $firstPage) {
            return null;
        }

        return [
            'project' => $project,
            'version' => $version,
            'category' => $firstCategory,
            'page' => $firstPage,
        ];
    }

    public function getSidebar(\App\Models\DocVersion $version)
    {
        return $version->categories()
            ->with(['pages' => fn ($q) => $q->where('status', 'published')->orderBy('order')])
            ->orderBy('order')
            ->get();
    }

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function search(string $projectSlug, string $versionSlug, string $query): array
    {
        /** @var \App\Models\DocProject $project */
        $project = \App\Models\DocProject::where('slug', $projectSlug)->where('is_active', true)->firstOrFail();

        /** @var \App\Models\DocVersion $version */
        $version = $project->versions()->where('slug', $versionSlug)->firstOrFail();

        $categories = $version->categories()
            ->where('name', 'like', "%{$query}%")
            ->orderBy('order')
            ->select(['id', 'name', 'slug', 'doc_version_id'])
            ->limit(5)
            ->get()
            ->map(function ($category) use ($project, $version) {
                // Find first page of category to link to
                $firstPage = $category->pages()->where('status', 'published')->orderBy('order')->first();

                return [
                    'type' => 'category',
                    'title' => $category->name,
                    'url' => route('docs.show', [
                        'projectSlug' => $project->slug,
                        'versionSlug' => $version->slug,
                        'categorySlug' => $category->slug,
                        'pageSlug' => $firstPage ? $firstPage->slug : '', // Handle empty category
                    ]),
                ];
            });

        $pages = \App\Models\DocPage::whereHas('category', function ($q) use ($version) {
            $q->where('doc_version_id', $version->id);
        })
            ->where('title', 'like', "%{$query}%")
            ->where('status', 'published')
            ->orderBy('order')
            ->limit(10)
            ->with('category')
            ->get()
            ->map(function ($page) use ($project, $version) {
                return [
                    'type' => 'page',
                    'title' => $page->title,
                    'category' => $page->category->name,
                    'url' => route('docs.show', [
                        'projectSlug' => $project->slug,
                        'versionSlug' => $version->slug,
                        'categorySlug' => $page->category->slug,
                        'pageSlug' => $page->slug,
                    ]),
                ];
            });

        return [
            'categories' => $categories,
            'pages' => $pages,
        ];
    }
}
