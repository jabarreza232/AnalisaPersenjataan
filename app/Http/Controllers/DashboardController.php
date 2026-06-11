<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WeaponSystems;
class DashboardController extends Controller
{
public function index()
    {
        // Mengambil daftar kategori unik untuk dropdown filter
        $categories = WeaponSystems::select('Category')->distinct()->pluck('Category');
        return view('dashboard.index', compact('categories'));
    }

    public function getData(Request $request)
    {
        $query = WeaponSystems::query();

        // 1. FITUR FILTER (SLICER) DARI REQUEST AJAX
        if ($request->has('category') && $request->category != '') {
            $query->where('Category', $request->category);
        }
        
        if ($request->has('year') && $request->year != '') {
            $query->where('Year_Introduced', '>=', $request->year);
        }

        // 2. MENYIAPKAN RINGKASAN DATA (KPI)
        $totalWeapons = $query->count();
        $avgCost = $query->avg('Unit_Cost_USD') ?? 0;
        $totalCombatProven = (clone $query)->where('Combat_Proven', 'Yes')->count();

        // 3. MENYIAPKAN DATA UNTUK GRAFIK (Contoh: Bar Chart Matra)
        $chartDataRaw = (clone $query)
            ->selectRaw('Category, count(*) as total')
            ->groupBy('Category')
            ->get();
            
        $chartLabels = $chartDataRaw->pluck('Category');
        $chartValues = $chartDataRaw->pluck('total');

        // Mengembalikan response dalam bentuk JSON
        return response()->json([
            'kpi' => [
                'total_weapons' => $totalWeapons,
                'avg_cost' => '$' . number_format($avgCost, 2),
                'combat_proven' => $totalCombatProven
            ],
            'chart' => [
                'labels' => $chartLabels,
                'values' => $chartValues
            ]
        ]);
    }
}
