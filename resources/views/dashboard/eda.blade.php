<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exploratory Data Analysis (EDA) & Data Cleaning</title>
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
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
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

    <!-- Top Navigation / Header -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-center w-full shadow-sm sticky top-0 z-50">
        <div>
            <h1 class="text-xl font-bold text-gray-900 tracking-tight">Laporan Analisis Sistem Persenjataan Indonesia</h1>
            <p class="text-sm text-gray-500 mt-0.5">Tugas Besar Analitik dan Visualisasi Data</p>
        </div>
    </header>

    <!-- Tab Navigation -->
    <div class="bg-white border-b border-gray-200 px-6 w-full flex items-center space-x-6 text-sm font-medium overflow-x-auto">
        <a href="/dashboard/global" class="text-gray-500 hover:text-gray-700 py-3 border-b-2 border-transparent transition-colors whitespace-nowrap">Global Analytics</a>
        <a href="/dashboard/indonesia" class="text-gray-500 hover:text-gray-700 py-3 border-b-2 border-transparent transition-colors whitespace-nowrap">Indonesia Analytics</a>
        <a href="/dashboard/eda" class="text-googleBlue py-3 border-b-2 border-googleBlue whitespace-nowrap">EDA & Data Cleaning</a>
    </div>

    <!-- Main Content -->
    <main class="w-full px-4 sm:px-6 lg:px-8 py-8 flex-grow flex flex-col space-y-8">
        
        <!-- SECTION 1: DATA CLEANING -->
        <div data-aos="fade-up">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center text-googleRed text-xl">
                    <i class="ph-fill ph-broom"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">1. Data Cleaning (Pembersihan Data)</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Case A -->
                <div class="dashboard-card p-6 border-t-4 border-t-googleRed">
                    <h3 class="text-base font-bold text-gray-800 mb-2">A. Penanganan Missing Data (Year_Retired)</h3>
                    <p class="text-sm text-gray-600 mb-4 text-justify">
                        Kolom <code>Year_Retired</code> bernilai <strong>NaN</strong> untuk ~92% catatan karena sebagian besar sistem masih aktif. Solusi yang digunakan yaitu mengubah status NaN menjadi <strong>“Masih Aktif”</strong> agar data dapat divisualisasikan tanpa error.
                    </p>
                    <div class="bg-gray-50 p-3 rounded border border-gray-200 mt-2">
                        <span class="text-xs font-bold text-gray-500 uppercase">Before Cleaning:</span>
                        <div class="text-sm font-mono text-gray-700 mt-1">Year_Retired: NULL</div>
                    </div>
                    <div class="bg-green-50 p-3 rounded border border-green-200 mt-2">
                        <span class="text-xs font-bold text-googleGreen uppercase">After Cleaning:</span>
                        <div class="text-sm font-mono text-gray-700 mt-1">Year_Retired: "Masih Aktif"</div>
                    </div>
                </div>
                
                <!-- Case B -->
                <div class="dashboard-card p-6 border-t-4 border-t-googleBlue">
                    <h3 class="text-base font-bold text-gray-800 mb-2">B. Normalisasi Kolom Spesifik Kategori</h3>
                    <p class="text-sm text-gray-600 mb-4 text-justify">
                        Beberapa kolom sengaja dibuat kosong (Misal: <code>Barrel_Length_mm</code> hanya untuk senjata api, senjata mesin, serta meriam pada tank, dan <code>Max_Speed_kmh</code> untuk kendaraan/rudal). Data diseragamkan menggunakan <code>CASE WHEN</code> menjadi "Data Belum Ada" atau "Tidak Berlaku".
                    </p>
                    <div class="bg-gray-50 p-3 rounded border border-gray-200 mt-2">
                        <span class="text-xs font-bold text-gray-500 uppercase">Before Cleaning:</span>
                        <div class="text-sm font-mono text-gray-700 mt-1">Barrel_Length_mm: NULL (Pada Senjata Rudal, Radar, Kendaraan Taktis)</div>
                    </div>
                    <div class="bg-green-50 p-3 rounded border border-green-200 mt-2">
                        <span class="text-xs font-bold text-googleGreen uppercase">After Cleaning:</span>
                        <div class="text-sm font-mono text-gray-700 mt-1">Panjang_Laras: "Tidak Berlaku"</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECTION 2: EDA -->
        <div data-aos="fade-up" data-aos-delay="100" class="pt-6 border-t border-gray-200">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-googleBlue text-xl">
                    <i class="ph-fill ph-chart-bar"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">2. Exploratory Data Analysis (EDA) & Eksekusi Query</h2>
            </div>
            
            <div class="grid grid-cols-1 gap-6">
                
                <!-- Tahap 1 -->
                <div class="dashboard-card p-6">
                    <h3 class="text-base font-bold text-gray-800">Tahap 1: Analisis Univariat (Distribusi Matra)</h3>
                    <p class="text-sm text-gray-600 mt-1 mb-3 text-justify">
                        <strong>Tujuan:</strong> Mengetahui fokus pertahanan saat ini (darat, laut, atau udara). <br>
                        <strong>Hasil:</strong> Fokus pertahanan saat ini yang pertama adalah angkatan darat (Land), disusul udara, dan terakhir laut. <br>
                        <em class="text-xs text-googleBlue">*Telah diimplementasikan pada grafik "Penggunaan Berdasarkan Matra" (Stacked Bar) di Dashboard.</em>
                    </p>
                    <div class="bg-gray-900 rounded p-4 overflow-x-auto">
<pre class="text-[13px] text-gray-300 font-mono leading-relaxed">
<span class="text-purple-400">SELECT</span> Theater_of_Operation, <span class="text-blue-400">COUNT</span>(*) <span class="text-purple-400">AS</span> Total_Sistem, 
<span class="text-blue-400">ROUND</span>((<span class="text-blue-400">COUNT</span>(*) * <span class="text-yellow-300">100.0</span>) / (<span class="text-purple-400">SELECT</span> <span class="text-blue-400">COUNT</span>(*) <span class="text-purple-400">FROM</span> global_weapons_systems <span class="text-purple-400">WHERE</span> Primary_Users <span class="text-purple-400">LIKE</span> <span class="text-green-300">'%Indonesia%'</span>), <span class="text-yellow-300">2</span>) <span class="text-purple-400">AS</span> Persentase 
<span class="text-purple-400">FROM</span> global_weapons_systems 
<span class="text-purple-400">WHERE</span> Primary_Users <span class="text-purple-400">LIKE</span> <span class="text-green-300">'%Indonesia%'</span>
<span class="text-purple-400">GROUP BY</span> Theater_of_Operation 
<span class="text-purple-400">ORDER BY</span> Total_Sistem <span class="text-purple-400">DESC</span>;
</pre>
                    </div>
                </div>

                <!-- Tahap 2A -->
                <div class="dashboard-card p-6">
                    <h3 class="text-base font-bold text-gray-800">Tahap 2A: Analisis Bivariat (Efisiensi Anggaran)</h3>
                    <p class="text-sm text-gray-600 mt-1 mb-3 text-justify">
                        <strong>Tujuan:</strong> Kategori apa yang paling menyerap anggaran negara.<br>
                        <strong>Hasil:</strong> Kategori senjata naval (laut) memakan paling banyak anggaran disusul senjata udara (Aircraft/UAS).<br>
                        <em class="text-xs text-googleBlue">*Telah diimplementasikan pada perhitungan "Rata-rata Biaya Pengadaan" (KPI Scorecard).</em>
                    </p>
                    <div class="bg-gray-900 rounded p-4 overflow-x-auto">
<pre class="text-[13px] text-gray-300 font-mono leading-relaxed">
<span class="text-purple-400">SELECT</span> Category, <span class="text-blue-400">COUNT</span>(*) <span class="text-purple-400">AS</span> Jumlah_Sistem, 
<span class="text-blue-400">AVG</span>(Unit_Cost_USD) <span class="text-purple-400">AS</span> Rata_Rata_Harga 
<span class="text-purple-400">FROM</span> global_weapons_systems 
<span class="text-purple-400">WHERE</span> Unit_Cost_USD <span class="text-purple-400">IS NOT NULL</span> <span class="text-purple-400">AND</span> Primary_Users <span class="text-purple-400">LIKE</span> <span class="text-green-300">'%Indonesia%'</span>
<span class="text-purple-400">GROUP BY</span> Category 
<span class="text-purple-400">ORDER BY</span> <span class="text-blue-400">AVG</span>(Unit_Cost_USD) <span class="text-purple-400">DESC</span>;
</pre>
                    </div>
                </div>
                
                <!-- Tahap 2B -->
                <div class="dashboard-card p-6">
                    <h3 class="text-base font-bold text-gray-800">Tahap 2B: Korelasi Interoperabilitas NATO</h3>
                    <p class="text-sm text-gray-600 mt-1 mb-3 text-justify">
                        <strong>Tujuan:</strong> Mengetahui kesiapan interoperabilitas TNI dalam operasi gabungan internasional.<br>
                        <strong>Hasil:</strong> Terdapat 22 jenis persenjataan standar NATO dan 28 Non-NATO. Pertahanan kita masih banyak mengandalkan sistem independen/blok timur.
                    </p>
                    <div class="bg-gray-900 rounded p-4 overflow-x-auto">
<pre class="text-[13px] text-gray-300 font-mono leading-relaxed">
<span class="text-purple-400">SELECT</span> Weapon_Name, 
<span class="text-blue-400">SUM</span>(<span class="text-purple-400">CASE WHEN</span> NATO_Compatible = <span class="text-green-300">'Yes'</span> <span class="text-purple-400">THEN</span> <span class="text-yellow-300">1</span> <span class="text-purple-400">ELSE</span> <span class="text-yellow-300">0</span> <span class="text-purple-400">END</span>) <span class="text-purple-400">AS</span> Standar_NATO, 
<span class="text-blue-400">SUM</span>(<span class="text-purple-400">CASE WHEN</span> NATO_Compatible = <span class="text-green-300">'No'</span> <span class="text-purple-400">THEN</span> <span class="text-yellow-300">1</span> <span class="text-purple-400">ELSE</span> <span class="text-yellow-300">0</span> <span class="text-purple-400">END</span>) <span class="text-purple-400">AS</span> Non_NATO 
<span class="text-purple-400">FROM</span> global_weapons_systems 
<span class="text-purple-400">WHERE</span> Primary_Users <span class="text-purple-400">LIKE</span> <span class="text-green-300">'%Indonesia%'</span>
<span class="text-purple-400">GROUP BY</span> Weapon_Name;
</pre>
                    </div>
                </div>

                <!-- Tahap 3 -->
                <div class="dashboard-card p-6 border-l-4 border-l-googleYellow">
                    <h3 class="text-base font-bold text-gray-800">Tahap 3: Analisis Anomali & Rekomendasi (Risiko Modernisasi)</h3>
                    <p class="text-sm text-gray-600 mt-1 mb-3 text-justify">
                        <strong>Tujuan:</strong> Mengidentifikasi alutsista tua (>30 tahun) yang masih aktif (Year_Retired IS NULL) untuk prioritas peremajaan.<br>
                        <strong>Hasil:</strong> Ditemukan senjata paling usang (SOM-M3) beroperasi hingga 80 tahun.<br>
                        <em class="text-xs text-googleYellow font-bold">*Telah divisualisasikan sepenuhnya pada grafik "Komposisi Usia per Matra" (Kategori: Usang) di Dashboard.</em>
                    </p>
                    <div class="bg-gray-900 rounded p-4 overflow-x-auto">
<pre class="text-[13px] text-gray-300 font-mono leading-relaxed">
<span class="text-purple-400">SELECT</span> Weapon_Name, Category, Country_of_Origin, Year_Introduced, 
(<span class="text-yellow-300">2026</span> - <span class="text-blue-400">CAST</span>(Year_Introduced <span class="text-purple-400">AS UNSIGNED</span>)) <span class="text-purple-400">AS</span> Usia_Beroperasi 
<span class="text-purple-400">FROM</span> global_weapons_systems 
<span class="text-purple-400">WHERE</span> (Year_Retired <span class="text-purple-400">IS NULL OR</span> Year_Retired = <span class="text-green-300">''</span>) 
  <span class="text-purple-400">AND</span> (<span class="text-yellow-300">2026</span> - <span class="text-blue-400">CAST</span>(Year_Introduced <span class="text-purple-400">AS UNSIGNED</span>)) > <span class="text-yellow-300">30</span> 
  <span class="text-purple-400">AND</span> Primary_Users <span class="text-purple-400">LIKE</span> <span class="text-green-300">'%Indonesia%'</span>
<span class="text-purple-400">ORDER BY</span> Usia_Beroperasi <span class="text-purple-400">DESC</span>;
</pre>
                    </div>
                </div>

            </div>
        </div>

        <!-- SECTION 3: CODE SNIPPETS -->
        <div data-aos="fade-up" data-aos-delay="200" class="pt-6 border-t border-gray-200">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-googlePurple text-xl">
                    <i class="ph-fill ph-code"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">3. Implementasi Kode (Code Snippets)</h2>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- PHP Snippet -->
                <div class="dashboard-card overflow-hidden flex flex-col">
                    <div class="bg-gray-800 px-4 py-3 flex items-center justify-between border-b border-gray-700">
                        <span class="text-xs font-bold text-gray-200 flex items-center gap-2"><i class="ph-fill ph-file-code text-lg text-blue-400"></i> Backend (Data Cleaning)</span>
                        <span class="text-[10px] bg-gray-700 text-gray-300 px-2 py-1 rounded border border-gray-600">DashboardController.php</span>
                    </div>
                    <pre class="flex-grow p-5 text-[13px] text-gray-300 bg-gray-900 overflow-x-auto font-mono leading-relaxed">
<span class="text-purple-400">public function</span> <span class="text-blue-400">getData</span>()
{
    <span class="text-green-400">// Membersihkan Nilai Kosong (Missing Values) & Outlier</span>
    <span class="text-yellow-300">$scatterDataRaw</span> = (clone <span class="text-yellow-300">$query</span>)
        ->select(<span class="text-green-300">'Weapon_Name'</span>, <span class="text-green-300">'Unit_Cost_USD'</span>)
        ->whereNotNull(<span class="text-green-300">'Unit_Cost_USD'</span>)
        ->where(<span class="text-green-300">'Unit_Cost_USD'</span>, <span class="text-green-300">'>'</span>, <span class="text-purple-300">0</span>) <span class="text-green-400">// Hapus Harga Negatif/Nol</span>
        ->get();

    <span class="text-green-400">// Filter Data Kosong pada Kategori</span>
    <span class="text-yellow-300">$categoryBarData</span> = (clone <span class="text-yellow-300">$query</span>)
        ->whereNotNull(<span class="text-green-300">'Category'</span>)
        ->where(<span class="text-green-300">'Category'</span>, <span class="text-green-300">'!='</span>, <span class="text-green-300">''</span>)
        ->groupBy(<span class="text-green-300">'Category'</span>)
        ->get();
}</pre>
                </div>

                <!-- Python Snippet -->
                <div class="dashboard-card overflow-hidden flex flex-col">
                    <div class="bg-gray-800 px-4 py-3 flex items-center justify-between border-b border-gray-700">
                        <span class="text-xs font-bold text-gray-200 flex items-center gap-2"><i class="ph-fill ph-file-python text-lg text-yellow-400"></i> Data Science (Machine Learning)</span>
                        <span class="text-[10px] bg-gray-700 text-gray-300 px-2 py-1 rounded border border-gray-600">ml_predictive_analysis.py</span>
                    </div>
                    <pre class="flex-grow p-5 text-[13px] text-gray-300 bg-gray-900 overflow-x-auto font-mono leading-relaxed">
<span class="text-purple-400">import</span> pandas <span class="text-purple-400">as</span> pd
<span class="text-purple-400">from</span> sklearn.ensemble <span class="text-purple-400">import</span> RandomForestClassifier

<span class="text-green-400"># 1. Membersihkan Missing Value dengan Pandas</span>
df_clean = df[features + [target]].dropna()
df_clean[<span class="text-green-300">'Unit_Cost_USD'</span>] = pd.to_numeric(
    df_clean[<span class="text-green-300">'Unit_Cost_USD'</span>], errors=<span class="text-green-300">'coerce'</span>
)

<span class="text-green-400"># 2. Melatih Model (Random Forest Classifier)</span>
model = RandomForestClassifier(random_state=<span class="text-purple-300">42</span>, n_estimators=<span class="text-purple-300">100</span>)
model.fit(X_train, y_train)

<span class="text-green-400"># 3. Mengekstrak Feature Importance (Tingkat Pengaruh)</span>
importances = model.feature_importances_
</pre>
                </div>
            </div>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            AOS.init({
                duration: 800,
                easing: 'ease-out-cubic',
                once: false, // Animasi reappear saat di-scroll naik-turun
                mirror: true, // Elemen akan teranimasi keluar saat melewati viewport
                offset: 50
            });
        });
    </script>
</body>
</html>
