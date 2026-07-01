<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\DocCategory;
use App\Models\DocPage;
use App\Models\DocProject;
use App\Models\DocVersion;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Get real stats
        $stats = [
            'users' => User::count(),
            'projects' => DocProject::count(),
            'versions' => DocVersion::count(),
            'categories' => DocCategory::count(),
            'pages' => DocPage::count(),
            'published_pages' => DocPage::where('status', 'published')->count(),
        ];

        // Get recent activity
        $recentActivity = ActivityLog::with('user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'user' => $activity->user?->name ?? 'System',
                    'description' => $activity->description,
                    'event' => $activity->event,
                    'subject_type' => class_basename($activity->subject_type ?? ''),
                    'subject_id' => $activity->subject_id,
                    'created_at' => $activity->created_at->diffForHumans(),
                ];
            });

        return Inertia::render('Admin/Pages/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }
}
