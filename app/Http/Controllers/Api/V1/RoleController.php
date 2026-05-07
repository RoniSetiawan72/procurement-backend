<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequst;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::with('permissions');

        if ($request->has('search') && $request->search != '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'ilike', $searchTerm);
            });
        }

        $roles = $query->latest()->paginate(10);

        return RoleResource::collection($roles);
    }

    public function store(StoreRoleRequst $request)
    {
        try {
            $role = DB::transaction(function () use ($request) {
                $newRole = Role::create([
                    'name'          => $request->validated('name'),
                    'guard_name'    => 'web'
                ]);
                $newRole->syncPermissions($request->validated('permissions'));
                return $newRole;
            });

            return response()->json([
                'success'   => true,
                'data'      => new RoleResource($role->load('permissions'))
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'message'   => $e->getMessage()
            ], 500);
        }
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        if ($role->name === 'Administrator' && $request->validated('name') !== 'Administrator') {
            return response()->json([
                'success'   => false,
                'message'   => 'Role Administrator sistem tidak boleh diubah.'
            ], 403);
        }

        try {
            DB::beginTransaction();
            $role->update([
                'name'  => $request->validated('name')
            ]);

            $role->syncPermissions($request->validated('permissions'));
            DB::commit();

            return response()->json([
                'success'   => true,
                'message'   => 'Role berhasil diperbarui.',
                'data'      => new RoleResource($role->load('permissions'))
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'   => false,
                'message'   => 'Terjadi kesalahan saat memperbarui role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Administrator') {
            return response()->json([
                'success'   => false,
                'message'   => 'Role Administrator sistem tidak boleh dihapus.'
            ], 403);
        }

        $role->delete();
        return response()->json([
            'success'   => true,
            'message'   => 'Role berhasil dihapus.'
        ]);
    }
}
