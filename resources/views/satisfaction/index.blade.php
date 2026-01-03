@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Satisfaction Scores</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track and manage distributor satisfaction levels.</p>
        </div>
        @if(Auth::user()->role !== 'manager')
        <div class="flex space-x-2">
            <a href="#" onclick="generateReportWithFilter(event)" class="inline-flex items-center p-2 md:px-4 md:py-2 text-sm font-medium text-brand-600 bg-white border border-brand-600 rounded-lg hover:bg-brand-50 focus:ring-4 focus:ring-brand-300 dark:bg-gray-800 dark:text-brand-400 dark:border-brand-400 dark:hover:bg-gray-700 focus:outline-none dark:focus:ring-brand-800 shadow-sm transition-colors duration-200">
                <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span class="hidden md:inline">View Report</span>
            </a>
            <a href="{{ route('satisfaction.create') }}" class="inline-flex items-center p-2 md:px-4 md:py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 focus:ring-4 focus:ring-brand-300 dark:bg-brand-600 dark:hover:bg-brand-700 focus:outline-none dark:focus:ring-brand-800 shadow-sm transition-colors duration-200">
                <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span class="hidden md:inline">Add New Score</span>
            </a>
        </div>
        @endif
    </div>
</div>



<!-- Charts Section -->
@if(isset($productStats) && count($productStats) > 0)
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Product Quality Chart -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 uppercase">Kualitas Produk</h3>
        <div class="relative h-64">
            <canvas id="qualityChart"></canvas>
        </div>
        <p class="text-xs text-center mt-2 text-gray-500 italic">Rata-rata: {{ $summary['prod_avg'] }}</p>
    </div>

    <!-- Service Quality Chart -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
        <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4 uppercase">Pelayanan</h3>
        <div class="relative h-64">
            <canvas id="serviceChart"></canvas>
        </div>
        <p class="text-xs text-center mt-2 text-gray-500 italic">Rata-rata: {{ $summary['serv_avg'] }}</p>
    </div>
</div>

<!-- Summary Text -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl p-6">
        <h3 class="flex items-center text-sm font-bold text-blue-900 dark:text-blue-100 mb-4 uppercase">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Highlight Kualitas Produk
        </h3>
        <div class="space-y-4">
            <div>
                <p id="sum-prod-max-val" class="text-xs text-blue-600 dark:text-blue-300 uppercase font-semibold">Tertinggi ({{ $summary['prod_max_val'] }})</p>
                <p id="sum-prod-max-items" class="text-sm text-gray-700 dark:text-gray-300">{{ $summary['prod_max_items'] }}</p>
            </div>
            <div>
                <p class="text-xs text-blue-600 dark:text-blue-300 uppercase font-semibold">Rata-rata</p>
                <p id="sum-prod-avg" class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['prod_avg'] }}</p>
            </div>
        </div>
    </div>

    <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800 rounded-xl p-6">
        <h3 class="flex items-center text-sm font-bold text-purple-900 dark:text-purple-100 mb-4 uppercase">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Highlight Pelayanan
        </h3>
        <div class="space-y-4">
            <div>
                <p id="sum-serv-max-val" class="text-xs text-purple-600 dark:text-purple-300 uppercase font-semibold">Tertinggi ({{ $summary['serv_max_val'] }})</p>
                <p id="sum-serv-max-items" class="text-sm text-gray-700 dark:text-gray-300">{{ $summary['serv_max_items'] }}</p>
            </div>
            <div>
                <p class="text-xs text-purple-600 dark:text-purple-300 uppercase font-semibold">Rata-rata</p>
                <p id="sum-serv-avg" class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['serv_avg'] }}</p>
            </div>
            
            <div id="sum-serv-min-container" class="pt-2 border-t border-purple-200 dark:border-purple-700" style="{{ $summary['serv_min_val'] < 4 ? '' : 'display:none' }}">
                <p id="sum-serv-min-val" class="text-xs text-red-600 dark:text-red-300 uppercase font-semibold">Perlu Perhatian ({{ $summary['serv_min_val'] }})</p>
                <p id="sum-serv-min-items" class="text-sm text-gray-700 dark:text-gray-300">{{ $summary['serv_min_items'] }}</p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productData = @json($productStats);
        const serviceData = @json($serviceStats);

        const commonOptions = {
            indexAxis: 'x', // Vertical bars as per image
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) { return context.raw; }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5.0,
                    ticks: { stepSize: 1 },
                    grid: { color: '#f3f4f6' }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10 },
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        };

        // Expose charts to window for update function
        window.qualityChart = new Chart(document.getElementById('qualityChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(productData),
                datasets: [{
                    data: Object.values(productData),
                    backgroundColor: '#1C64F2', // Brand blue
                    borderRadius: 4,
                    barThickness: 30
                }]
            },
            options: commonOptions
        });

        // Service Chart
        window.serviceChart = new Chart(document.getElementById('serviceChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(serviceData),
                datasets: [{
                    data: Object.values(serviceData),
                    backgroundColor: '#1C64F2',
                    borderRadius: 4,
                    barThickness: 30
                }]
            },
            options: commonOptions
        });
    });

    function updateChart(chart, newData) {
        chart.data.labels = Object.keys(newData);
        chart.data.datasets[0].data = Object.values(newData);
        chart.update();
    }
    
    function updateSummary(summary) {
        // Helper to update text content safely
        const setText = (id, text) => {
            const el = document.getElementById(id);
            if (el) el.textContent = text;
        };

        // Update Product Highlights
        setText('sum-prod-max-val', `Tertinggi (${summary.prod_max_val})`);
        setText('sum-prod-max-items', summary.prod_max_items);
        setText('sum-prod-avg', summary.prod_avg);
        
        // Update Service Highlights
        setText('sum-serv-max-val', `Tertinggi (${summary.serv_max_val})`);
        setText('sum-serv-max-items', summary.serv_max_items);
        setText('sum-serv-avg', summary.serv_avg);
        
        // Update "Need Attention" if exists
        const minSection = document.getElementById('sum-serv-min-container');
        if (summary.serv_min_val < 4) {
            if (minSection) {
                minSection.style.display = 'block';
                setText('sum-serv-min-val', `Perlu Perhatian (${summary.serv_min_val})`);
                setText('sum-serv-min-items', summary.serv_min_items);
            }
        } else {
             if (minSection) minSection.style.display = 'none';
        }
    }

    function generateReportWithFilter(e) {
        e.preventDefault();
        const searchInput = document.querySelector('input[placeholder="Search by distributor..."]');
        const searchValue = searchInput ? searchInput.value : '';
        const monthSelect = document.querySelector('select[x-model="selectedMonth"]'); // Targeting via attribute if possible, or just ID
        // Alternatively, since we are inside Alpine scope mostly, but this function is global.
        // Let's try to get value from the element directly.
        const monthValue = document.getElementById('monthFilter').value;
        const yearValue = document.getElementById('yearFilter').value;
        
        let url = "{{ route('satisfaction.report') }}";
        const params = [];
        if (searchValue) params.push(`search=${encodeURIComponent(searchValue)}`);
        if (monthValue) params.push(`month=${encodeURIComponent(monthValue)}`);
        if (yearValue) params.push(`year=${encodeURIComponent(yearValue)}`);
        
        if (params.length > 0) {
            url += '?' + params.join('&');
        }
        
        window.open(url, '_blank');
    }
</script>
@endif

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden" x-data="{
    searchQuery: '',
    selectedMonth: '',
    selectedYear: '{{ $year }}',
    isLoading: false,
    debounce: null,
    fetchData() {
        this.isLoading = true;
        clearTimeout(this.debounce);
        this.debounce = setTimeout(() => {
            let url = '{{ route('satisfaction.index') }}?search=' + this.searchQuery;
            if (this.selectedMonth) {
                url += '&month=' + this.selectedMonth;
            }
            if (this.selectedYear) {
                url += '&year=' + this.selectedYear;
            }
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update Table
                if (document.getElementById('satisfaction-table-container')) {
                    document.getElementById('satisfaction-table-container').outerHTML = data.html;
                }
                
                // Update Charts if they exist
                if (window.qualityChart && window.serviceChart) {
                    updateChart(window.qualityChart, data.productStats);
                    updateChart(window.serviceChart, data.serviceStats);
                    
                    // Update Summary Texts
                    updateSummary(data.summary);
                }
                
                this.isLoading = false;
            })
            .catch(err => {
                console.error('Search failed', err);
                this.isLoading = false;
            });
        }, 300);
    },
    init() {
        this.$watch('searchQuery', () => this.fetchData());
        this.$watch('selectedMonth', () => this.fetchData());
        this.$watch('selectedYear', () => this.fetchData());
    }
}">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 flex flex-col md:flex-row gap-4">
        <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" x-model="searchQuery" class="block p-2 pl-3 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-white focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" placeholder="Search by distributor...">
        </div>
        
        <div class="relative w-full md:w-48">
             <select id="yearFilter" x-model="selectedYear" class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500">
                @foreach($years as $y)
                    <option value="{{ $y }}">Year: {{ $y }}</option>
                @endforeach
            </select>
        </div>

        <div class="relative w-full md:w-48">
             <select id="monthFilter" x-model="selectedMonth" class="block w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-brand-500 focus:border-brand-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500">
                <option value="">All Months</option>
                @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                @endfor
            </select>
        </div>

        <div x-show="isLoading" class="flex items-center">
             <svg class="animate-spin h-5 w-5 text-brand-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
    
    @include('satisfaction.partials.table')
</div>
@endsection
