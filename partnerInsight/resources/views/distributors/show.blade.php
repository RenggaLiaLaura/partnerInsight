@extends('layouts.app')

@section('content')
<div class="mb-6">
    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 text-sm font-medium md:space-x-2">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-gray-700 hover:text-brand-600 dark:text-gray-300 dark:hover:text-white">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path></svg>
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('distributors.index') }}" class="ml-1 text-gray-700 hover:text-brand-600 md:ml-2 dark:text-gray-300 dark:hover:text-white">Distributors</a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-gray-400 md:ml-2 dark:text-gray-500" aria-current="page">Profile</span>
                </div>
            </li>
        </ol>
    </nav>
    
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
        <div class="flex items-center">
            <div class="h-16 w-16 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 text-2xl font-bold mr-4 shadow-sm">
                {{ substr($distributor->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $distributor->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $distributor->region }} • {{ $distributor->phone }}</p>
            </div>
        </div>
        @if(Auth::user()->role === 'admin')
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="{{ route('distributors.edit', $distributor->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-brand-300 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Edit Profile
            </a>
        </div>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Info Card -->
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
        <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Current Status</h3>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-500">Cluster Group</p>
                @if($distributor->clusteringResult)
                    <span class="inline-flex items-center mt-1 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        {{ $distributor->clusteringResult->cluster_group }}
                    </span>
                @else
                    <span class="inline-flex items-center mt-1 px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Unclustered</span>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Avg. Satisfaction</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $distributor->satisfactionScores->avg('score') ? number_format($distributor->satisfactionScores->avg('score'), 1) : '-' }}
                        <span class="text-xs text-gray-400 font-normal">/5</span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Sales</p>
                    <p class="text-xl font-bold text-brand-600">
                        {{ number_format($distributor->salesPerformances->sum('amount') / 1000000, 1) }}M
                    </p>
                </div>
            </div>
            <div>
                <p class="text-sm text-gray-500">Address</p>
                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $distributor->address }}</p>
            </div>
        </div>
    </div>

    <!-- Satisfaction Chart -->
    <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 lg:col-span-2">
        <h3 class="mb-4 text-lg font-bold text-gray-900 dark:text-white">Performance History</h3>
        <div class="relative h-64">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Satisfaction Scores -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Satisfaction History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Period</th>
                        <th scope="col" class="px-6 py-3">Score</th>
                        @if(Auth::user()->role === 'admin')
                        <th scope="col" class="px-6 py-3 text-right">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($distributor->satisfactionScores as $score)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">{{ date('M Y', strtotime($score->period)) }}</td>
                        <td class="px-6 py-4">
                            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                {{ $score->score }}
                            </span>
                        </td>
                        @if(Auth::user()->role === 'admin')
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('satisfaction.edit', $score->id) }}" class="text-brand-600 hover:underline">Edit</a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Sales -->
    <div class="bg-white border border-gray-100 rounded-xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Sales History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Period</th>
                        <th scope="col" class="px-6 py-3">Amount</th>
                        @if(Auth::user()->role === 'admin')
                        <th scope="col" class="px-6 py-3 text-right">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($distributor->salesPerformances as $sale)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">{{ date('M Y', strtotime($sale->period)) }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                            Rp {{ number_format($sale->amount, 0, ',', '.') }}
                        </td>
                        @if(Auth::user()->role === 'admin')
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sales.edit', $sale->id) }}" class="text-brand-600 hover:underline">Edit</a>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($satisfactionData['labels']),
                datasets: [
                    {
                        label: 'Satisfaction Score',
                        data: @json($satisfactionData['data']),
                        borderColor: '#10B981', // Green
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        yAxisID: 'y',
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Sales (Millions)',
                        data: @json($salesData['data']).map(val => val / 1000000),
                        borderColor: '#1C64F2', // Brand Blue
                        backgroundColor: 'rgba(28, 100, 242, 0.1)',
                        yAxisID: 'y1',
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Score (0-5)' },
                        min: 0,
                        max: 5
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Sales (Millions Rp)' },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    });
</script>
@endsection
