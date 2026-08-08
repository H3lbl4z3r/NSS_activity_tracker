<?php

namespace App\Http\Controllers;

use App\Models\ActivityUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $from = $request->filled('from')
            ? Carbon::parse($request->string('from'))->startOfDay()
            : today()->subDays(6)->startOfDay();

        $to = $request->filled('to')
            ? Carbon::parse($request->string('to'))->endOfDay()
            : today()->endOfDay();

        $query = ActivityUpdate::with(['activity', 'updater'])
            ->whereBetween('created_at', [$from, $to]);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $history = $query->orderByDesc('created_at')->paginate(15)->withQueryString();

        $summary = [
            'total_updates' => (clone $query)->count(),
            'marked_done' => (clone $query)->where('status', 'done')->count(),
            'marked_pending' => (clone $query)->where('status', 'pending')->count(),
        ];

        return view('pages.reports.index', [
            'title' => 'Reports',
            'history' => $history,
            'summary' => $summary,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'filters' => $request->only(['status']),
        ]);
    }
}