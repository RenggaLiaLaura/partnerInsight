<div id="sales-table-container">
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
                @forelse ($distributors as $distributor)
                    @php
                        $salesByYear = $distributor->salesPerformances->groupBy(function($item) {
                            return \Carbon\Carbon::parse($item->period)->format('Y');
                        });
                    @endphp

                    @foreach ($salesByYear as $year => $sales)
                        <tbody x-data="{ expanded: false }" class="border-b border-gray-100 dark:border-gray-700">
                            <tr class="bg-gray-50 dark:bg-gray-700/50 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors" @click="expanded = !expanded">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 transition-transform duration-200 text-gray-500" :class="{'rotate-90': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        {{ $distributor->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-brand-100 text-brand-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-brand-900 dark:text-brand-300">
                                        Total: {{ number_format($sales->sum('amount'), 0, ',', '.') }} ctn
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold">{{ $year }}</td>
                                <td class="px-6 py-4 text-center text-xs text-gray-500">
                                    <span x-show="!expanded">View {{ $sales->count() }} Months</span>
                                    <span x-show="expanded">Hide Details</span>
                                </td>
                            </tr>
                            
                            @foreach ($sales as $sale)
                            <tr x-show="expanded" class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-700 last:border-b-0" style="display: none;">
                                <td class="px-6 py-3 pl-12 text-sm text-gray-500 dark:text-gray-400">
                                    {{ date('F', strtotime($sale->period)) }}
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-gray-300">
                                        {{ number_format($sale->amount, 0, ',', '.') }} ctn
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-500">
                                    {{ date('d M Y', strtotime($sale->period)) }}
                                </td>
                                <td class="px-6 py-3 text-center">
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
                            @endforeach
                        </tbody>
                    @endforeach
                @empty
                <tbody>
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No records found.</td>
                    </tr>
                </tbody>
                @endforelse
            </tbody>
        </table>
    </div>
    <!-- Pagination -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
        {{ $distributors->links() }}
    </div>
</div>
