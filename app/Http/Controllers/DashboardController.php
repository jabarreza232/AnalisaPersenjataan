<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
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
            ->orderByRaw('CAST(Year_Introduced AS INTEGER) ASC')
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
            ->select('Weapon_Name', 'Unit_Cost_USD', 'Num_Operator_Nations')
            ->whereNotNull('Unit_Cost_USD')
            ->where('Unit_Cost_USD', '>', 0)
            ->whereNotNull('Num_Operator_Nations')
            ->get();
            
        // Memformat menjadi array titik koordinat (x = Harga, y = Negara Pengguna)
        $scatterPoints = $scatterDataRaw->map(function($item) {
            return [
                'x' => (float) $item->Unit_Cost_USD,
                'y' => (float) $item->Num_Operator_Nations,
                'name' => $item->Weapon_Name
            ];
        });

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
            ]
        ]);
    }
}
