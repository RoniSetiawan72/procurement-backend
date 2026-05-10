<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $query = Budget::with('department');

        if ($request->filled('fiscal_year')) {
            $query->where('fiscal_year', $request->fiscal_year);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->whereHas('department', function ($q) use ($searchTerm) {
                $q->where('name', 'ilike', '%' . $searchTerm . '%')
                ->orWhere('code', 'ilike', '%' . $searchTerm . '%');
            });
        }

        $query->orderBy('fiscal_year', 'desc');

        $perPage = $request->input('per_page', 10);
        $budgets = $query->paginate($perPage);

        return BudgetResource::collection($budgets);
    }

    public function store(StoreBudgetRequest $request)
    {
        $exists = Budget::where('department_id', $request->department_id)
                        ->where('fiscal_year', $request->fiscal_year)
                        ->exists();

        if ($exists) {
            return response()->json([
                'success'   => false,
                'message'   => 'Anggaran untuk departmen ini pada tahun tersebut sudah ada.'
            ], 422);
        }

        $budget = Budget::create($request->validated());
        return new BudgetResource($budget);
    }

    public function update(UpdateBudgetRequest $request, Budget $budget)
    {
        $mainAllowed = $budget->used_amount + $budget->reserved_amount;

        if ($request->total_amount < $mainAllowed) {
            return response()->json([
                'success'   => false,
                'message'   => "Total pagu tidak boleh kurang dari dana yang sudah berjalan (Minimal: Rp {$mainAllowed})"
            ], 422);
        }

        $budget->update(['total_amount' => $request->total_amount]);

        return response()->json([
            'success'   => true,
            'message'   => 'Total anggaran berhasil disesuaikan',
            'data'      => $budget->load('department')
        ]);
    }

    public function destroy(Budget $budget)
    {
        if ($budget->used_amount > 0 || $budget->reserved_amount > 0) {
            return response()->json([
                'success'   => false,
                'message'   => 'Anggaran tidak bisa dihapus karena sudah ada riwayat pemakaian.'
            ], 403);
        }

        $budget->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'Anggaran berhasil dihapus'
        ]);
    }
}
