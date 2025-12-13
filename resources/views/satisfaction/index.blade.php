@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Satisfaction Scores</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track and manage distributor satisfaction levels.</p>
        </div>
        <a href="{{ route('satisfaction.create') }}" class="inline-flex items-center p-2 md:px-4 md:py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 focus:ring-4 focus:ring-brand-300 dark:bg-brand-600 dark:hover:bg-brand-700 focus:outline-none dark:focus:ring-brand-800 shadow-sm transition-colors duration-200">
            <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span class="hidden md:inline">Add New Score</span>
        </a>
    </div>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden" x-data="{
    searchQuery: '',
    isLoading: false,
    debounce: null,
    init() {
        this.$watch('searchQuery', (value) => {
            this.isLoading = true;
            clearTimeout(this.debounce);
            this.debounce = setTimeout(() => {
                fetch('{{ route('satisfaction.index') }}?search=' + value, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.text())
                .then(html => {
                    document.getElementById('satisfaction-table-container').outerHTML = html;
                    this.isLoading = false;
                })
                .catch(() => {
                    this.isLoading = false;
                });
            }, 300);
        });
    }
}">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" x-model="searchQuery" class="block p-2 pl-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-white focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="Search by distributor...">
            <div x-show="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
                <svg class="animate-spin h-5 w-5 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    @include('satisfaction.partials.table')
</div>
@endsection
