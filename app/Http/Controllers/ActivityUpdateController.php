<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityUpdateController extends Controller
{
    public function store(Request $request, Activity $activity): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,done'],
            'remark' => ['nullable', 'string'],
        ]);

        // Log this update with who did it and when (captured automatically via created_at)
        $activity->updates()->create([
            'status' => $data['status'],
            'remark' => $data['remark'] ?? null,
            'updated_by' => $request->user()->id,
        ]);

        // Keep the activity's current status in sync with the latest update
        $activity->update(['status' => $data['status']]);

        return back()->with('success', 'Activity updated.');
    }
}