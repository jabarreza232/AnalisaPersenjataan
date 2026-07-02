<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\File;

class DashboardController extends Controller
{
   public function generateMLInsight()
    {
        // 1. Definisikan path ke script Python
        $scriptPath = base_path('scripts/ml_predictive_analysis.py');

        $process = new Process(['python3.12', $scriptPath]);
        $process->setWorkingDirectory(base_path());
        $process->setTimeout(120); 
        $process->run();

        // 2. Cek apakah ada error saat script berjalan
        if (!$process->isSuccessful()) {
            // Jika error, tampilkan view dengan membawa pesan error asli Python
            return view('ml_insight_view', [
                'status' => 'failed',
                'error_message' => 'Python script crashed!',
                'python_error' => $process->getErrorOutput(),
                'ml_data' => null
            ]);
        }

        // 3. Ambil output teks dari print() di Python
        $pythonOutput = $process->getOutput();

        // 4. Cek File JSON hasil ekspor
        $jsonPath = public_path('data/ml_insight.json');
        
        if (\Illuminate\Support\Facades\File::exists($jsonPath)) {
            $jsonData = json_decode(\Illuminate\Support\Facades\File::get($jsonPath), true);
            
            // 5. SUCCESS: Return ke HTML (Blade View) dan bawa datanya
            return view('ml_insight_view', [
                'status' => 'success',
                'terminal_output' => $pythonOutput,
                'ml_data' => $jsonData,
                'error_message' => null
            ]);
        }

        // Jika script Python sukses jalan tapi file JSON tidak terbentuk
        return view('ml_insight_view', [
            'status' => 'failed',
            'error_message' => 'File JSON gagal dibuat. Pastikan script Python memiliki akses untuk menulis file.',
            'python_error' => null,
            'ml_data' => null
        ]);
    }
    /**
     * Menampilkan halaman utama dashboard.
     * Mengambil daftar kategori dan tahun untuk ditaruh di Dropdown Filter.
     */
    public function global()
    {
        // Mengambil daftar Kategori unik dari database untuk dropdown filter
        $categories = DB::table('global_weapons_systems')
                        ->select('Category')
                        ->whereNotNull('Category')
                        ->where('Category', '!=', '')
                        ->distinct()
                        ->orderBy('Category')
                        ->pluck('Category');
                        
        // Mengambil daftar Tahun unik dari database untuk dropdown filter
        $years = DB::table('global_weapons_systems')
                        ->select('Year_Introduced')
                        ->whereNotNull('Year_Introduced')
                        ->where('Year_Introduced', '!=', '')
                        ->distinct()
                        ->orderBy('Year_Introduced', 'desc')
                        ->pluck('Year_Introduced');

        return view('dashboard.global', compact('categories', 'years'));
    }

    public function indonesia()
    {
        // Mengambil daftar Kategori unik dari database untuk dropdown filter
        $categories = DB::table('global_weapons_systems')
                        ->where('Primary_Users', 'LIKE', '%Indonesia%')
                        ->select('Category')
                        ->whereNotNull('Category')
                        ->where('Category', '!=', '')
                        ->distinct()
                        ->orderBy('Category')
                        ->pluck('Category');
                        
        // Mengambil daftar Tahun unik dari database untuk dropdown filter
        $years = DB::table('global_weapons_systems')
                        ->where('Primary_Users', 'LIKE', '%Indonesia%')
                        ->select('Year_Introduced')
                        ->whereNotNull('Year_Introduced')
                        ->where('Year_Introduced', '!=', '')
                        ->distinct()
                        ->orderBy('Year_Introduced', 'desc')
                        ->pluck('Year_Introduced');

        return view('dashboard.indonesia', compact('categories', 'years'));
    }

    public function eda()
    {
        // 1. Data Cleaning Stats (Before vs After)
        $rawCount = DB::table('global_weapons_systems')->count();
        $cleanCount = DB::table('global_weapons_systems')
            ->whereNotNull('Category')->where('Category', '!=', '')
            ->whereNotNull('Unit_Cost_USD')->where('Unit_Cost_USD', '>', 0)
            ->whereNotNull('Year_Introduced')->where('Year_Introduced', '!=', '')
            ->count();
            
        $missingValues = $rawCount - $cleanCount;
        
        $totalDuplicates = DB::table('global_weapons_systems')
            ->select('Weapon_Name', DB::raw('count(*) as count'))
            ->groupBy('Weapon_Name')
            ->having('count', '>', 1)
            ->get()->count();

        // 2. Exploratory Data Analysis (Descriptive Stats)
        $avgCost = DB::table('global_weapons_systems')->where('Unit_Cost_USD', '>', 0)->avg('Unit_Cost_USD') ?? 0;
        $maxCost = DB::table('global_weapons_systems')->where('Unit_Cost_USD', '>', 0)->max('Unit_Cost_USD') ?? 0;
        $minCost = DB::table('global_weapons_systems')->where('Unit_Cost_USD', '>', 0)->min('Unit_Cost_USD') ?? 0;
        
        // Coba parsing angka dari 'Year_Introduced' buat dapet oldest/newest
        $oldestYear = DB::table('global_weapons_systems')->where('Year_Introduced', '!=', '')->min('Year_Introduced');
        $newestYear = DB::table('global_weapons_systems')->where('Year_Introduced', '!=', '')->max('Year_Introduced');

        return view('dashboard.eda', compact(
            'rawCount', 'cleanCount', 'missingValues', 'totalDuplicates',
            'avgCost', 'maxCost', 'minCost', 'oldestYear', 'newestYear'
        ));
    }

    /**
     * Endpoint API (AJAX) untuk mengambil data grafik dan KPI berdasarkan filter.
     * Tidak over-engineered, murni menggunakan Query Builder sederhana (sama seperti query SQL di paper).
     */
    public function getData(Request $request)
    {
        // Memulai query dasar ke tabel persenjataan
        $query = DB::table('global_weapons_systems');
        
        // Cek scope untuk filter data Indonesia saja
        if ($request->scope == 'indonesia') {
            $query->where('Primary_Users', 'LIKE', '%Indonesia%');
        }

        // FITUR FILTER: Jika user memilih kategori, tambahkan kondisi WHERE
        if ($request->has('category') && $request->category != '') {
            $query->where('Category', $request->category);
        }
        
        // FITUR FILTER: Jika user memilih tahun, tambahkan kondisi WHERE
        if ($request->has('year') && $request->year != '') {
            $query->where('Year_Introduced', $request->year);
        }

        // ==========================================
        // 1. MENGAMBIL DATA UNTUK SCORECARD (KPI)
        // ==========================================
        $totalWeapons = $query->count(); // Total seluruh sistem
        $avgCost = $query->avg('Unit_Cost_USD') ?? 0; // Rata-rata harga
        $totalCombatProven = (clone $query)->where('Combat_Proven', 'Yes')->count(); // Total yang teruji tempur

        // ==========================================
        // 1.5. DATA UNTUK SINGLE BAR CHART (Distribusi Kategori)
        // ==========================================
        $categoryBarData = (clone $query)
            ->select('Category', DB::raw('count(*) as total'))
            ->whereNotNull('Category')
            ->where('Category', '!=', '')
            ->groupBy('Category')
            ->get();
            
        $categoryBarLabels = $categoryBarData->pluck('Category');
        $categoryBarValues = $categoryBarData->pluck('total');

        // ==========================================
        // 2. DATA UNTUK BAR CHART (Penggunaan Berdasarkan Matra)
        // ==========================================
        $barData = (clone $query)
            ->select('Category', 'Theater_of_Operation', DB::raw('count(*) as total'))
            ->whereNotNull('Theater_of_Operation')
            ->where('Theater_of_Operation', '!=', '')
            ->whereNotNull('Category')
            ->groupBy('Category', 'Theater_of_Operation')
            ->get();
            
        // Memformat data agar mudah dibaca oleh Chart.js (Stacked format)
        $categoriesLabel = $barData->pluck('Category')->unique()->values();
        $theaters = ['Land', 'Air', 'Sea'];
        $barDatasets = [];
        
        foreach ($theaters as $theater) {
            $dataPoints = [];
            foreach ($categoriesLabel as $cat) {
                // Mencari total per kategori dan matra
                $match = $barData->where('Category', $cat)->where('Theater_of_Operation', $theater)->first();
                $dataPoints[] = $match ? $match->total : 0;
            }
            $barDatasets[] = [
                'label' => $theater,
                'data' => $dataPoints
            ];
        }

        // ==========================================
        // 3. DATA UNTUK LINE CHART (Tren Pengadaan Tahun)
        // ==========================================
        $lineDataRaw = (clone $query)
            ->select('Year_Introduced', DB::raw('count(*) as total'))
            ->whereNotNull('Year_Introduced')
            ->where('Year_Introduced', '!=', '')
            // Filter out non-numeric years if any, and order chronologically
            ->orderBy('Year_Introduced', 'ASC')
            ->groupBy('Year_Introduced')
            ->get();
            
        $lineLabels = $lineDataRaw->pluck('Year_Introduced');
        $lineValues = $lineDataRaw->pluck('total');

        // ==========================================
        // 4. DATA UNTUK PIE CHART (Persentase Teruji Tempur)
        // ==========================================
        $pieDataRaw = (clone $query)
            ->select('Combat_Proven', DB::raw('count(*) as total'))
            ->whereNotNull('Combat_Proven')
            ->where('Combat_Proven', '!=', '')
            ->groupBy('Combat_Proven')
            ->get();
            
        $pieLabels = $pieDataRaw->pluck('Combat_Proven');
        $pieValues = $pieDataRaw->pluck('total');

        // ==========================================
        // 5. DATA UNTUK SCATTER PLOT (Value for Money)
        // ==========================================
 $scatterDataRaw = (clone $query)
            // TAMBAHKAN 'Category' PADA SELECT DI BAWAH INI
            ->select('Weapon_Name', 'Unit_Cost_USD', 'Num_Operator_Nations', 'Category') 
            ->whereNotNull('Unit_Cost_USD')
            ->where('Unit_Cost_USD', '>', 0)
            ->whereNotNull('Num_Operator_Nations')
            ->get();
            
        // Memformat menjadi array titik koordinat (x = Harga, y = Negara Pengguna)
        $scatterPoints = $scatterDataRaw->map(function($item) {
            return [
                'x' => (float) $item->Unit_Cost_USD,
                'y' => (float) $item->Num_Operator_Nations,
                'name' => $item->Weapon_Name,
                'category' => $item->Category // TAMBAHKAN DATA KATEGORI DI SINI
            ];
        });
// ==========================================
        // 6. DATA UNTUK TABEL DETAIL
        // ==========================================
        $tableData = (clone $query)
            ->select(
                'Weapon_Name', 
                'Category', 
                'Combat_Proven', 
                'Primary_Users', // Kolom Rekomendasi
                'Unit_Cost_USD',     // Kolom Rekomendasi
                'Theater_of_Operation', // Kolom Rekomendasi
                'Year_Introduced'
            )
            ->orderBy('Weapon_Name', 'asc')
            
            ->get();

        // ==========================================
        // 6. DATA UNTUK AGE COMPOSITION (Stacked Bar Chart)
        // ==========================================
        $ageDataRaw = (clone $query)
            ->select('Theater_of_Operation', 'Year_Introduced')
            ->whereNotNull('Theater_of_Operation')
            ->where('Theater_of_Operation', '!=', '')
            ->whereNotNull('Year_Introduced')
            ->where('Year_Introduced', '!=', '')
            ->get();
            
        $ageCategories = ['Modern (< 10 thn)', 'Menengah (10-30 thn)', 'Usang (> 30 thn)'];
        $theatersAge = ['Land', 'Air', 'Sea'];
        $currentYear = date('Y');
        
        $ageDatasets = [];
        foreach ($ageCategories as $cat) {
            $ageDatasets[$cat] = ['Land' => 0, 'Air' => 0, 'Sea' => 0];
        }
        
        $totalModern = 0;
        foreach ($ageDataRaw as $item) {
            $age = $currentYear - (int)$item->Year_Introduced;
            $theater = $item->Theater_of_Operation;
            if (!in_array($theater, $theatersAge)) continue;
            
            if ($age < 10) {
                $ageDatasets['Modern (< 10 thn)'][$theater]++;
                $totalModern++;
            } elseif ($age <= 30) {
                $ageDatasets['Menengah (10-30 thn)'][$theater]++;
            } else {
                $ageDatasets['Usang (> 30 thn)'][$theater]++;
            }
        }
        
        $ageChartDatasets = [];
        foreach ($ageCategories as $cat) {
            $ageChartDatasets[] = [
                'label' => $cat,
                'data' => [ $ageDatasets[$cat]['Land'], $ageDatasets[$cat]['Air'], $ageDatasets[$cat]['Sea'] ]
            ];
        }

        // ==========================================
        // 7. DATA UNTUK RADAR CHART (Capability Benchmark)
        // ==========================================
        $percModern = $totalWeapons > 0 ? round(($totalModern / $totalWeapons) * 100) : 0;
        $percProven = $totalWeapons > 0 ? round(($totalCombatProven / $totalWeapons) * 100) : 0;
        
        $radarLabels = ['Daya Gempur (Volume)', 'Teruji Tempur (%)', 'Efisiensi Anggaran (Skala)', 'Modernisasi (%)'];
        $radarDatasets = [
            [
                'label' => 'Skor Kapabilitas',
                'data' => [100, $percProven, 75, $percModern]
            ]
        ];

        // Mengirimkan semua data kembali ke tampilan Frontend (Blade) dalam format JSON
        return response()->json([
            'kpi' => [
                'total_weapons' => number_format($totalWeapons),
                'avg_cost' => '$' . number_format($avgCost, 2),
                'combat_proven' => number_format($totalCombatProven)
            ],
            'categoryBarChart' => [
                'labels' => $categoryBarLabels,
                'values' => $categoryBarValues
            ],
            'barChart' => [
                'labels' => $categoriesLabel,
                'datasets' => $barDatasets
            ],
            'lineChart' => [
                'labels' => $lineLabels,
                'values' => $lineValues
            ],
            'pieChart' => [
                'labels' => $pieLabels,
                'values' => $pieValues
            ],
            'scatterChart' => [
                'points' => $scatterPoints
            ],
            'tableData' => $tableData,
            'ageChart' => [
                'datasets' => $ageChartDatasets
            ],
            'radarChart' => [
                'labels' => $radarLabels,
                'datasets' => $radarDatasets
            ]
        ]);
    }
}

