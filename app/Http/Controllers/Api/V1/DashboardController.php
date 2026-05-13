<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\PurchaseOrder;
use App\Models\PurchaseRequisition;
use App\Models\Tender;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        $prPendingCount = PurchaseRequisition::where('status', 'pending')->count();
        $tenderOpenCount = Tender::where('status', 'open')->count();
        $poCompletedCount = PurchaseOrder::where('status', 'completed')->count();

        $monthlyExpenditure = PurchaseOrder::whereMonth('created_at', $currentMonth)
                                            ->whereYear('created_at', $currentYear)
                                            ->sum('actual_total_cost');

        $budgets = Budget::where('fiscal_year', $currentYear)->get();
        $totalBudget = $budgets->sum('total_amount');
        $totalUsed = $budgets->sum('used_amount');
        $totalReserved = $budgets->sum('reserved_amount');
        $availableBudget = $totalBudget - ($totalUsed + $totalReserved);

        $recentActivities = PurchaseOrder::with('vendor')
        ->latest()
        ->limit(5)
        ->get()
        ->map(function ($po) {
            return [
                'id' => $po->po_number,
                'description' => $po->notes ?? 'No description',
                'vendor' => $po->vendor->name,
                'status' => $po->status,
                'total' => $po->actual_total_cost
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data statistik dashboard berhasil diambil.',
            'data' => [
                'widgets' => [
                    'pr_pending'     => $prPendingCount,
                    'tenders_open'   => $tenderOpenCount,
                    'po_completed'   => $poCompletedCount,
                ],
                'finance' => [
                    'monthly_expenditure' => $monthlyExpenditure,
                    'yearly_budget' => [
                        'total'     => $totalBudget,
                        'used'      => $totalUsed,
                        'reserved'  => $totalReserved,
                        'available' => $availableBudget,
                    ]
                ],
                'recent_activities' => $recentActivities
            ]
        ]);
    }
}
