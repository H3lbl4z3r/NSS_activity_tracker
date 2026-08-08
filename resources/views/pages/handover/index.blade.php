@extends('layouts.app')

@section('content')
<div>
    <x-common.page-breadcrumb pageTitle="Daily Handover" />

    <!-- Date navigator -->
    <form method="GET" action="{{ route('handover.index') }}" class="mb-6 flex flex-wrap items-center gap-3">
        <a href="{{ route('handover.index', ['date' => $date->copy()->subDay()->toDateString()]) }}"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
            &larr; Prev Day
        </a>

        <input type="date" name="date" value="{{ $date->toDateString() }}" onchange="this.form.submit()"
            class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />

        <a href="{{ route('handover.index', ['date' => $date->copy()->addDay()->toDateString()]) }}"
            class="rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-white/5">
            Next Day &rarr;
        </a>

        <span class="text-sm text-gray-500 dark:text-gray-400">
            Showing {{ $date->format('l, d M Y') }}{{ $date->isToday() ? ' (Today)' : '' }}
        </span>
    </form>

    @if ($pendingCount > 0)
        <div class="mb-6 rounded-lg bg-yellow-50 px-4 py-3 text-sm text-yellow-700 dark:bg-yellow-500/15 dark:text-orange-400">
            <strong>{{ $pendingCount }}</strong> {{ Str::plural('activity', $pendingCount) }} still pending for this day — hand these over carefully.
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">

        <!-- Activities for the day -->
        <x-common.component-card title="Activities" desc="Every activity scheduled for this day, and its state">
            @forelse ($activities as $activity)
                <div class="border-b border-gray-100 pb-4 last:border-b-0 dark:border-gray-800">
                    <div class="flex items-start justify-between gap-3">
                        <a href="{{ route('activities.show', $activity) }}" class="font-medium text-gray-800 text-theme-sm hover:text-brand-500 dark:text-white/90">
                            {{ $activity->title }}
                        </a>
                        <x-ui.badge :color="$activity->status === 'done' ? 'success' : 'warning'" size="sm">
                            {{ ucfirst($activity->status) }}
                        </x-ui.badge>
                    </div>

                    @forelse ($activity->updates as $update)
                        <p class="mt-2 text-theme-xs text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-gray-700 dark:text-gray-300">{{ $update->updater->name }}</span>
                            at {{ $update->created_at->format('g:i A') }}:
                            {{ $update->remark ?: 'no remark left' }}
                        </p>
                    @empty
                        <p class="mt-2 italic text-theme-xs text-gray-400 dark:text-gray-500">
                            Not touched yet today.
                        </p>
                    @endforelse
                </div>
            @empty
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">No activities scheduled for this day.</p>
            @endforelse
        </x-common.component-card>

        <!-- Chronological timeline -->
        <x-common.component-card title="Update Timeline" desc="Everything logged today, in order">
            @forelse ($timeline as $update)
                <div class="flex gap-3 border-b border-gray-100 pb-4 last:border-b-0 dark:border-gray-800">
                    <div class="w-16 flex-shrink-0 text-theme-xs text-gray-400 dark:text-gray-500">
                        {{ $update->created_at->format('g:i A') }}
                    </div>
                    <div class="flex-1">
                        <p class="text-theme-sm text-gray-800 dark:text-white/90">
                            <span class="font-medium">{{ $update->updater->name }}</span>
                            marked
                            <a href="{{ route('activities.show', $update->activity) }}" class="text-brand-500 hover:text-brand-600">
                                {{ $update->activity->title }}
                            </a>
                            as
                            <x-ui.badge :color="$update->status === 'done' ? 'success' : 'warning'" size="sm">
                                {{ ucfirst($update->status) }}
                            </x-ui.badge>
                        </p>
                        @if ($update->remark)
                            <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">{{ $update->remark }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-theme-sm dark:text-gray-400">No updates logged on this day yet.</p>
            @endforelse
        </x-common.component-card>

    </div>
</div>
@endsection