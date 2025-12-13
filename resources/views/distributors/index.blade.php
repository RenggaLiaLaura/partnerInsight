@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Distributor Data</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage your distributor partners and their information.</p>
        </div>
        <div class="flex gap-3">
            @if(Auth::user()->role === 'admin')
            <a href="{{ route('distributors.create') }}" class="inline-flex items-center p-2 md:px-4 md:py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 focus:ring-4 focus:ring-brand-300 dark:bg-brand-600 dark:hover:bg-brand-700 focus:outline-none dark:focus:ring-brand-800 shadow-sm transition-colors duration-200">
                <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="hidden md:inline">Add New</span>
            </a>
            @endif

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="inline-flex items-center p-2 md:px-4 md:py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                    <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    <span class="hidden md:inline">Import/Export</span>
                    <svg class="w-4 h-4 ml-2 hidden md:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5">
                    <div class="py-1">
                        <a href="{{ route('distributors.import') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            Import Distributors
                        </a>
                        <!-- <a href="{{ route('distributors.template') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Download Template
                        </a> -->
                        <hr class="my-1 border-gray-200 dark:border-gray-600">
                        <a href="{{ route('export.all') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-600">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                            Export All Data
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        <form action="{{ route('distributors.index') }}" method="GET" class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" class="block p-2 pl-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full md:w-80 bg-white focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="Search by name, region, address, or phone">
        </form>
    </div>
    <div id="distributors-table">
        @include('distributors.partials.table')
    </div>
</div>

<script>
    let searchTimeout;
    const searchInput = document.querySelector('input[name="search"]');
    const tableContainer = document.getElementById('distributors-table');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const query = this.value;
            const url = new URL("{{ route('distributors.index') }}");
            url.searchParams.set('search', query);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                tableContainer.innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
        }, 300); // 300ms debounce
    });
</script>
@endsection
