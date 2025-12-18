<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Hasil Evaluasi Kuesioner Kepuasan Pelanggan</title>
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
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-black dark:text-gray-100 font-sans antialiased">

<div class="report-container relative text-black"> <!-- Force text black for print -->
    
    <!-- Header -->
    <div class="flex justify-between items-center border-b-2 border-black pb-2 mb-6">
        <div>
            <!-- Optional Logo placeholder -->
        </div>
        <div class="text-center flex-1">
            <h1 class="text-xl font-bold uppercase tracking-wide">Laporan Hasil Evaluasi Kuesioner Kepuasan Pelanggan</h1>
        </div>
        <div class="text-xs font-mono">
            MKT.FO.05.005
        </div>
    </div>

    <!-- Metadata Table -->
    <div class="mb-8 text-sm">
        <table class="w-full">
            <tr>
                <td class="w-32 font-semibold">Tahun</td>
                <td class="w-4">:</td>
                <td>{{ $year }}</td>
                <td class="w-32 font-semibold">Jadwal</td>
                <td class="w-4">:</td>
                <td>{{ $monthName ? $monthName . ' / ' . $year : 'Per Semester' }}</td>
            </tr>
            <tr>
                <td class="font-semibold">Jenis</td>
                <td>:</td>
                <td>Survey Kepuasan Pelanggan - Produk Ekspor</td>
                <td class="font-semibold">Departemen</td>
                <td>:</td>
                <td>Marketing</td>
            </tr>
            <tr>
                <td class="font-semibold">Tim</td>
                <td>:</td>
                <td>Sopanna, Gloria</td>
                <td colspan="3"></td>
            </tr>
        </table>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div>
            <h3 class="text-center font-bold mb-2 uppercase text-sm">Kualitas Produk</h3>
            <canvas id="qualityChart"></canvas>
            <div class="text-center text-xs mt-2 italic">Rata-rata: {{ $summary['prod_avg'] }}</div>
        </div>
        <div>
            <h3 class="text-center font-bold mb-2 uppercase text-sm">Pelayanan</h3>
            <canvas id="serviceChart"></canvas>
            <div class="text-center text-xs mt-2 italic">Rata-rata: {{ $summary['serv_avg'] }}</div>
        </div>
    </div>

    <!-- Text Summary -->
    <div class="text-xs text-justify leading-relaxed mb-8 space-y-2">
        <p>
            Berdasarkan grafik di atas, hasil penilaian kepuasan pelanggan terhadap kualitas produk PT. Adyaboga Pranata Industries menunjukan nilai rata-rata tertinggi yaitu <strong>{{ $summary['prod_max_val'] }}</strong> yaitu pada kriteria <strong>{{ $summary['prod_max_items'] }}</strong>.
        </p>
        <p>
            Berdasarkan grafik di atas, kepuasan pelanggan terhadap pelayanan produk ekspor PT. Adyaboga Pranata Industries menunjukan nilai rata-rata tertinggi yaitu <strong>{{ $summary['serv_max_val'] }}</strong> pada kriteria <strong>{{ $summary['serv_max_items'] }}</strong>.
            Dan hasil kepuasan terendah terhadap pelayanan dengan nilai rata-rata <strong>{{ $summary['serv_min_val'] }}</strong> yaitu pada kriteria <strong>{{ $summary['serv_min_items'] }}</strong>.
        </p>
        <p>
            Berdasarkan hasil rata-rata secara keseluruhan yaitu untuk kualitas produk <strong>{{ $summary['prod_avg'] }}</strong> & pelayanan <strong>{{ $summary['serv_avg'] }}</strong>, maka dapat disimpulkan bahwa pelanggan puas terhadap kualitas produk dan pelayanan untuk produk ekspor PT. Adyaboga Pranata Industries.
        </p>
    </div>

    <!-- Distributor Comparison Chart -->
    <div class="mb-12">
        <h3 class="text-center font-bold mb-4 uppercase text-sm">Peringkat Kepuasan Distributor (Top 15)</h3>
        <div class="relative w-full" style="height: 350px">
             <canvas id="distributorScoreChart"></canvas>
        </div>
    </div>

    <!-- Respondent Details -->
    <div class="mb-12">
        <h3 class="font-bold border-b border-black mb-2 pb-1 text-sm uppercase">Data Responden</h3>
        <table class="w-full text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 border-b border-gray-300">
                    <th class="py-2 px-2 text-left w-10">No</th>
                    <th class="py-2 px-2 text-left">Nama Distributor</th>
                    <th class="py-2 px-2 text-center w-32">Periode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($scores as $index => $score)
                <tr class="border-b border-gray-200">
                    <td class="py-1 px-2">{{ $index + 1 }}</td>
                    <td class="py-1 px-2 font-medium">{{ $score->distributor->name ?? 'Unknown' }}</td>
                    <td class="py-1 px-2 text-center">{{ \Carbon\Carbon::parse($score->period)->translatedFormat('F Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Signatures -->
    <div class="flex justify-between items-end px-12 pb-12">
        <div class="text-center">
            <p class="mb-16">Dibuat oleh,</p>
            <p class="font-bold underline">Sopanna</p>
            <p>Kepala Marketing</p>
        </div>
        <div class="text-center">
            <p class="mb-4">Tangerang, {{ date('d F Y') }}</p>
            <p class="mb-12">Mengetahui,</p>
            <p class="font-bold underline">Lipto Setiawan</p>
            <p>Wakil Manajemen</p>
        </div>
    </div>

</div>

<!-- Print Button for convenience -->
<div class="fixed bottom-4 right-4 no-print">
    <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full shadow-lg flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
        Print / Save PDF
    </button>
</div>

<script>
    // Data passed from controller
    const productData = @json($productStats);
    const serviceData = @json($serviceStats);

    // Chart Configuration
    const createChart = (ctx, label, data, color) => {
        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: '',
                    data: Object.values(data),
                    backgroundColor: color,
                    borderWidth: 1,
                    barThickness: 20
                }]
            },
            options: {
                indexAxis: 'x',
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.raw;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5.0,
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        ticks: {
                            font: { size: 9 }, // Smaller font for x-axis labels
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                },
                animation: false // Disable animation for printing
            }
        });
    };

    // Render Charts
    createChart(
        document.getElementById('qualityChart'), 
        'Kualitas Produk', 
        productData, 
        '#1e40af' // Blue-800
    );

    createChart(
        document.getElementById('serviceChart'), 
        'Pelayanan', 
        serviceData, 
        '#1e40af'
    );

    // Distributor Comparison Chart
    const distributorData = @json($distributorChartData);
    new Chart(document.getElementById('distributorScoreChart'), {
        type: 'bar',
        data: {
            labels: distributorData.map(d => d.label),
            datasets: [{
                label: 'Score',
                data: distributorData.map(d => d.value),
                backgroundColor: distributorData.map(d => d.value >= 4 ? '#10b981' : (d.value >= 3 ? '#f59e0b' : '#ef4444')), // Green, Amber, Red
                borderWidth: 1,
                barThickness: 15
            }]
        },
        options: {
            indexAxis: 'y', // Horizontal Bar
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.raw;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 5.0,
                    ticks: { stepSize: 1 }
                },
                y: {
                    ticks: {
                        font: { size: 10 }
                    }
                }
            },
            animation: false
        }
    });
</script>

</body>
</html>
