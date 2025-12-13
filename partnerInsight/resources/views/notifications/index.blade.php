@extends('layouts.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notifications</h1>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">All Notifications</h3>
        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300 font-medium">
                Mark all as read
            </button>
        </form>
    </div>
    
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @forelse($notifications as $notification)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out {{ $notification->read_at ? 'opacity-75' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $notification->title }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notification->read_at)
                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-brand-600 hover:text-brand-800 dark:text-brand-400 dark:hover:text-brand-300" title="Mark as read">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                <p>No notifications found.</p>
            </div>
        @endforelse
    </div>
    
    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
        {{ $notifications->links() }}
    </div>
</div>
@endsection
