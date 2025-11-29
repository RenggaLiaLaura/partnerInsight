@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Audit Log Details</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">View detailed information about this change.</p>
        </div>
        <!-- <a href="{{ route('audit-logs.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:border-gray-600">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Back to List
        </a> -->
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Log Information -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Log Information</h3>
        
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Date & Time</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $log->created_at->format('Y-m-d H:i:s') }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $log->user ? $log->user->name : 'System' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Action</dt>
                <dd class="mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $log->action == 'created' ? 'bg-green-100 text-green-800' : 
                           ($log->action == 'updated' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($log->action) }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Model Type</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ class_basename($log->auditable_type) }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Record ID</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">#{{ $log->auditable_id }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IP Address</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $log->ip_address }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">User Agent</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white break-all">{{ $log->user_agent }}</dd>
            </div>
        </dl>
    </div>

    <!-- Changes -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Changes</h3>
        
        @if($log->action === 'created')
            <div class="space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">New record created with the following values:</p>
                @foreach($log->new_values as $key => $value)
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-1/3">{{ ucfirst($key) }}:</span>
                        <span class="text-sm text-green-600 dark:text-green-400 w-2/3">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        @elseif($log->action === 'deleted')
            <div class="space-y-2">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Record deleted with the following values:</p>
                @foreach($log->old_values as $key => $value)
                    <div class="flex items-start">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-1/3">{{ ucfirst($key) }}:</span>
                        <span class="text-sm text-red-600 dark:text-red-400 w-2/3 line-through">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        @elseif($log->action === 'updated')
            @if(count($differences) > 0)
                <div class="space-y-3">
                    @foreach($differences as $field => $change)
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ ucfirst($field) }}</div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-red-600 dark:text-red-400 line-through">{{ $change['old'] ?? 'null' }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                <span class="text-sm text-green-600 dark:text-green-400">{{ $change['new'] ?? 'null' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400">No changes detected.</p>
            @endif
        @endif
    </div>
</div>
@endsection
