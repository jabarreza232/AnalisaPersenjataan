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
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .dashboard-card {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.03);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px -8px rgba(0, 0, 0, 0.15);
            border-color: #d1d5db;
        }
    </style>
</head>
<body class="text-gray-800 antialiased min-h-screen flex flex-col">

    <!-- Top Navigation / Header (Edge to Edge) -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-center w-full shadow-sm sticky top-0 z-50">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Laporan Analisis Sistem Persenjataan Global</h1>
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
    <div class="bg-white border-b border-gray-200 px-6 w-full flex items-center space-x-6 text-sm font-medium overflow-x-auto">
        <a href="/dashboard/global" class="text-googleBlue py-3 border-b-2 border-googleBlue whitespace-nowrap">Global Analytics</a>
        <a href="/dashboard/indonesia" class="text-gray-500 hover:text-gray-700 py-3 border-b-2 border-transparent transition-colors whitespace-nowrap">Indonesia Analytics</a>
        <a href="/dashboard/eda" class="text-gray-500 hover:text-gray-700 py-3 border-b-2 border-transparent transition-colors whitespace-nowrap">EDA & Data Cleaning</a>
    </div>

    <!-- Main Content (Full Width) -->
    <main class="w-full px-4 sm:px-6 lg:px-8 py-6 flex-grow flex flex-col space-y-6">
        
        <!-- Scorecards / KPIs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="dashboard-card p-5 flex items-center gap-4" data-aos="fade-up" data-aos-delay="0">
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
            
            <div class="dashboard-card p-5 flex items-center gap-4" data-aos="fade-up" data-aos-delay="100">
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
            
            <div class="dashboard-card p-5 flex items-center gap-4" data-aos="fade-up" data-aos-delay="200">
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

        <!-- Chart Grid 1 (Top) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
            <!-- Category Bar Chart -->
            <div class="dashboard-card p-6 flex flex-col col-span-1" data-aos="fade-up" data-aos-delay="100">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base font-semibold text-gray-800">Distribusi Kategori</h3>
                    <select id="sort-category-bar" class="text-xs border border-gray-300 text-gray-600 rounded p-1 outline-none focus:ring-1 focus:ring-googleBlue" onchange="renderCategoryBarChart()">
                        <option value="total_desc">Total Terbanyak</option>
                        <option value="total_asc">Total Terkecil</option>
                        <option value="name_asc">A-Z</option>
                        <option value="name_desc">Z-A</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mb-4">Klik batang grafik untuk filter</p>
                <div class="relative w-full flex-grow" style="min-height: 320px;">
                    <canvas id="categoryBarChart"></canvas>
                </div>
            </div>

            <!-- Scatter Plot -->
            <div class="dashboard-card p-6 flex flex-col col-span-1 lg:col-span-2" data-aos="fade-up" data-aos-delay="200">
                <h3 class="text-base font-semibold text-gray-800 mb-1">Analisis Value for Money (Harga vs Adopsi Global)</h3>
                <p class="text-xs text-gray-500 mb-4">Korelasi antara biaya unit (skala logaritmik) dengan jumlah negara operator</p>
                <div class="relative w-full flex-grow" style="min-height: 320px;">
                    <canvas id="scatterChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart Grid 2 (Middle) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
            <!-- Stacked Bar Chart -->
            <div class="dashboard-card p-6 flex flex-col col-span-1" data-aos="fade-up" data-aos-delay="100">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base font-semibold text-gray-800">Penggunaan Berdasarkan Matra</h3>
                    <select id="sort-bar" class="text-xs border border-gray-300 text-gray-600 rounded p-1 outline-none focus:ring-1 focus:ring-googleBlue" onchange="renderBarChart()">
                        <option value="name_asc">A-Z</option>
                        <option value="name_desc">Z-A</option>
                        <option value="total_desc">Terbanyak</option>
                        <option value="total_asc">Terkecil</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mb-4">Darat, Laut, Udara</p>
                <div class="relative w-full flex-grow" style="min-height: 320px;">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <!-- Line / Area Chart -->
            <div class="dashboard-card p-6 flex flex-col col-span-1" data-aos="fade-up" data-aos-delay="200">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base font-semibold text-gray-800">Tren Pengadaan Berdasarkan Tahun</h3>
                    <select id="sort-line" class="text-xs border border-gray-300 text-gray-600 rounded p-1 outline-none focus:ring-1 focus:ring-googleBlue" onchange="renderLineChart()">
                        <option value="year_asc">Lama-Baru</option>
                        <option value="year_desc">Baru-Lama</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mb-4">Volume pengenalan masa ke masa</p>
                <div class="relative w-full flex-grow" style="min-height: 320px;">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="dashboard-card p-6 flex flex-col col-span-1" data-aos="fade-up" data-aos-delay="300">
                <div class="flex justify-between items-start mb-1">
                    <h3 class="text-base font-semibold text-gray-800">Status Teruji Tempur</h3>
                    <select id="sort-pie" class="text-xs border border-gray-300 text-gray-600 rounded p-1 outline-none focus:ring-1 focus:ring-googleBlue" onchange="renderPieChart()">
                        <option value="total_desc">Terbanyak</option>
                        <option value="total_asc">Terkecil</option>
                    </select>
                </div>
                <p class="text-xs text-gray-500 mb-4">Proporsi divalidasi</p>
                <div class="relative w-full flex-grow flex justify-center items-center" style="min-height: 280px;">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <!-- NEW SECTION: Advanced Analytics & AI Insights -->
        <div class="mt-8 border-t border-gray-200 pt-8" data-aos="fade-up">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 shrink-0 bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl flex items-center justify-center shadow-inner border border-blue-100">
                    <i class="ph-fill ph-brain text-3xl text-googlePurple animate-bounce"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 tracking-tight">Advanced Analytics & AI Insights (Global Scope)</h2>
                    <p class="text-sm text-gray-500">Menganalisis kapabilitas lintas-matra dan model prediktif Machine Learning (Random Forest) untuk seluruh populasi data global.</p>
                </div>
            </div>
            
            <!-- INFO CARDS (Data, Algoritma, Insight) -->
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-50/50 border border-blue-100 p-4 rounded-lg transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                    <h4 class="font-bold text-blue-800 text-xs uppercase mb-1 flex items-center"><i class="ph-fill ph-database mr-1"></i> Sumber Data</h4>
                    <p class="text-xs text-gray-600 leading-relaxed text-justify">Seluruh analitik pada halaman ini (termasuk Model AI) memproses <strong>100% populasi data Global (128 Negara)</strong>. Memberikan representasi tolok ukur (benchmark) standar persenjataan dunia sesungguhnya.</p>
                </div>
                <div class="bg-purple-50/50 border border-purple-100 p-4 rounded-lg transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                    <h4 class="font-bold text-purple-800 text-xs uppercase mb-1 flex items-center"><i class="ph-fill ph-brain mr-1"></i> Algoritma & Alasan Pemilihan</h4>
                    <p class="text-xs text-gray-600 leading-relaxed text-justify">Model <strong>Random Forest Classifier</strong> dipilih karena ketahanannya terhadap <em>outlier</em> harga alutsista yang sangat variatif. Algoritma ini jauh lebih relevan mencari pola non-linear dibandingkan model Regresi Logistik standar.</p>
                </div>
                <div class="bg-green-50/50 border border-green-100 p-4 rounded-lg transition-all duration-300 hover:shadow-md hover:-translate-y-1">
                    <h4 class="font-bold text-green-800 text-xs uppercase mb-1 flex items-center"><i class="ph-fill ph-lightbulb mr-1"></i> Wawasan Utama (Insight)</h4>
                    <p class="text-xs text-gray-600 leading-relaxed text-justify">Secara global, anggaran militer dunia paling masif berada di matra darat. Prediksi AI juga menyimpulkan bahwa <strong>Usia (Tahun Rilis)</strong> adalah prediktor terbaik apakah alutsista itu teruji tempur atau tidak.</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-auto">
                <!-- Radar Chart -->
                <div class="dashboard-card p-6 flex flex-col col-span-1" data-aos="zoom-in" data-aos-delay="0">
                    <h3 class="text-base font-semibold text-gray-800 mb-1">Kapabilitas & Benchmark</h3>
                    <p class="text-xs text-gray-500 mb-4">Analisis multi-variabel untuk kapabilitas alutsista global</p>
                    <div class="relative w-full flex-grow flex justify-center items-center" style="height: 320px;">
                        <canvas id="radarChart"></canvas>
                    </div>
                </div>

                <!-- Age Composition -->
                <div class="dashboard-card p-6 flex flex-col col-span-1" data-aos="zoom-in" data-aos-delay="150">
                    <h3 class="text-base font-semibold text-gray-800 mb-1">Komposisi Usia per Matra</h3>
                    <p class="text-xs text-gray-500 mb-4">Distribusi alutsista Modern (< 10 thn) vs Usang (> 30 thn)</p>
                    <div class="relative w-full flex-grow flex justify-center items-center" style="height: 320px;">
                        <canvas id="ageChart"></canvas>
                    </div>
                </div>

                <!-- ML Feature Importance -->
                <div class="dashboard-card p-6 flex flex-col col-span-1 border-t-4 border-googlePurple lg:border-t-0 lg:border-l-4" data-aos="zoom-in" data-aos-delay="300">
                    <h3 class="text-base font-semibold text-gray-800 mb-1">Variabel Paling Berpengaruh terhadap Prediksi Status Combat Proven</h3>
                    <p class="text-xs text-gray-500 mb-4">Model Machine Learning: Random Forest Classifier</p>
                    <div class="relative w-full flex-grow flex justify-center items-center" style="height: 320px;">
                        <canvas id="mlChart"></canvas>
                    </div>
                    <div class="mt-4 p-3 bg-purple-50/70 rounded-lg border border-purple-100">
                        <div id="ml-accuracy" class="text-xs font-bold text-googlePurple text-center mb-2">Loading AI Model...</div>
                        <p class="text-[10px] text-gray-500 leading-relaxed text-justify italic">
                            *Catatan Akademis: Model ini bersifat eksploratif (Pattern Recognition). Akurasi model mengindikasikan bahwa kelayakan tempur turut dipengaruhi variabel eksternal (geopolitik, strategi militer) di luar dataset. Fitur ini berfungsi sebagai insight pendukung, bukan dasar tunggal keputusan pengadaan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
  <div class="dashboard-card p-6 flex flex-col overflow-hidden w-full mt-6">
            
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Detail Sistem Persenjataan Global</h3>
                    <p class="text-xs text-gray-500 mt-1">Tabulasi data mentah berdasarkan filter aktif (Limit 100 data)</p>
                </div>
                
                <div class="w-full sm:w-72 relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i class="ph ph-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="text" id="table-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-googleBlue focus:border-googleBlue block w-full pl-10 p-2.5 outline-none transition-colors" placeholder="Cari nama, negara, matra...">
                </div>
            </div> <div class="overflow-x-auto w-full border border-gray-200 rounded-lg">
                <table class="min-w-full text-left text-sm whitespace-nowrap">
                    <thead class="uppercase tracking-wider bg-gray-50 border-b border-gray-200 text-gray-600 text-[11px] font-bold">
                        <tr>
                            <th scope="col" class="px-4 py-3">Nama Senjata</th>
                            <th scope="col" class="px-4 py-3">Kategori</th>
                            <th scope="col" class="px-4 py-3 text-center">Tahun Rilis</th>
                            <th scope="col" class="px-4 py-3">Matra</th>
                            <th scope="col" class="px-4 py-3">Pengguna Utama</th>
                            <th scope="col" class="px-4 py-3 text-right">Harga (USD)</th>
                            <th scope="col" class="px-4 py-3 text-center">Teruji Tempur</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody" class="divide-y divide-gray-200 bg-white">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500 text-sm">Memuat data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div id="pagination-container" class="w-full mt-4"></div>
            
        </div>
    </main>

    <script>
        // Konfigurasi Dasar Grafik
        Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
        Chart.defaults.color = '#6b7280'; // gray-500
        Chart.defaults.scale.grid.color = '#f3f4f6'; // gray-100
        Chart.defaults.scale.grid.borderColor = '#e5e7eb'; // gray-200
        let currentPage = 1;
        const rowsPerPage = 10;
        let searchQuery = "";
        let filteredTableData = [];
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
        let currentData = null; // Menyimpan data hasil fetch secara global untuk fitur sorting

        async function fetchDashboardData() {
            const category = document.getElementById('filter-category').value;
            const year = document.getElementById('filter-year').value;
            
            const url = `/dashboard/data?scope=global&category=${encodeURIComponent(category)}&year=${encodeURIComponent(year)}`;
            
            try {
                const response = await fetch(url);
                currentData = await response.json();
                filteredTableData = currentData.tableData || [];
                searchQuery = "";
                const searchInput = document.getElementById('table-search');
                if (searchInput) searchInput.value = "";
                currentPage = 1;
                updateKPIs(currentData.kpi);
                
                // Gambar ulang grafik dengan filter aktif
                renderCategoryBarChart();
                renderBarChart();
                renderLineChart();
                renderPieChart();
                renderScatterChart();
                renderTable();
                
                // Analitik Lanjutan
                renderAgeChart();
                renderRadarChart();
                
            } catch (error) {
                console.error('Error fetching data:', error);
            }
        }

        async function fetchMLData() {
            try {
                const response = await fetch('/data/ml_insight.json');
                const data = await response.json();
                renderMLChart(data);
            } catch (error) {
                console.log("ML Insight not found or not generated yet.", error);
                document.getElementById('ml-accuracy').innerText = "Model JSON belum digenerate.";
            }
        }

        // --- LOGIKA PENGURUTAN (SORTING LOGIC) --- //

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
            
            // Salin data untuk menghindari mutasi (perubahan) pada data asli
            let labels = [...currentData.barChart.labels];
            let datasets = JSON.parse(JSON.stringify(currentData.barChart.datasets)); 

            // Gabungkan menjadi objek agar mudah diurutkan
            let items = labels.map((label, i) => {
                let total = datasets.reduce((sum, ds) => sum + ds.data[i], 0);
                return { label, total, data: datasets.map(ds => ds.data[i]) };
            });

            // Logika Pengurutan
            if (sortOrder === 'name_asc') items.sort((a, b) => a.label.localeCompare(b.label));
            else if (sortOrder === 'name_desc') items.sort((a, b) => b.label.localeCompare(a.label));
            else if (sortOrder === 'total_desc') items.sort((a, b) => b.total - a.total);
            else if (sortOrder === 'total_asc') items.sort((a, b) => a.total - b.total);

            // Ekstrak kembali data yang sudah diurutkan
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
            
            // Logika Pengurutan
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
            
            // Logika Pengurutan
            if (sortOrder === 'total_desc') items.sort((a, b) => b.value - a.value);
            else if (sortOrder === 'total_asc') items.sort((a, b) => a.value - b.value);

            updatePieChart(items.map(it => it.label), items.map(it => it.value));
        }

        function renderScatterChart() {
            if (!currentData) return;
            // Titik koordinat scatter bersifat mutlak, tidak perlu pengurutan
            updateScatterChart(currentData.scatterChart.points);
        }

        // --- MENGGAMBAR GRAFIK (RENDER CHARTS) --- //

        function updateKPIs(kpi) {
            document.getElementById('kpi-total').innerText = kpi.total_weapons;
            document.getElementById('kpi-cost').innerText = kpi.avg_cost;
            document.getElementById('kpi-proven').innerText = kpi.combat_proven;
        }

        function updateCategoryBarChart(labels, values) {
            const ctx = document.getElementById('categoryBarChart').getContext('2d');
            if(charts.categoryBar) charts.categoryBar.destroy();

            charts.categoryBar = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Sistem Senjata',
                        data: values,
                        backgroundColor: '#4285F4', // Biru Google agar serasi dengan referensi UI
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: tooltipDefaults
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: { beginAtZero: true, border: { dash: [4, 4] } }
                    },
                    onClick: (e, activeEls) => {
                        if (activeEls.length > 0) {
                            const dataIndex = activeEls[0].index;
                            const labelClicked = charts.categoryBar.data.labels[dataIndex];
                            document.getElementById('filter-category').value = labelClicked;
                            fetchDashboardData();
                        }
                    }
                }
            });
        }

        function updateBarChart(labels, datasets) {
            const ctx = document.getElementById('barChart').getContext('2d');
            if(charts.bar) charts.bar.destroy();
            
            // Timpa warna bawaan untuk tiap kategori Matra
            datasets.forEach(ds => {
                if(ds.label === 'Land') ds.backgroundColor = '#4285F4'; // Biru Google
                if(ds.label === 'Air') ds.backgroundColor = '#FBBC05'; // Kuning Google
                if(ds.label === 'Sea') ds.backgroundColor = '#34A853'; // Hijau Google
                ds.borderWidth = 0;
            });

            charts.bar = new Chart(ctx, {
                type: 'bar',
                data: { labels, datasets },
                options: {
                    indexAxis: 'y', // Mengubah grafik batang menjadi horizontal
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
                if(l === 'Yes') return '#34A853'; // Hijau Google
                if(l === 'No') return '#EA4335';  // Merah Google
                if(l === 'Limited') return '#FBBC05'; // Kuning Google
                return '#9ca3af'; // Abu-abu
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
                                    // TAMBAHKAN KATEGORI KE DALAM ARRAY RETURN TOOLTIP
                                    return [
                                        point.name,
                                        'Kategori: ' + point.category, 
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

        // --- MENGGAMBAR GRAFIK LANJUTAN (AI & RADAR) --- //

        function renderAgeChart() {
            if (!currentData || !currentData.ageChart) return;
            const ctx = document.getElementById('ageChart').getContext('2d');
            if(charts.age) charts.age.destroy();
            
            let datasets = JSON.parse(JSON.stringify(currentData.ageChart.datasets));
            
            // Tetapkan warna berdasarkan kelompok usia
            datasets.forEach(ds => {
                if(ds.label.includes('Modern')) ds.backgroundColor = '#34A853'; // Hijau Google
                if(ds.label.includes('Menengah')) ds.backgroundColor = '#FBBC05'; // Kuning Google
                if(ds.label.includes('Usang')) ds.backgroundColor = '#EA4335'; // Merah Google
                ds.borderWidth = 0;
            });

            charts.age = new Chart(ctx, {
                type: 'bar',
                data: { labels: ['Darat', 'Udara', 'Laut'], datasets: datasets },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { tooltip: tooltipDefaults, legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } },
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: { stacked: true, border: { dash: [4, 4] } }
                    }
                }
            });
        }

        function renderRadarChart() {
            if (!currentData || !currentData.radarChart) return;
            const ctx = document.getElementById('radarChart').getContext('2d');
            if(charts.radar) charts.radar.destroy();
            
            charts.radar = new Chart(ctx, {
                type: 'radar',
                data: {
                    labels: currentData.radarChart.labels,
                    datasets: currentData.radarChart.datasets.map(ds => ({
                        ...ds,
                        backgroundColor: 'rgba(66, 133, 244, 0.2)', // Biru Google Transparan
                        borderColor: '#4285F4',
                        pointBackgroundColor: '#4285F4',
                        borderWidth: 2,
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { 
                        r: { 
                            min: 0, max: 100, 
                            ticks: { display: false },
                            grid: { color: '#e5e7eb' },
                            angleLines: { color: '#e5e7eb' }
                        } 
                    },
                    plugins: { tooltip: tooltipDefaults, legend: { display: false } }
                }
            });
        }

        function renderMLChart(mlData) {
            const ctx = document.getElementById('mlChart').getContext('2d');
            if(charts.ml) charts.ml.destroy();
            
            document.getElementById('ml-accuracy').innerHTML = `<i class="ph-fill ph-check-circle mr-1"></i>Akurasi Model Scikit-Learn: ${mlData.accuracy}%`;

            charts.ml = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: mlData.insights.map(i => i.factor),
                    datasets: [{
                        label: 'Tingkat Pengaruh (%)',
                        data: mlData.insights.map(i => i.importance_score),
                        backgroundColor: '#A142F4', // Ungu Google
                        borderRadius: 4,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    indexAxis: 'y', // Batang Horizontal
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: tooltipDefaults },
                    scales: {
                        x: { beginAtZero: true, max: 100, border: { dash: [4, 4] } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        // Inisialisasi dan pendaftaran pendengar aksi (event listeners)
        document.addEventListener('DOMContentLoaded', () => {
            // Inisialisasi Animasi AOS
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: false,
                mirror: true,
                offset: 50
            });

            fetchDashboardData();
            fetchMLData();
            
            document.getElementById('filter-category').addEventListener('change', fetchDashboardData);
            document.getElementById('filter-year').addEventListener('change', fetchDashboardData);
       document.getElementById('table-search').addEventListener('input', (e) => {
                searchQuery = e.target.value.toLowerCase();
                
                if (currentData && currentData.tableData) {
                    // Filter data berdasarkan semua kolom teks yang relevan
                    filteredTableData = currentData.tableData.filter(row => {
                        return (
                            (row.Weapon_Name && row.Weapon_Name.toLowerCase().includes(searchQuery)) ||
                            (row.Theater_of_Operation && row.Theater_of_Operation.toLowerCase().includes(searchQuery)) ||
                            (row.Country_of_Origin && row.Country_of_Origin.toLowerCase().includes(searchQuery)) ||
                            (row.Combat_Proven && row.Combat_Proven.toLowerCase().includes(searchQuery)) 
                        );
                    });
                }
                
                currentPage = 1; // Kembali ke halaman 1 saat mengetik pencarian
                renderTable();
            });
       
        });

   function renderTable() {
    if (!filteredTableData) return; 
    
    const tbody = document.getElementById('dataTableBody');
    const paginationContainer = document.getElementById('pagination-container');
    tbody.innerHTML = ''; 

    if (filteredTableData.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="px-4 py-8 text-center text-gray-500 text-sm">Tidak ada data yang sesuai dengan pencarian "${searchQuery}".</td></tr>`;
        paginationContainer.innerHTML = '';
        return;
    }

    const totalRows = filteredTableData.length;
    const startIndex = (currentPage - 1) * rowsPerPage;
    const endIndex = Math.min(startIndex + rowsPerPage, totalRows);
    const paginatedData = filteredTableData.slice(startIndex, endIndex);

    paginatedData.forEach(row => {
        let badgeClass = 'bg-gray-100 text-gray-800';
        if (row.Combat_Proven === 'Yes') badgeClass = 'bg-green-100 text-green-800';
        if (row.Combat_Proven === 'No') badgeClass = 'bg-red-100 text-red-800';
        if (row.Combat_Proven === 'Limited') badgeClass = 'bg-yellow-100 text-yellow-800';

        let priceFormatted = row.Unit_Cost_USD ? '$' + parseFloat(row.Unit_Cost_USD).toLocaleString() : 'N/A';

        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50 transition-colors";
        tr.innerHTML = `
            <td class="px-4 py-3 font-semibold text-gray-900">${row.Weapon_Name || '-'}</td>
            <td class="px-4 py-3 text-gray-600">${row.Category || '-'}</td>
            <td class="px-4 py-3 text-center text-gray-600">${row.Year_Introduced || '-'}</td> 
            
            <td class="px-4 py-3 text-gray-600">${row.Theater_of_Operation || '-'}</td>
            <td class="px-4 py-3 text-gray-600">${row.Primary_Users || '-'}</td>
            
            <td class="px-4 py-3 text-gray-600 font-mono text-right">${priceFormatted}</td>
            <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold tracking-wide uppercase ${badgeClass}">
                    ${row.Combat_Proven || 'UNKNOWN'}
                </span>
            </td>
        `;
        tbody.appendChild(tr);
    });

    renderPaginationControls(totalRows, startIndex, endIndex);
}
        function renderPaginationControls(totalRows, startIndex, endIndex) {
            const container = document.getElementById('pagination-container');
            const totalPages = Math.ceil(totalRows / rowsPerPage);

            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            container.innerHTML = `
                <div class="flex items-center justify-between border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
                    <div class="flex flex-1 justify-between sm:hidden">
                        <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">Previous</button>
                        <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50">Next</button>
                    </div>
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Menampilkan <span class="font-medium">${startIndex + 1}</span> sampai <span class="font-medium">${endIndex}</span> dari <span class="font-medium">${totalRows}</span> data
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <button onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''} class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="ph ph-caret-left"></i>
                                </button>
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">
                                    Halaman ${currentPage} dari ${totalPages}
                                </span>
                                <button onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''} class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                    <span class="sr-only">Next</span>
                                    <i class="ph ph-caret-right"></i>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            `;
        }

        function changePage(newPage) {
            // UBAH BARIS INI: Gunakan filteredTableData
            const totalPages = Math.ceil(filteredTableData.length / rowsPerPage);
            
            if (newPage >= 1 && newPage <= totalPages) {
                currentPage = newPage;
                renderTable();
            }
        }
    </script>
</body>
</html>
