<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::query();

        if ($request->has('search') && $request->search != null) {
            $searchTerm = '%' . $request->search . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('item_code', 'ilike', $searchTerm)
                ->orWhere('name', 'ilike', $searchTerm);
            });
        }

        $items = $query->orderBy('name', 'asc')->paginate(10);
        return ItemResource::collection($items);
    }

    public function store(StoreItemRequest $request)
    {
        $item = Item::create($request->validated());
        return new ItemResource($item);
    }

    public function update(UpdateItemRequest $request, $id)
    {
        $item = Item::findOrFail($id);
        $item->update($request->validated());
        return new ItemResource($item);
    }

    public function destroy(string $id)
    {
        $item = Item::findOrFail($id);
        $item->delete();

        return response()->json([
            'success'   => true,
            'message'   => 'Item berhasil dihapus.'
        ], 200);
    }
}
