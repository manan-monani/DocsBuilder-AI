<?php

namespace App\Http\Controllers\Admin\Docs;

use App\Http\Controllers\Controller;

class DocPageController extends Controller
{
    public function __construct(protected \App\Services\DocService $docService) {}

    public function index(): \Inertia\Response
    {
        $projectId = request('doc_project_id');
        $versionId = request('doc_version_id');
        $categoryId = request('doc_category_id');
        $search = request('search');

        // Build versions query based on project filter
        $versionsQuery = \App\Models\DocVersion::with('project');
        if ($projectId) {
            $versionsQuery->where('doc_project_id', $projectId);
        }

        // Build categories query based on project/version filter
        $categoriesQuery = \App\Models\DocCategory::with('version.project');
        if ($versionId) {
            $categoriesQuery->where('doc_version_id', $versionId);
        } elseif ($projectId) {
            $categoriesQuery->whereHas('version', fn ($q) => $q->where('doc_project_id', $projectId));
        }

        if ($categoryId) {
            $category = \App\Models\DocCategory::with('version.project')->find($categoryId);
            $pages = $category
                ? $category->pages()
                    ->with('category.version.project')
                    ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
                    ->orderBy('order')
                    ->paginate(10)
                    ->withQueryString()
                : collect();
        } else {
            $category = null;
            $pagesQuery = \App\Models\DocPage::with('category.version.project');

            if ($versionId) {
                $pagesQuery->whereHas('category', fn ($q) => $q->where('doc_version_id', $versionId));
            } elseif ($projectId) {
                $pagesQuery->whereHas('category.version', fn ($q) => $q->where('doc_project_id', $projectId));
            }

            $pages = $pagesQuery
                ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
                ->orderBy('order')
                ->paginate(10)
                ->withQueryString();
        }

        return inertia('Admin/Docs/Pages/Index', [
            'category' => $category,
            'pages' => $pages,
            'allProjects' => \App\Models\DocProject::select('id', 'name')->get(),
            'allVersions' => $versionsQuery->select('id', 'name', 'doc_project_id')->get(),
            'allCategories' => $categoriesQuery->select('id', 'name', 'doc_version_id')->get(),
            'filters' => [
                'search' => $search,
                'doc_project_id' => $projectId,
                'doc_version_id' => $versionId,
                'doc_category_id' => $categoryId,
            ],
        ]);
    }

    public function create(): \Inertia\Response
    {
        return inertia('Admin/Docs/Pages/Create', [
            'categories' => \App\Models\DocCategory::with('version.project')->get(),
            'selected_category_id' => request('doc_category_id'),
        ]);
    }

    public function store(\App\Http\Requests\Admin\Docs\StoreDocPageRequest $request): \Illuminate\Http\RedirectResponse
    {
        $page = $this->docService->createPage($request->validated());

        return to_route('admin.docs.pages.index', ['doc_category_id' => $page->doc_category_id])->with('success', 'Page created successfully.');
    }

    public function edit(\App\Models\DocPage $page): \Inertia\Response
    {
        return inertia('Admin/Docs/Pages/Edit', [
            'page' => $page->load('category.version.project'),
            'categories' => \App\Models\DocCategory::with('version.project')->get(),
        ]);
    }

    public function update(\App\Http\Requests\Admin\Docs\UpdateDocPageRequest $request, \App\Models\DocPage $page): \Illuminate\Http\RedirectResponse
    {
        $this->docService->updatePage($page, $request->validated());

        return to_route('admin.docs.pages.index', ['doc_category_id' => $page->doc_category_id])->with('success', 'Page updated successfully.');
    }

    public function destroy(\App\Models\DocPage $page): \Illuminate\Http\RedirectResponse
    {
        $page->delete();

        return back()->with('success', 'Page deleted successfully.');
    }

    public function revisions(\App\Models\DocPage $page): \Inertia\Response
    {
        return inertia('Admin/Docs/Pages/Revisions', [
            'page' => $page,
            'revisions' => $page->revisions()->with('user')->latest()->paginate(10),
        ]);
    }

    public function restoreRevision(\App\Models\DocRevision $revision): \Illuminate\Http\RedirectResponse
    {
        $page = $revision->page;
        $this->docService->updatePage($page, [
            'content_json' => $revision->content_json,
            'content_html' => $revision->content_html,
            'title' => $page->title, // Keep title or use from revision if stored
            'slug' => $page->slug,
            'status' => $page->status,
            'order' => $page->order,
            'change_summary' => "Restored from revision #{$revision->id}",
        ]);

        return back()->with('success', 'Revision restored successfully.');
    }
}
