<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Analitik Alutsista</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        military: {
                            800: '#1e293b', // Dark Slate
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">

    <div class="flex h-screen overflow-hidden">
        
        <aside class="w-64 bg-military-900 text-white flex flex-col shadow-xl hidden md:flex">
            <div class="p-6 text-center border-b border-gray-700">
                <h1 class="text-xl font-bold tracking-wider uppercase text-blue-400">
                    <i class="fa-solid fa-shield-halved mr-2"></i> Kemenhan RI
                </h1>
                <p class="text-xs text-gray-400 mt-1">Sistem Analisa Persenjataan</p>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2">
                <a href="#" class="flex items-center px-4 py-3 bg-blue-600 rounded-lg text-white font-medium transition-colors">
                    <i class="fa-solid fa-chart-pie w-6"></i> Dashboard
                </a>
                <a href="#" class="flex items-center px-4 py-3 hover:bg-gray-800 rounded-lg text-gray-300 transition-colors">
                    <i class="fa-solid fa-database w-6"></i> Data Inventaris
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col h-screen overflow-y-auto">
            
            <header class="bg-white shadow-sm py-4 px-8 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Ringkasan Eksekutif</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500"><i class="fa-regular fa-clock mr-1"></i> Update Terakhir: Hari ini</span>
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold">
                        A
                    </div>
                </div>
            </header>

            <main class="p-8 space-y-6">
                
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Matra / Kategori Sistem</label>
                        <select id="filter-category" onchange="updateDashboard()" class="w-full border-gray-300 rounded-lg bg-gray-50 px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            <option value="">-- Semua Kategori Utama --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-600 mb-2">Tahun Mulai Operasi</label>
                        <input type="number" id="filter-year" placeholder="Cth: 1990" oninput="updateDashboard()" class="w-full border-gray-300 rounded-lg bg-gray-50 px-4 py-2 text-gray-700 focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="w-full md:w-auto">
                        <button onclick="resetFilter()" class="w-full px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium transition-colors">
                            <i class="fa-solid fa-rotate-right mr-2"></i> Reset
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                        <div class="p-4 bg-blue-50 rounded-lg text-blue-600 mr-4">
                            <i class="fa-solid fa-crosshairs text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Unit Persenjataan</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1" id="kpi-total">0</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                        <div class="p-4 bg-emerald-50 rounded-lg text-emerald-600 mr-4">
                            <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Rata-rata Biaya (USD)</p>
                            <h3 class="text-2xl font-bold text-gray-800 mt-1" id="kpi-avg-cost">$0</h3>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center">
                        <div class="p-4 bg-rose-50 rounded-lg text-rose-600 mr-4">
                            <i class="fa-solid fa-fire-flame-curved text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status Teruji Tempur</p>
                            <h3 class="text-3xl font-bold text-gray-800 mt-1" id="kpi-combat">0</h3>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Distribusi Persenjataan Berdasarkan Kategori</h3>
                            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded">Klik batang grafik untuk filter otomatis</span>
                        </div>
                        <div class="relative h-80 w-full">
                            <canvas id="barChart"></canvas>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <script>
        let myChart = null;

        // Inisialisasi default Chart.js agar lebih elegan
        Chart.defaults.font.family = "'Inter', 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif";
        Chart.defaults.color = '#6b7280';

        async function updateDashboard(clickedCategory = null) {
            let category = clickedCategory || document.getElementById('filter-category').value;
            let year = document.getElementById('filter-year').value;

            // Fetch ke API Laravel
            let response = await fetch(`/dashboard/data?category=${category}&year=${year}`);
            let data = await response.json();

            // Animasi angka (opsional sederhana)
            document.getElementById('kpi-total').innerText = data.kpi.total_weapons;
            document.getElementById('kpi-avg-cost').innerText = data.kpi.avg_cost;
            document.getElementById('kpi-combat').innerText = data.kpi.combat_proven;

            renderChart(data.chart.labels, data.chart.values);
        }

        function resetFilter() {
            document.getElementById('filter-category').value = '';
            document.getElementById('filter-year').value = '';
            updateDashboard();
        }

        function renderChart(labels, values) {
            const ctx = document.getElementById('barChart').getContext('2d');
            
            if(myChart != null) {
                myChart.destroy();
            }

            // Membuat gradient untuk warna batang grafik
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)'); // Blue-500
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0.2)'); // Blue-600 transparent

            myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Sistem Senjata',
                        data: values,
                        backgroundColor: gradient,
                        borderColor: 'rgba(37, 99, 235, 1)',
                        borderWidth: 1,
                        borderRadius: 6, // Ujung batang melengkung
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }, // Sembunyikan legend karena sudah jelas
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.9)',
                            padding: 12,
                            titleFont: { size: 14 },
                            bodyFont: { size: 13 }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f3f4f6' }
                        },
                        x: {
                            grid: { display: false }
                        }
                    },
                    onClick: (e, activeEls) => {
                        if (activeEls.length > 0) {
                            const dataIndex = activeEls[0].index;
                            const labelClicked = myChart.data.labels[dataIndex];
                            
                            document.getElementById('filter-category').value = labelClicked;
                            updateDashboard(labelClicked);
                        }
                    }
                }
            });
        }

        window.onload = function() {
            updateDashboard();
        };
    </script>

</body>
</html>