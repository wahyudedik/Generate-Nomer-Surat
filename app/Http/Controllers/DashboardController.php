<?php

namespace App\Http\Controllers;

use App\Models\Letter;
use App\Models\LetterActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('admin');

        $baseQuery = Letter::query();

        if (! $isAdmin) {
            $baseQuery->where('created_by', $user->id);
        }

        $outQuery = (clone $baseQuery)->where('type', 'out');
        $inQuery = (clone $baseQuery)->where('type', 'in');
        $since = now()->subDays(30);

        $stats = [
            'totalOut' => (clone $outQuery)->count(),
            'draftOut' => (clone $outQuery)->where('status', Letter::STATUS_DRAFT)->count(),
            'completedOut' => (clone $outQuery)->where('status', Letter::STATUS_COMPLETE)->count(),
            'totalIn' => (clone $inQuery)->count(),
            'completedIn' => (clone $inQuery)->where('status', Letter::STATUS_COMPLETE)->count(),
            'out30' => (clone $outQuery)->whereDate('created_at', '>=', $since)->count(),
            'in30' => (clone $inQuery)->whereDate('created_at', '>=', $since)->count(),
        ];

        $blockingDraft = Letter::where('type', 'out')
            ->where('status', Letter::STATUS_DRAFT)
            ->with('creator')
            ->latest()
            ->first();

        $drafts = (clone $outQuery)
            ->where('status', Letter::STATUS_DRAFT)
            ->latest()
            ->take(5)
            ->get();

        $logsQuery = LetterActivityLog::with(['letter', 'user'])->latest();
        if (! $isAdmin) {
            $logsQuery->where('user_id', $user->id);
        }
        $recentLogs = $logsQuery->take(10)->get();

        return view('dashboard', compact('stats', 'blockingDraft', 'drafts', 'recentLogs', 'isAdmin'));
    }
}
