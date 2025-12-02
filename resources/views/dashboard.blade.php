@extends('layouts.app')

@section('content')
<div class="mb-8">
    <div class="flex flex-col md:flex-row items-center justify-between p-6 bg-gradient-to-r from-brand-600 to-brand-700 rounded-xl shadow-lg text-white">
        <div>
            <h1 class="text-3xl font-bold">Dashboard Overview</h1>
            <p class="mt-2 text-brand-100">Welcome back! Here is the latest performance summary.</p>
        </div>
        <!-- <a href="{{ route('clustering.index') }}" class="mt-4 md:mt-0 inline-flex items-center px-5 py-2.5 text-sm font-medium text-brand-700 bg-white rounded-lg hover:bg-brand-50 focus:ring-4 focus:ring-brand-300 transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            Jalankan Clustering
        </a> -->
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Distributor -->
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Distributor</h3>
            <div class="p-2 bg-blue-50 rounded-lg dark:bg-blue-900">
                <svg class="w-6 h-6 text-brand-600 dark:text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalDistributors) }}</p>
        <p class="mt-1 text-sm text-green-600 flex items-center">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            <span class="font-medium">Active Partners</span>
        </p>
    </div>

    <!-- Rata-rata Kepuasan -->
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Avg. Satisfaction</h3>
            <div class="p-2 bg-green-50 rounded-lg dark:bg-green-900">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($avgSatisfaction, 1) }}<span class="text-lg text-gray-400 font-normal">/5</span></p>
        <p class="mt-1 text-sm text-gray-500">Based on latest scores</p>
    </div>

    <!-- Total Quantity -->
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Quantity</h3>
            <div class="p-2 bg-purple-50 rounded-lg dark:bg-purple-900">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-brand-600">{{ $formattedSales }}</p>
        <p class="mt-1 text-sm text-gray-500">All time volume</p>
    </div>

    <!-- Cluster Terbanyak -->
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-shadow duration-200 dark:bg-gray-800 dark:border-gray-700">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Top Cluster</h3>
            <div class="p-2 bg-yellow-50 rounded-lg dark:bg-yellow-900">
                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
        </div>
        <p class="text-3xl font-bold text-yellow-600">{{ $topCluster }}</p>
        <p class="mt-1 text-sm text-gray-500">Most common segment</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Satisfaction vs Sales Analysis</h3>
        <div class="relative h-80">
            <canvas id="scatterChart"></canvas>
        </div>
    </div>
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Cluster Distribution</h3>
        <div class="relative h-80">
            <canvas id="pieChart"></canvas>
        </div>
    </div>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Top Performing Distributors</h3>
        <a href="{{ route('distributors.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">View All</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-4">Distributor Name</th>
                    <th scope="col" class="px-6 py-4">Region</th>
                    <th scope="col" class="px-6 py-4">Satisfaction</th>
                    <th scope="col" class="px-6 py-4">Quantity (Carton)</th>
                    <th scope="col" class="px-6 py-4">Cluster</th>
                    <th scope="col" class="px-6 py-4 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach($distributors as $distributor)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 font-bold mr-3">
                                {{ substr($distributor->name, 0, 1) }}
                            </div>
                            {{ $distributor->name }}
                        </div>
                    </th>
                    <td class="px-6 py-4">{{ $distributor->region }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 text-yellow-400 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            {{ $distributor->satisfactionScores->avg('score') ? number_format($distributor->satisfactionScores->avg('score'), 0) : '-' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        {{ number_format($distributor->salesPerformances->sum('amount'), 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        @if($distributor->clusteringResult)
                            @php
                                $cluster = $distributor->clusteringResult->cluster_group;
                                $badgeClass = 'bg-gray-100 text-gray-800'; // Default
                                if ($cluster == 'Loyal') $badgeClass = 'bg-green-100 text-green-800';
                                elseif ($cluster == 'Potensial') $badgeClass = 'bg-yellow-100 text-yellow-800';
                                elseif ($cluster == 'Berisiko') $badgeClass = 'bg-red-100 text-red-800';
                                elseif ($cluster == 'Cluster 1') $badgeClass = 'bg-blue-100 text-blue-800';
                                elseif ($cluster == 'Cluster 2') $badgeClass = 'bg-red-100 text-red-800';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $cluster }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                Unclustered
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('distributors.show', $distributor->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-brand-700 bg-brand-50 rounded-lg hover:bg-brand-100 focus:ring-4 focus:outline-none focus:ring-brand-300 dark:bg-brand-900 dark:text-brand-300 dark:hover:bg-brand-800 transition-colors duration-200">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <!-- Lihat Detail -->
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Line Chart Data
        // Line Chart Data
        const lineChartData = @json($lineChartData);
        const scatterCtx = document.getElementById('scatterChart').getContext('2d');
        
        const labels = lineChartData.map(item => item.label);
        const dataPoints = lineChartData.map(item => item.value);
        const clusters = lineChartData.map(item => item.cluster);

        const clusterColors = {
            'Loyal': '#1C64F2',    // Blue
            'Potensial': '#FACA15', // Yellow
            'Berisiko': '#FACA15',  // Yellow (mapped to Yellow as requested for 2 colors)
            'Cluster 1': '#1C64F2', // Blue (mapped to Blue)
            'Cluster 2': '#FACA15'  // Yellow (mapped to Yellow)
        };

        new Chart(scatterCtx, {
            type: 'bar',
            data: { 
                labels: labels,
                datasets: [{
                    label: 'Sales Performance',
                    data: dataPoints,
                    backgroundColor: context => {
                        const index = context.dataIndex;
                        const cluster = clusters[index] || '';
                        const clusterName = cluster.toLowerCase();
                        
                        // User requested 2 colors: Blue and Yellow
                        // Blue for Loyal/High performance
                        if (clusterName.includes('loyal') || clusterName.includes('cluster 1')) {
                            return '#1C64F2'; // Blue
                        }
                        // Yellow for everything else (Potensial, Berisiko, etc.)
                        return '#FACA15'; // Yellow
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
                                const uniqueClusters = [...new Set(clusters)];
                                return uniqueClusters.map(cluster => {
                                    const name = cluster.toLowerCase();
                                    let color = '#FACA15'; // Default Yellow
                                    if (name.includes('loyal') || name.includes('cluster 1')) {
                                        color = '#1C64F2'; // Blue
                                    }
                                    
                                    return {
                                        text: cluster,
                                        fillStyle: color,
                                        strokeStyle: color,
                                        lineWidth: 0,
                                        hidden: false
                                    };
                                });
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
                        title: { display: true, text: 'Quantity (Carton)' },
                        beginAtZero: true
                    }
                }
            }
        });

        // Pie Chart Data
        const pieData = @json($clusterDistribution);
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        
        new Chart(pieCtx, {
            type: 'doughnut', // Changed to doughnut for modern look
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
                plugins: { legend: { position: 'bottom' } },
                cutout: '70%'
            }
        });
    });
</script>
@endsection
