<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class DocsController extends Controller
{
    public function __construct(
        protected \App\Services\DocResolutionService $resolutionService,
        protected \App\Services\ContentService $contentService
    ) {}

    public function show(string $projectSlug, ?string $versionSlug = null, ?string $categorySlug = null, ?string $pageSlug = null): \Inertia\Response|\Illuminate\Http\RedirectResponse
    {
        if (! $versionSlug) {
            $resolved = $this->resolutionService->resolveInitialPage($projectSlug);
            if (! $resolved) {
                abort(404, 'Documentation not found.');
            }

            return to_route('docs.show', [
                'projectSlug' => $resolved['project']->slug,
                'versionSlug' => $resolved['version']->slug,
                'categorySlug' => $resolved['category']->slug,
                'pageSlug' => $resolved['page']->slug,
            ]);
        }

        $resolved = $this->resolutionService->resolvePage($projectSlug, $versionSlug, $categorySlug, $pageSlug);

        $sidebar = $this->resolutionService->getSidebar($resolved['version']);

        $processed = $this->contentService->process($resolved['page']->content_html);
        $resolved['page']->content_html = $processed['html'];

        return \Inertia\Inertia::render('Public/Docs/Show', [
            'project' => $resolved['project'],
            'versions' => $resolved['project']->versions,
            'currentVersion' => $resolved['version'],
            'sidebar' => $sidebar,
            'page' => $resolved['page'],
            'toc' => $processed['toc'],
        ]);
    }

    public function search(string $projectSlug, string $versionSlug): \Illuminate\Http\JsonResponse
    {
        $query = request('query');

        if (! $query || strlen($query) < 2) {
            return response()->json(['categories' => [], 'pages' => []]);
        }

        $results = $this->resolutionService->search($projectSlug, $versionSlug, $query);

        return response()->json($results);
    }
}
