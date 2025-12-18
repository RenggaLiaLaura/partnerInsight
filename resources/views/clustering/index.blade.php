@extends('layouts.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col md:flex-row items-center justify-between">
        <div class="mb-4 md:mb-0">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Clustering Analysis</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure parameters, run analysis, and view segmentation results.</p>
        </div>
        <div class="flex space-x-3">
            @if($results->count() > 0)
            <a href="{{ route('clustering.export') }}" class="inline-flex items-center p-2 md:px-4 md:py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-brand-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-5 h-5 md:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                <span class="hidden md:inline">Export Results</span>
            </a>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 gap-6 mb-6">
    <!-- Configuration & Actions -->
    @if(Auth::user()->role === 'admin')
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6 relative overflow-hidden" x-data="{ loading: false }">
        
        <!-- Loading Overlay -->
        <div x-show="loading" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 z-10 flex flex-col items-center justify-center backdrop-blur-sm">
            <svg class="animate-spin h-10 w-10 text-brand-600 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-brand-600 font-medium animate-pulse">Running Analysis...</span>
        </div>

        <form action="{{ route('clustering.run') }}" method="POST" @submit="loading = true" class="flex flex-col md:flex-row md:items-end gap-4">
            @csrf
            
            @if ($errors->any())
                <div class="w-full mb-4 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex-1">
                <label for="clusters" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Number of Clusters (K)</label>
                <input type="number" id="clusters" name="clusters" min="5" max="100" value="{{ session('clustering_k', 5) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required>
            </div>
            <div class="flex-1">
                <label for="max_iter" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Max Iterations</label>
                <input type="number" id="max_iter" name="max_iter" min="5" max="100" value="{{ session('clustering_max_iter', 100) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-brand-500 focus:border-brand-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-brand-500 dark:focus:border-brand-500" required>
            </div>
            <div class="flex-none">
                <button type="submit" class="text-white bg-brand-600 hover:bg-brand-700 focus:ring-4 focus:outline-none focus:ring-brand-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-brand-600 dark:hover:bg-brand-700 dark:focus:ring-brand-800 shadow-lg shadow-brand-500/30 transition-all duration-200">
                    Run Analysis
                </button>
            </div>
        </form>
    </div>
    @endif

    <!-- Charts Area -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-4">
            <h4 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">Satisfaction vs Sales Analysis</h4>
            <div class="relative h-64">
                <canvas id="scatterChart"></canvas>
            </div>
        </div>
        <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-4">
            <h4 class="mb-2 text-sm font-semibold text-gray-500 dark:text-gray-400">Cluster Distribution</h4>
            <div class="relative h-64">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Results Table -->
<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/50">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Analysis Results</h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $results->count() }} Distributors Processed</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Distributor</th>
                    <th scope="col" class="px-6 py-3">Satisfaction</th>
                    <th scope="col" class="px-6 py-3">Quantity (Carton)</th>
                    <th scope="col" class="px-6 py-3">Cluster</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($results as $result)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ $result->distributor->name }}
                    </th>
                    <td class="px-6 py-4">{{ number_format($result->score_satisfaction, 0) }}</td>
                    <td class="px-6 py-4">{{ number_format($result->score_sales, 0, ',', '.') }}</td>
                    <td class="px-6 py-4">
                        @php
                            $badgeClass = 'bg-gray-100 text-gray-800'; // Default
                            if ($result->cluster_group == 'Loyal') $badgeClass = 'bg-green-100 text-green-800';
                            elseif ($result->cluster_group == 'Potensial') $badgeClass = 'bg-yellow-100 text-yellow-800';
                            elseif ($result->cluster_group == 'Berisiko') $badgeClass = 'bg-red-100 text-red-800';
                            elseif ($result->cluster_group == 'Cluster 1') $badgeClass = 'bg-blue-100 text-blue-800';
                            elseif ($result->cluster_group == 'Cluster 2') $badgeClass = 'bg-red-100 text-red-800';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                            {{ $result->cluster_group }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No results yet. Run the analysis to see data.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Pie Chart
        const pieData = @json($clusterDistribution);
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(pieData),
                datasets: [{
                    data: Object.values(pieData),
                    backgroundColor: ['#1C64F2', '#E02424', '#FACA15', '#31C48D'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right', labels: { boxWidth: 10 } } },
                cutout: '70%'
            }
        });

        // Line Chart Data
        // Line Chart Data
        const lineChartData = @json($lineChartData);
        const scatterCtx = document.getElementById('scatterChart').getContext('2d');
        
        const labels = lineChartData.map(item => item.label);
        const dataPoints = lineChartData.map(item => item.value);
        const clusters = lineChartData.map(item => item.cluster);
        const uniqueClusters = [...new Set(clusters)].sort();

        const palette = ['#1C64F2', '#FACA15', '#E02424', '#31C48D', '#7E3AF2', '#F472B6', '#60A5FA', '#A78BFA', '#34D399', '#FB923C'];
        const clusterColors = {};

        uniqueClusters.forEach((cluster, index) => {
            if (cluster.includes('Loyal')) clusterColors[cluster] = '#1C64F2';
            else if (cluster.includes('Potensial')) clusterColors[cluster] = '#FACA15';
            else if (cluster.includes('Berisiko')) clusterColors[cluster] = '#E02424';
            else clusterColors[cluster] = palette[index % palette.length];
        });

        new Chart(scatterCtx, {
            type: 'bar',
            data: { 
                labels: labels,
                datasets: [{
                    label: 'Sales Performance',
                    data: dataPoints,
                    backgroundColor: context => {
                        const index = context.dataIndex;
                        const cluster = clusters[index];
                        return clusterColors[cluster] || '#9CA3AF';
                    },
                    borderWidth: 0,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'bottom',
                        labels: {
                            generateLabels: function(chart) {
                                return uniqueClusters.map(cluster => ({
                                    text: cluster,
                                    fillStyle: clusterColors[cluster],
                                    strokeStyle: clusterColors[cluster],
                                    lineWidth: 0,
                                    hidden: false
                                }));
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                const cluster = clusters[index];
                                return `Sales: ${context.raw} (${cluster})`;
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        title: { display: true, text: 'Distributors' },
                        ticks: { display: false } // Hide labels if too many
                    },
                    y: { 
                        title: { display: true, text: 'Total Sales' },
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endsection
