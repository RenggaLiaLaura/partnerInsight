@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Logs</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track all changes made to your data.</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-4 mb-6">
    <form method="GET" action="{{ route('audit-logs.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
            <select name="user_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Users</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Action</label>
            <select name="action" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Actions</option>
                <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>Created</option>
                <option value="updated" {{ request('action') == 'updated' ? 'selected' : '' }}>Updated</option>
                <option value="deleted" {{ request('action') == 'deleted' ? 'selected' : '' }}>Deleted</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">From Date</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">To Date</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        <div class="flex items-end">
            <button type="submit" class="w-full text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-brand-600 dark:hover:bg-brand-700">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Audit Logs Table -->
<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-4">Date/Time</th>
                    <th scope="col" class="px-6 py-4">User</th>
                    <th scope="col" class="px-6 py-4">Action</th>
                    <th scope="col" class="px-6 py-4">Model</th>
                    <th scope="col" class="px-6 py-4">Record ID</th>
                    <th scope="col" class="px-6 py-4">IP Address</th>
                    <th scope="col" class="px-6 py-4 text-center">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $log->user ? $log->user->name : 'System' }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            {{ $log->action == 'created' ? 'bg-green-100 text-green-800' : 
                               ($log->action == 'updated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($log->action) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        {{ class_basename($log->auditable_type) }}
                    </td>
                    <td class="px-6 py-4">
                        #{{ $log->auditable_id }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $log->ip_address }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('audit-logs.show', $log->id) }}" class="text-brand-600 hover:text-brand-700 dark:text-brand-400">
                            View Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No audit logs found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
