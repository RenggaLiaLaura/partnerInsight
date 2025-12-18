<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Hasil Analisis Clustering</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
        .report-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            border: 1px solid #d3d3d3;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        @media print {
            .report-container {
                margin: 0;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                page-break-after: always;
            }
            .keep-together {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-black dark:text-gray-100 font-sans antialiased">

<div class="report-container relative text-gray-800 dark:text-gray-100 font-sans">
    
    <!-- Header -->
    <div class="border-b-4 border-double border-gray-800 pb-4 mb-8">
        <div class="flex justify-between items-end">
            <div>
                 <h1 class="text-xl font-extrabold uppercase tracking-widest text-gray-900">PT. ADYABOGA PRANATA INDUSTRIES</h1>
                 <p class="text-sm text-gray-500 uppercase tracking-wide">Clustering Analysis Report</p>
            </div>
            <div class="text-right">
                 <div class="text-sm text-gray-500">Refference Number</div>
                 <div class="text-lg font-mono font-bold text-gray-900">CLS.RP.{{ date('Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Info Grid -->
    <div class="mb-10 bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm">
        <div class="grid grid-cols-2 gap-x-12 gap-y-2">
            <div class="flex justify-between border-b border-gray-200 pb-1">
                <span class="font-semibold text-gray-600">Tanggal Laporan</span>
                <span class="font-medium text-gray-900">{{ date('d F Y') }}</span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-1">
                <span class="font-semibold text-gray-600">Dibuat Oleh</span>
                <span class="font-medium text-gray-900">System Administrator</span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-1">
                <span class="font-semibold text-gray-600">Total Distributor</span>
                <span class="font-medium text-gray-900">{{ $results->count() ?? 0 }} Data</span>
            </div>
            <div class="flex justify-between border-b border-gray-200 pb-1">
                <span class="font-semibold text-gray-600">Total Cluster</span>
                <span class="font-medium text-gray-900">{{ count($chartData) }} Groups</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="mb-8 space-y-8">
        <!-- Bar Chart (Full Width) -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm break-inside-avoid">
            <h3 class="text-center font-bold mb-6 text-gray-700 text-sm uppercase tracking-wider border-b pb-4">Grafik Karakteristik Cluster</h3>
            <div class="relative w-full" style="height: 300px">
                <canvas id="clusterChart"></canvas>
            </div>
        </div>
        
        <!-- Pie Chart (Centered/Smaller) -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm break-inside-avoid">
            <h3 class="text-center font-bold mb-6 text-gray-700 text-sm uppercase tracking-wider border-b pb-4">Distribusi Anggota</h3>
            <div class="relative flex justify-center w-full" style="height: 250px">
                <canvas id="distributionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="mb-10">
        <h3 class="font-bold mb-4 text-gray-800 uppercase text-sm border-l-4 border-blue-600 pl-3">Ringkasan Karakteristik</h3>
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-xs tracking-wider">
                        <th class="p-3 text-left font-bold">Cluster Group</th>
                        <th class="p-3 text-center font-bold">Jml Distributor</th>
                        <th class="p-3 text-center font-bold">Rata-rata Kepuasan</th>
                        <th class="p-3 text-right font-bold">Rata-rata Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($chartData as $data)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-3 font-semibold text-blue-900">{{ $data['cluster'] }}</td>
                        <td class="p-3 text-center text-gray-700">{{ $data['count'] }}</td>
                        <td class="p-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $data['avg_satisfaction'] }}
                            </span>
                        </td>
                        <td class="p-3 text-right font-mono text-gray-700">Rp {{ number_format($data['avg_sales'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detailed List -->
    <div class="mb-8">
        <h3 class="font-bold mb-6 text-gray-800 uppercase text-sm border-l-4 border-green-600 pl-3">Detail Anggota Cluster</h3>
        
        @foreach($clusters as $group => $items)
        <div class="mb-8 keep-together bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-800 text-sm">{{ $group }}</h4>
                <span class="bg-gray-200 text-gray-600 text-xs px-2 py-1 rounded-md">{{ $items->count() }} Distributor</span>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-white text-gray-500 border-b border-gray-100 text-xs uppercase">
                        <th class="px-4 py-2 text-left w-12 font-medium">No</th>
                        <th class="px-4 py-2 text-left font-medium">Nama Distributor</th>
                        <th class="px-4 py-2 text-center w-32 font-medium">Kepuasan</th>
                        <th class="px-4 py-2 text-right w-40 font-medium">Penjualan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($items as $index => $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-center text-gray-400 text-xs">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-gray-700">{{ $item->distributor->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-2 text-center text-gray-600">{{ $item->score_satisfaction }}</td>
                        <td class="px-4 py-2 text-right text-gray-600 font-mono">Rp {{ number_format($item->score_sales, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
    </div>

    <!-- Signatures -->
    <div class="flex justify-end mt-12 px-8 keep-together">
        <div class="text-center w-64">
            <p class="text-sm text-gray-600 mb-1">Tangerang, {{ date('d F Y') }}</p>
            <p class="text-sm font-bold text-gray-800 mb-16">Mengetahui,</p>
            <div class="border-b border-gray-400 w-full mb-2"></div>
            <p class="font-bold text-gray-900">Management</p>
        </div>
    </div>

</div>

<!-- Print Button -->
<div class="fixed bottom-4 right-4 no-print">
    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full shadow-lg flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Print / Save PDF
    </button>
</div>

<script>
    const chartData = @json($chartData);
    
    const labels = chartData.map(d => d.cluster);
    const satData = chartData.map(d => d.avg_satisfaction);
    const salesData = chartData.map(d => d.avg_sales);

    const ctx = document.getElementById('clusterChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Rata-rata Kepuasan',
                    data: satData,
                    backgroundColor: '#1d4ed8', // Blue-700
                    yAxisID: 'y',
                    barPercentage: 0.4
                },
                {
                    label: 'Rata-rata Penjualan',
                    data: salesData,
                    backgroundColor: '#10b981', // Emerald-500
                    yAxisID: 'y1',
                    barPercentage: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Score Kepuasan (1-5)' },
                    min: 0,
                    max: 5
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: { display: true, text: 'Total Penjualan' },
                    grid: { drawOnChartArea: false }
                }
            },
            animation: false
        }
    });

    // Distribution Chart (Pie)
    const counts = chartData.map(d => d.count);
    const ctxDist = document.getElementById('distributionChart').getContext('2d');
    new Chart(ctxDist, {
        type: 'pie',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: [
                    '#1d4ed8', // Blue
                    '#10b981', // Emerald
                    '#f59e0b', // Amber
                    '#ef4444', // Red
                    '#8b5cf6', // Violet
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        font: { size: 10 }
                    }
                }
            },
            layout: {
                padding: 10
            },
            animation: false
        }
    });
</script>

</body>
</html>
