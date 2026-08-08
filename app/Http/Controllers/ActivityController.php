<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $query = Activity::with(['creator', 'updates.updater'])
            ->orderByDesc('activity_date')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->string('search').'%');
        }

        $activities = $query->paginate(10)->withQueryString();

        $stats = [
            'total' => Activity::count(),
            'pending' => Activity::where('status', 'pending')->count(),
            'done' => Activity::where('status', 'done')->count(),
            'today' => Activity::whereDate('activity_date', today())->count(),
        ];

        return view('pages.activities.index', [
            'title' => 'Activities',
            'activities' => $activities,
            'stats' => $stats,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'activity_date' => ['required', 'date'],
        ]);

        $data['created_by'] = $request->user()->id;
        $data['status'] = 'pending';

        Activity::create($data);

        return redirect()->route('activities.index')->with('success', 'Activity added.');
    }

    public function show(Activity $activity): View
    {
        $activity->load(['creator', 'updates.updater']);

        return view('pages.activities.show', [
            'title' => 'Activity Details',
            'activity' => $activity,
        ]);
    }
}