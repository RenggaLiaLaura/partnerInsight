@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Performance</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Monitor sales data and performance metrics.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 focus:ring-4 focus:ring-brand-300 dark:bg-brand-600 dark:hover:bg-brand-700 focus:outline-none dark:focus:ring-brand-800 shadow-sm transition-colors duration-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Sales Data
        </a>
    </div>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden" x-data="{
    searchQuery: '',
    filterRow(distributor, amount, period) {
        if (!this.searchQuery) return true;
        const query = this.searchQuery.toLowerCase();
        return distributor.toLowerCase().includes(query) ||
               amount.toString().includes(query) ||
               period.toLowerCase().includes(query);
    }
}">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" x-model="searchQuery" class="block p-2 pl-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-white focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="Search by distributor, amount, or period">
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-4">Distributor</th>
                    <th scope="col" class="px-6 py-4">Total Sales</th>
                    <th scope="col" class="px-6 py-4">Period</th>
                    <th scope="col" class="px-6 py-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($sales as $sale)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150"
                    x-show="filterRow('{{ $sale->distributor->name }}', '{{ $sale->amount }}', '{{ date('F Y', strtotime($sale->period)) }}')">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $sale->distributor->name }}
                    </th>
                    <td class="px-6 py-4">{{ number_format($sale->amount, 0, ',', '.') }} ctn</td>
                    <td class="px-6 py-4">{{ date('F Y', strtotime($sale->period)) }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('sales.edit', $sale->id) }}" class="inline-flex items-center justify-center w-7 h-7 text-amber-700 transition-colors duration-150 bg-amber-100 rounded-lg hover:bg-amber-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 dark:bg-amber-900 dark:text-amber-300 dark:hover:bg-amber-800" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form action="{{ route('sales.destroy', $sale->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center justify-center w-7 h-7 text-rose-700 transition-colors duration-150 bg-rose-100 rounded-lg hover:bg-rose-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-rose-500 dark:bg-rose-900 dark:text-rose-300 dark:hover:bg-rose-800" onclick="return confirm('Are you sure?')" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        {{ $sales->links() }}
    </div>
</div>
@endsection
