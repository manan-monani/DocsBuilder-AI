<?php

namespace App\Http\Controllers\Admin\Docs;

use App\Http\Controllers\Controller;

class DocCategoryController extends Controller
{
    public function __construct(protected \App\Services\DocService $docService) {}

    public function index(): \Inertia\Response
    {
        $projectId = request('doc_project_id');
        $versionId = request('doc_version_id');
        $search = request('search');

        $versionsQuery = \App\Models\DocVersion::with('project');
        if ($projectId) {
            $versionsQuery->where('doc_project_id', $projectId);
        }

        if ($versionId) {
            $version = \App\Models\DocVersion::with('project')->find($versionId);
            $categories = $version
                ? $version->categories()
                    ->withCount('pages')
                    ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
                    ->orderBy('order')
                    ->paginate(10)
                    ->withQueryString()
                : collect();
        } else {
            $version = null;
            $categoriesQuery = \App\Models\DocCategory::with('version.project')->withCount('pages');

            if ($projectId) {
                $categoriesQuery->whereHas('version', fn ($q) => $q->where('doc_project_id', $projectId));
            }

            $categories = $categoriesQuery
                ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
                ->orderBy('order')
                ->paginate(10)
                ->withQueryString();
        }

        return inertia('Admin/Docs/Categories/Index', [
            'version' => $version,
            'categories' => $categories,
            'allProjects' => \App\Models\DocProject::select('id', 'name')->get(),
            'allVersions' => $versionsQuery->select('id', 'name', 'doc_project_id')->get(),
            'filters' => ['search' => $search, 'doc_project_id' => $projectId, 'doc_version_id' => $versionId],
        ]);
    }

    public function create(): \Inertia\Response
    {
        return inertia('Admin/Docs/Categories/Create', [
            'versions' => \App\Models\DocVersion::with('project')->get(),
            'selected_version_id' => request('doc_version_id'),
        ]);
    }

    public function store(\App\Http\Requests\Admin\Docs\StoreDocCategoryRequest $request): \Illuminate\Http\RedirectResponse
    {
        $category = $this->docService->createCategory($request->validated());

        return to_route('admin.docs.categories.index', ['doc_version_id' => $category->doc_version_id])->with('success', 'Category created successfully.');
    }

    public function edit(\App\Models\DocCategory $category): \Inertia\Response
    {
        return inertia('Admin/Docs/Categories/Edit', [
            'category' => $category->load('version.project'),
            'versions' => \App\Models\DocVersion::with('project')->get(),
        ]);
    }

    public function update(\App\Http\Requests\Admin\Docs\UpdateDocCategoryRequest $request, \App\Models\DocCategory $category): \Illuminate\Http\RedirectResponse
    {
        $this->docService->updateCategory($category, $request->validated());

        return to_route('admin.docs.categories.index', ['doc_version_id' => $category->doc_version_id])->with('success', 'Category updated successfully.');
    }

    public function destroy(\App\Models\DocCategory $category): \Illuminate\Http\RedirectResponse
    {
        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
