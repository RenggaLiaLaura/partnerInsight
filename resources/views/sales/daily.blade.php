@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Sales Performance - Daily View</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aggregated sales data by day.</p>
        </div>
    </div>
</div>

<div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="salesTab" data-tabs-toggle="#salesTabContent" role="tablist">
        <li class="mr-2" role="presentation">
            <a href="{{ route('sales.index') }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ Route::is('sales.index') ? 'border-brand-600 text-brand-600 dark:text-brand-500 dark:border-brand-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}">Distributor List</a>
        </li>
        <li class="mr-2" role="presentation">
            <a href="{{ route('sales.monthly') }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ Route::is('sales.monthly') ? 'border-brand-600 text-brand-600 dark:text-brand-500 dark:border-brand-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}">Monthly View</a>
        </li>
        <li class="mr-2" role="presentation">
            <a href="{{ route('sales.daily') }}" class="inline-block p-4 border-b-2 rounded-t-lg {{ Route::is('sales.daily') ? 'border-brand-600 text-brand-600 dark:text-brand-500 dark:border-brand-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300' }}">Daily View</a>
        </li>
    </ul>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex justify-end">
        <form action="{{ route('sales.daily') }}" method="GET" class="flex items-center">
            <label for="month" class="mr-2 text-sm text-gray-600 dark:text-gray-400">Month:</label>
            <input type="month" name="month" id="month" value="{{ $month }}" onchange="this.form.submit()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-4">Date</th>
                    <th scope="col" class="px-6 py-4">Total Sales (Ctn/Ton)</th>
                    <th scope="col" class="px-6 py-4">Active Distributors</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($dailySales as $sale)
                <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        {{ date('d M Y', strtotime($sale->period)) }} <span class="text-gray-500 text-xs ml-1">({{ $sale->day_name }})</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="bg-brand-100 text-brand-800 text-sm font-semibold px-2.5 py-0.5 rounded dark:bg-brand-900 dark:text-brand-300">
                            {{ number_format($sale->total_amount, 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        {{ $sale->active_distributors }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">No sales data found for this month.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        {{ $dailySales->appends(['month' => $month])->links() }}
    </div>
</div>
@endsection
