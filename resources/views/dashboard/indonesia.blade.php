<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analisis Kesiapan dan Kelayakan Alutsista TNI Berbasis Data Persenjataan Global</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        googleBlue: '#4285F4',
                        googleRed: '#EA4335',
                        googleYellow: '#FBBC05',
                        googleGreen: '#34A853',
                        googlePurple: '#A142F4',
                        googleTeal: '#24C1E0',
                        bgBase: '#f3f4f6'
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .dashboard-card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
        }
    </style>
</head>
<body class="text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation / Header (Edge to Edge) -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-center w-full shadow-sm sticky top-0 z-50">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Analisa Persenjataan Indonesia</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tugas Besar Analitik dan Visualisasi Data</p>
        </div>
        
        <!-- Global Filters -->
        <div class="mt-4 sm:mt-0 flex space-x-3 w-full sm:w-auto">
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Kategori</label>
                <select id="filter-category" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-md focus:ring-googleBlue focus:border-googleBlue block p-2 outline-none w-full sm:w-48 shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label class="text-xs font-semibold text-gray-500 mb-1 uppercase tracking-wider">Tahun</label>
                <select id="filter-year" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-md focus:ring-googleBlue focus:border-googleBlue block p-2 outline-none w-full sm:w-40 shadow-sm">
                    <option value="">Semua Tahun</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </header>

    <!-- Tab Navigation -->
    <div class="bg-white border-b border-gray-200 px-6 w-full flex items-center space-x-6 text-sm font-medium">
        <a href="/dashboard/global" class="text-gray-500 hover:text-gray-700 py-3 border-b-2 border-transparent transition-colors">Global Analytics</a>
        <a href="/dashboard/indonesia" class="text-googleBlue py-3 border-b-2 border-googleBlue">Indonesia Analytics</a>
    </div>

    <!-- Main Content (Full Width) -->
    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-grow flex flex-col space-y-6">
        
        <!-- Scorecards / KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dashboard-card p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-googleBlue flex items-center justify-center text-2xl shrink-0">
                    <i class="ph-fill ph-crosshair"></i>
                </div>
                <div>
                    <h3 class="text-gray-500 text-[11px] font-bold uppercase tracking-wider mb-1">Total Inventaris Persenjataan</h3>
                    <div class="flex items-end">
                        <span class="text-3xl font-bold text-gray-900 leading-none" id="kpi-total">-</span>
                        <span class="ml-1 text-sm text-gray-500 font-medium">Sistem</span>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-green-50 text-googleGreen flex items-center justify-center text-2xl shrink-0">
                    <i class="ph-fill ph-currency-dollar"></i>
                </div>
                <div>
                    <h3 class="text-gray-500 text-[11px] font-bold uppercase tracking-wider mb-1">Rata-rata Biaya Pengadaan</h3>
                    <div class="flex items-end">
                        <span class="text-3xl font-bold text-gray-900 leading-none" id="kpi-cost">-</span>
                        <span class="ml-1 text-sm text-gray-500 font-medium">Per Unit</span>
                    </div>
                </div>
            </div>
            
            <div class="dashboard-card p-5 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-googleRed flex items-center justify-center text-2xl shrink-0">
                    <i class="ph-fill ph-fire"></i>
                </div>
                <div>
                    <h3 class="text-gray-500 text-[11px] font-bold uppercase tracking-wider mb-1">Total Teruji Tempur</h3>
                    <div class="flex items-end">
                        <span class="text-3xl font-bold text-gray-900 leading-none" id="kpi-proven">-</span>
                        <span class="ml-1 text-sm text-gray-500 font-medium">Sistem</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Grid 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 h-auto">
            <!-- Stacked Bar Chart -->
            <div class="dashboard-card p-6 flex flex-col">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base font-semibold text-gray-800">Laporan penggunaan senjata berdasarkan matra/angkatan laut/darat/udara</h3>
                    <select id="sort-bar" class="text-xs border border-gray-300 text-gray-600 rounded p-1 outline-none focus:ring-1 focus:ring-googleBlue" onchange="renderBarChart()">
                        <option value="name_asc">A-Z</option>
                        <option value="name_desc">Z-A</option>
                        <option value="total_desc">Total Terbanyak</option>
                        <option value="total_asc">Total Terkecil</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mb-4">Distribusi kategori aset per angkatan (Darat, Laut, Udara)</p>
                <div class="relative w-full" style="height: 350px;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <!-- Line / Area Chart -->
            <div class="dashboard-card p-6 flex flex-col">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base font-semibold text-gray-800">Tren Pengadaan Berdasarkan Tahun</h3>
                    <select id="sort-line" class="text-xs border border-gray-300 text-gray-600 rounded p-1 outline-none focus:ring-1 focus:ring-googleBlue" onchange="renderLineChart()">
                        <option value="year_asc">Tahun Lama-Baru</option>
                        <option value="year_desc">Tahun Baru-Lama</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mb-4">Volume pengenalan persenjataan dari masa ke masa</p>
                <div class="relative w-full" style="height: 350px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Grid 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
            <!-- Pie Chart -->
            <div class="dashboard-card p-6 flex flex-col col-span-1">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base font-semibold text-gray-800">Status Teruji Tempur</h3>
                    <select id="sort-pie" class="text-xs border border-gray-300 text-gray-600 rounded p-1 outline-none focus:ring-1 focus:ring-googleBlue" onchange="renderPieChart()">
                        <option value="total_desc">Jumlah Terbanyak</option>
                        <option value="total_asc">Jumlah Terkecil</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mb-4">Proporsi sistem yang telah tervalidasi</p>
                <div class="relative w-full flex-grow flex justify-center items-center" style="height: 320px;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>

            <!-- Scatter Plot -->
            <div class="dashboard-card p-6 flex flex-col col-span-1 lg:col-span-2">
                <h3 class="text-base font-semibold text-gray-800 mb-1">Analisis Value for Money (Harga vs Adopsi Global)</h3>
                <p class="text-xs text-gray-500 mb-4">Korelasi antara biaya unit (skala logaritmik) dengan jumlah negara operator</p>
                <div class="relative w-full flex-grow" style="height: 320px;">
                    <canvas id="scatterChart"></canvas>
                </div>
            </div>
        </div>
        
    </main>

    <script>
        // Global Chart Configurations
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#6b7280'; // gray-500
        Chart.defaults.scale.grid.color = '#f3f4f6'; // gray-100
        Chart.defaults.scale.grid.borderColor = '#e5e7eb'; // gray-200
        
        const tooltipDefaults = {
            backgroundColor: 'rgba(255, 255, 255, 0.95)',
            titleColor: '#111827',
            bodyColor: '#374151',
            borderColor: '#e5e7eb',
            borderWidth: 1,
            padding: 10,
            boxPadding: 4,
            usePointStyle: true,
            titleFont: { size: 13, weight: 'bold' },
            bodyFont: { size: 12 }
        };

        let charts = {};
        let currentData = null; // Store fetched data globally for sorting

        async function fetchDashboardData() {
            const category = document.getElementById('filter-category').value;
            const year = document.getElementById('filter-year').value;
            
            const url = `/dashboard/data?scope=indonesia&category=${encodeURIComponent(category)}&year=${encodeURIComponent(year)}`;
            
            try {
                const response = await fetch(url);
                currentData = await response.json();
                
                updateKPIs(currentData.kpi);
                
                // Render charts with sorting applied
                renderBarChart();
                renderLineChart();
                renderPieChart();
                renderScatterChart();
                
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        // --- SORTING LOGIC --- //

        function renderCategoryBarChart() {
            if (!currentData || !currentData.categoryBarChart) return;
            const sortOrder = document.getElementById('sort-category-bar').value;
            
            let labels = [...currentData.categoryBarChart.labels];
            let values = [...currentData.categoryBarChart.values];

            let items = labels.map((label, i) => ({ label, value: values[i] }));
            
            if (sortOrder === 'name_asc') items.sort((a, b) => a.label.localeCompare(b.label));
            else if (sortOrder === 'name_desc') items.sort((a, b) => b.label.localeCompare(a.label));
            else if (sortOrder === 'total_desc') items.sort((a, b) => b.value - a.value);
            else if (sortOrder === 'total_asc') items.sort((a, b) => a.value - b.value);

            updateCategoryBarChart(items.map(it => it.label), items.map(it => it.value));
        }

        function renderBarChart() {
            if (!currentData) return;
            const sortOrder = document.getElementById('sort-bar').value;
            
            // Deep copy to avoid mutating original data
            let labels = [...currentData.barChart.labels];
            let datasets = JSON.parse(JSON.stringify(currentData.barChart.datasets)); 

            // Combine into objects for sorting
            let items = labels.map((label, i) => {
                let total = datasets.reduce((sum, ds) => sum + ds.data[i], 0);
                return { label, total, data: datasets.map(ds => ds.data[i]) };
            });

            // Sort logic
            if (sortOrder === 'name_asc') items.sort((a, b) => a.label.localeCompare(b.label));
            else if (sortOrder === 'name_desc') items.sort((a, b) => b.label.localeCompare(a.label));
            else if (sortOrder === 'total_desc') items.sort((a, b) => b.total - a.total);
            else if (sortOrder === 'total_asc') items.sort((a, b) => a.total - b.total);

            // Unpack sorted data
            let sortedLabels = items.map(it => it.label);
            datasets.forEach((ds, dsIndex) => {
                ds.data = items.map(it => it.data[dsIndex]);
            });

            updateBarChart(sortedLabels, datasets);
        }

        function renderLineChart() {
            if (!currentData) return;
            const sortOrder = document.getElementById('sort-line').value;
            
            let labels = [...currentData.lineChart.labels];
            let values = [...currentData.lineChart.values];

            let items = labels.map((label, i) => ({ label, value: values[i] }));
            
            // Sort logic
            if (sortOrder === 'year_asc') items.sort((a, b) => a.label.localeCompare(b.label));
            else if (sortOrder === 'year_desc') items.sort((a, b) => b.label.localeCompare(a.label));

            updateLineChart(items.map(it => it.label), items.map(it => it.value));
        }

        function renderPieChart() {
            if (!currentData) return;
            const sortOrder = document.getElementById('sort-pie').value;
            
            let labels = [...currentData.pieChart.labels];
            let values = [...currentData.pieChart.values];

            let items = labels.map((label, i) => ({ label, value: values[i] }));
            
            // Sort logic
            if (sortOrder === 'total_desc') items.sort((a, b) => b.value - a.value);
            else if (sortOrder === 'total_asc') items.sort((a, b) => a.value - b.value);

            updatePieChart(items.map(it => it.label), items.map(it => it.value));
        }

        function renderScatterChart() {
            if (!currentData) return;
            // Scatter chart points are absolute coordinates, no sorting needed.
            updateScatterChart(currentData.scatterChart.points);
        }

        // --- DRAW CHARTS --- //

        function updateKPIs(kpi) {
            document.getElementById('kpi-total').innerText = kpi.total_weapons;
            document.getElementById('kpi-cost').innerText = kpi.avg_cost;
            document.getElementById('kpi-proven').innerText = kpi.combat_proven;
        }

        // Removed category bar chart

        function updateBarChart(labels, datasets) {
            const ctx = document.getElementById('barChart').getContext('2d');
            if(charts.bar) charts.bar.destroy();
            
            // Override dataset colors to match SS: Land (Blue), Air (Orange), Sea (Purple)
            datasets.forEach(ds => {
                if(ds.label === 'Land') ds.backgroundColor = '#4285F4'; // Google Blue
                if(ds.label === 'Air') ds.backgroundColor = '#F6B26B'; // Orange
                if(ds.label === 'Sea') ds.backgroundColor = '#B4A7D6'; // Purple
                ds.borderWidth = 0;
            });

            charts.bar = new Chart(ctx, {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    indexAxis: 'y', // Makes the bar chart horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: tooltipDefaults
                    },
                    scales: {
                        x: { stacked: true, beginAtZero: true, border: { dash: [4, 4] } },
                        y: { stacked: true, grid: { display: false } }
                    }
                }
            });
        }

        function updateLineChart(labels, values) {
            const ctx = document.getElementById('lineChart').getContext('2d');
            if(charts.line) charts.line.destroy();

            charts.line = new Chart(ctx, {
                type: 'line',
                data: {
                    labels,
                    datasets: [{
                        label: 'Volume Pengadaan',
                        data: values,
                        borderColor: '#4285F4',
                        backgroundColor: 'rgba(66, 133, 244, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4285F4',
                        pointBorderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { 
                        legend: { display: false },
                        tooltip: tooltipDefaults
                    },
                    scales: { 
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, border: { dash: [4, 4] } } 
                    }
                }
            });
        }

        function updatePieChart(labels, values) {
            const ctx = document.getElementById('pieChart').getContext('2d');
            if(charts.pie) charts.pie.destroy();
            
            const bgColors = labels.map(l => {
                if(l === 'No') return '#4285F4'; // Blue
                if(l === 'Yes') return '#F6B26B';  // Orange
                if(l === 'Limited') return '#B4A7D6'; // Purple
                return '#9ca3af'; // Gray
            });

            charts.pie = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values,
                        backgroundColor: bgColors,
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '65%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } },
                        tooltip: tooltipDefaults
                    }
                }
            });
        }

        function updateScatterChart(points) {
            const ctx = document.getElementById('scatterChart').getContext('2d');
            if(charts.scatter) charts.scatter.destroy();
            
            charts.scatter = new Chart(ctx, {
                type: 'scatter',
                data: {
                    datasets: [{
                        label: 'Sistem Persenjataan',
                        data: points,
                        backgroundColor: 'rgba(66, 133, 244, 0.5)',
                        borderColor: '#4285F4',
                        borderWidth: 1,
                        pointRadius: 4,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipDefaults,
                            callbacks: {
                                label: function(context) {
                                    let point = context.raw;
                                    return [
                                        point.name,
                                        'Harga: $' + point.x.toLocaleString(),
                                        'Pengguna: ' + point.y + ' negara'
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            title: { display: true, text: 'Harga per Unit (USD) - Skala Logaritmik', color: '#6b7280', font: {size: 11} },
                            type: 'logarithmic',
                            grid: { color: '#f3f4f6' }
                        },
                        y: { 
                            title: { display: true, text: 'Jumlah Negara Pengguna', color: '#6b7280', font: {size: 11} },
                            beginAtZero: true,
                            grid: { color: '#f3f4f6' }
                        }
                    }
                }
            });
        }

        // Initialize and setup event listeners
        document.addEventListener('DOMContentLoaded', () => {
            fetchDashboardData();
            
            document.getElementById('filter-category').addEventListener('change', fetchDashboardData);
            document.getElementById('filter-year').addEventListener('change', fetchDashboardData);
        });
    </script>
</body>
</html>