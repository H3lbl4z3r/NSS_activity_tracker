@extends('layouts.app')

@section('content')
<div>
    <x-common.page-breadcrumb pageTitle="Activity Details" />

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-500/15 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('activities.index') }}" class="text-sm text-brand-500 hover:text-brand-600">
            &larr; Back to Activities
        </a>
    </div>

    <x-common.component-card :title="$activity->title" :desc="$activity->description">

        <div class="flex flex-wrap items-center gap-6 -mt-2 mb-4 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400">Status: </span>
                <x-ui.badge :color="$activity->status === 'done' ? 'success' : 'warning'">
                    {{ ucfirst($activity->status) }}
                </x-ui.badge>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Date: </span>
                <span class="text-gray-800 dark:text-white/90">{{ $activity->activity_date->format('d M Y') }}</span>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400">Created by: </span>
                <span class="text-gray-800 dark:text-white/90">{{ $activity->creator->name }}</span>
                <span class="text-gray-400 dark:text-gray-500">({{ $activity->creator->role }})</span>
            </div>
        </div>

        <h4 class="mb-3 text-base font-medium text-gray-800 dark:text-white/90">Update History</h4>

        @forelse ($activity->updates as $update)
            <div class="flex gap-4 border-b border-gray-100 py-4 last:border-b-0 dark:border-gray-800">
                <div class="flex-shrink-0">
                    <x-ui.badge :color="$update->status === 'done' ? 'success' : 'warning'" size="sm">
                        {{ ucfirst($update->status) }}
                    </x-ui.badge>
                </div>
                <div class="flex-1">
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                            {{ $update->updater->name }}
                            <span class="font-normal text-gray-400 dark:text-gray-500">({{ $update->updater->role }})</span>
                        </p>
                        <p class="text-theme-xs text-gray-400 dark:text-gray-500">
                            {{ $update->created_at->format('d M Y, g:i A') }}
                        </p>
                    </div>
                    @if ($update->remark)
                        <p class="mt-1 text-gray-600 text-theme-sm dark:text-gray-300">{{ $update->remark }}</p>
                    @else
                        <p class="mt-1 italic text-gray-400 text-theme-sm dark:text-gray-500">No remark left.</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                No updates yet — this activity is still pending its first status update.
            </p>
        @endforelse

    </x-common.component-card>
</div>
@endsection