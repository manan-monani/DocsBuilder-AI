<?php

namespace App\Http\Controllers\Admin\Docs;

use App\Http\Controllers\Controller;

class DocProjectController extends Controller
{
    public function __construct(protected \App\Services\DocService $docService) {}

    public function index(): \Inertia\Response
    {
        // Add permission check if any
        return inertia('Admin/Docs/Projects/Index', [
            'projects' => $this->docService->getAllProjects(request()->only('search')),
        ]);
    }

    public function create(): \Inertia\Response
    {
        return inertia('Admin/Docs/Projects/Create');
    }

    public function store(\App\Http\Requests\Admin\Docs\StoreDocProjectRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->docService->createProject($request->validated());

        return to_route('admin.docs.projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(\App\Models\DocProject $project): \Inertia\Response
    {
        return inertia('Admin/Docs/Projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(\App\Http\Requests\Admin\Docs\UpdateDocProjectRequest $request, \App\Models\DocProject $project): \Illuminate\Http\RedirectResponse
    {
        $this->docService->updateProject($project, $request->validated());

        return to_route('admin.docs.projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(\App\Models\DocProject $project): \Illuminate\Http\RedirectResponse
    {
        $this->docService->deleteProject($project);

        return back()->with('success', 'Project deleted successfully.');
    }
}
