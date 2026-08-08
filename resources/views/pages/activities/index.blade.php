@extends('layouts.app')

@section('content')
<div x-data="{ showAddModal: false, showUpdateModal: false, target: { id: null, title: '', status: 'pending' } }">

    <x-common.page-breadcrumb pageTitle="Activities" />

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700 dark:bg-green-500/15 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stat cards -->
    <div class="grid grid-cols-2 gap-4 mb-6 sm:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total</p>
            <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Today</p>
            <p class="mt-1 text-2xl font-semibold text-gray-800 dark:text-white/90">{{ $stats['today'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
            <p class="mt-1 text-2xl font-semibold text-yellow-600 dark:text-orange-400">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
            <p class="text-sm text-gray-500 dark:text-gray-400">Done</p>
            <p class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-500">{{ $stats['done'] }}</p>
        </div>
    </div>

    <x-common.component-card title="All Activities" desc="Everything the support team is tracking">

        <div class="flex flex-wrap items-center justify-between gap-3 -mt-2 mb-2">
            <form method="GET" action="{{ route('activities.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search title..."
                    class="h-10 w-48 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />

                <select name="status" onchange="this.form.submit()"
                    class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="">All statuses</option>
                    <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Pending</option>
                    <option value="done" @selected(($filters['status'] ?? '') === 'done')>Done</option>
                </select>

                <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-white/5 dark:text-white/90 dark:hover:bg-white/10">
                    Filter
                </button>
            </form>

            <x-ui.button @click="showAddModal = true">
                + Add Activity
            </x-ui.button>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-800">
            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[900px]">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Activity</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Date</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Created By</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p></th>
                            <th class="px-5 py-3 text-left"><p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Actions</p></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activities as $activity)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4">
                                    <a href="{{ route('activities.show', $activity) }}" class="font-medium text-gray-800 text-theme-sm hover:text-brand-500 dark:text-white/90">
                                        {{ $activity->title }}
                                    </a>
                                    @if ($activity->description)
                                        <p class="mt-0.5 line-clamp-1 text-theme-xs text-gray-500 dark:text-gray-400">{{ $activity->description }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $activity->activity_date->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-gray-500 text-theme-sm dark:text-gray-400">
                                    {{ $activity->creator->name }}
                                </td>
                                <td class="px-5 py-4">
                                    <x-ui.badge :color="$activity->status === 'done' ? 'success' : 'warning'">
                                        {{ ucfirst($activity->status) }}
                                    </x-ui.badge>
                                </td>
                                <td class="px-5 py-4">
                                    <button type="button"
                                        @click="showUpdateModal = true; target = { id: {{ $activity->id }}, title: @js($activity->title), status: @js($activity->status) }"
                                        class="text-sm font-medium text-brand-500 hover:text-brand-600">
                                        Update
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500 text-theme-sm dark:text-gray-400">
                                    No activities yet. Add the first one above.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            {{ $activities->links() }}
        </div>
    </x-common.component-card>

    <!-- Add Activity Modal -->
    <x-ui.modal x-model="showAddModal" x-bind:open="showAddModal" class="max-w-[500px] p-6">
        <h4 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90">Add Activity</h4>
        <form method="POST" action="{{ route('activities.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Title</label>
                <input type="text" name="title" required placeholder='e.g. "Daily SMS count vs logs"'
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description (optional)</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Date</label>
                <input type="date" name="activity_date" required value="{{ now()->toDateString() }}"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="showAddModal = false"
                    class="rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5">Cancel</button>
                <x-ui.button type="submit">Save Activity</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    <!-- Update Status Modal -->
    <x-ui.modal x-bind:open="showUpdateModal" class="max-w-[500px] p-6">
        <h4 class="mb-1 text-lg font-semibold text-gray-800 dark:text-white/90" x-text="target.title"></h4>
        <p class="mb-5 text-sm text-gray-500 dark:text-gray-400">Update the status and leave a remark for the next person.</p>
        <form method="POST" :action="`/activities/${target.id}/updates`" class="space-y-4">
            @csrf
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
                <select name="status" x-model="target.status"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    <option value="pending">Pending</option>
                    <option value="done">Done</option>
                </select>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Remark</label>
                <textarea name="remark" rows="3" placeholder="e.g. Logs delayed by 20 mins, still checking..."
                    class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="showUpdateModal = false"
                    class="rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5">Cancel</button>
                <x-ui.button type="submit">Save Update</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

</div>
@endsection