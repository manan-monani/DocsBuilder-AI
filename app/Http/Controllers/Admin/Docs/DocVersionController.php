<?php

namespace App\Http\Controllers\Admin\Docs;

use App\Http\Controllers\Controller;

class DocVersionController extends Controller
{
    public function __construct(protected \App\Services\DocService $docService) {}

    public function index(): \Inertia\Response
    {
        $projectId = request('doc_project_id');
        $search = request('search');

        if ($projectId) {
            $project = \App\Models\DocProject::find($projectId);
            $versions = $project
                ? $project->versions()
                    ->with('project')
                    ->withCount('categories')
                    ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
                    ->latest()
                    ->paginate(10)
                    ->withQueryString()
                : collect();
        } else {
            $project = null;
            $versions = \App\Models\DocVersion::with('project')
                ->withCount('categories')
                ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"))
                ->latest()
                ->paginate(10)
                ->withQueryString();
        }

        return inertia('Admin/Docs/Versions/Index', [
            'project' => $project,
            'versions' => $versions,
            'allProjects' => \App\Models\DocProject::select('id', 'name')->get(),
            'filters' => ['search' => $search, 'doc_project_id' => $projectId],
        ]);
    }

    public function create(): \Inertia\Response
    {
        return inertia('Admin/Docs/Versions/Create', [
            'projects' => \App\Models\DocProject::all(),
            'selected_project_id' => request('doc_project_id'),
        ]);
    }

    public function store(\App\Http\Requests\Admin\Docs\StoreDocVersionRequest $request): \Illuminate\Http\RedirectResponse
    {
        $version = $this->docService->createVersion($request->validated());

        return to_route('admin.docs.versions.index', ['doc_project_id' => $version->doc_project_id])->with('success', 'Version created successfully.');
    }

    public function edit(\App\Models\DocVersion $version): \Inertia\Response
    {
        return inertia('Admin/Docs/Versions/Edit', [
            'version' => $version->load('project'),
            'projects' => \App\Models\DocProject::all(),
        ]);
    }

    public function update(\App\Http\Requests\Admin\Docs\UpdateDocVersionRequest $request, \App\Models\DocVersion $version): \Illuminate\Http\RedirectResponse
    {
        $this->docService->updateVersion($version, $request->validated());

        return to_route('admin.docs.versions.index', ['doc_project_id' => $version->doc_project_id])->with('success', 'Version updated successfully.');
    }

    public function destroy(\App\Models\DocVersion $version): \Illuminate\Http\RedirectResponse
    {
        $version->delete();

        return back()->with('success', 'Version deleted successfully.');
    }
}
