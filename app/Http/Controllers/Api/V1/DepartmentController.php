<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::query();

        if($request->has('search') && $request->search != '') {
            $saerchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($saerchTerm) {
                $q->where('name', 'ilike', $saerchTerm)
                ->orWhere('code', 'ilike', $saerchTerm);
            });
        }

        $departments = $query->orderBy('name', 'asc')->paginate(10);
        return DepartmentResource::collection($departments);
    }

    public function store(StoreDepartmentRequest $request)
    {
        $department = Department::create($request->validated());
        return new DepartmentResource($department);
    }

    public function update(UpdateDepartmentRequest $request, $id)
    {
        $department = Department::findOrFail($id);
        $department->update($request->validated());

        return new DepartmentResource($department);
    }

    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Department berhasil dihapus.'
        ], 200);
    }
}
