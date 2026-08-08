@extends('layouts.app')

@section('content')
<div>
    <x-common.page-breadcrumb pageTitle="Reports" />

    <!-- Date range filter -->
    <form method="GET" action="{{ route('reports.index') }}" class="mb-6 flex flex-wrap items-end gap-3">
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From</label>
            <input type="date" name="from" value="{{ $from }}"
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">To</label>
            <input type="date" name="to" value="{{ $to }}"
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
        </div>
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
            <select name="status"
                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                <option value="">All</option>
                <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                <option value="done" @selected(($filters['status'] ?? '') === 'done')>Done</option>
            </select>
        </div>
        <x-ui.button type="submit">Run Report</x-ui.button>
    </form>

    <!-- Summary -->
    <div class="grid grid-cols-1 gap-4 mb-6 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Updates</p>
            <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $summary['total_updates'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Marked Done</p>
            <p class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-500">{{ $summary['marked_done'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Marked Pending</p>
            <p class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-orange-400">{{ $summary['marked_pending'] }}</p>
        </div>
    </div>

    <x-common.component-card title="Activity History" :desc="'From ' . \Carbon\Carbon::parse($from)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($to)->format('d M Y')">

        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Time</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Activity</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Updated By</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Remark</p></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($history as $update)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $update->created_at->format('d M, g:i A') }}
                                </td>
                                <td class="px-5 py-4">
                                    <a href="{{ route('activities.show', $update->activity) }}" class="text-gray-800 text-theme-sm hover:text-brand-500 dark:text-white/90">
                                        {{ $update->activity->title }}
                                    </a>
                                </td>
                                <td class="px-5 py-4">
                                    <x-ui.badge :color="$update->status === 'done' ? 'success' : 'warning'" size="sm">
                                        {{ ucfirst($update->status) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $update->updater->name }}
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $update->remark ?: '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500 text-theme-sm dark:text-gray-400">
                                    No activity history in this range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $history->links() }}
        </div>
    </x-common.component-card>
</div>
@endsection