<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HandoverController extends Controller
{
    public function index(Request $request): View
    {
        $date = $request->filled('date')
            ? Carbon::parse($request->string('date'))
            : today();

        $activities = Activity::with(['creator', 'updates' => function ($q) use ($date) {
                $q->whereDate('created_at', $date)->latest();
            }, 'updates.updater'])
            ->whereDate('activity_date', $date)
            ->orderBy('status')
            ->get();

        $timeline = ActivityUpdate::with(['activity', 'updater'])
            ->whereDate('created_at', $date)
            ->orderByDesc('created_at')
            ->get();

        $pendingCount = $activities->where('status', 'pending')->count();

        return view('pages.handover.index', [
            'title' => 'Daily Handover',
            'date' => $date,
            'activities' => $activities,
            'timeline' => $timeline,
            'pendingCount' => $pendingCount,
        ]);
    }
}