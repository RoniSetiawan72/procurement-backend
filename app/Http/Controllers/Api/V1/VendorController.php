<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorRequest;
use App\Http\Requests\UpdateVendorRequest;
use App\Http\Resources\VendorResource;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        $query = Vendor::query();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'ilike', $searchTerm)
                ->orWhere('email', 'ilike', $searchTerm);
            });
        }

        $vendors = $query->orderBy('name', 'asc')->paginate(10);
        return VendorResource::collection($vendors);
    }

    public function store(StoreVendorRequest $request)
    {
        $vendor = Vendor::create($request->validated());
        return new VendorResource($vendor);
    }

    public function update(UpdateVendorRequest $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->update($request->validated());
        return new VendorResource($vendor);
    }

    public function destroy(string $id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Vendor berhasil dihapus.'
        ], 200);
    }
}
