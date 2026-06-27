<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['branch', 'product']);

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $inventories = $query->paginate(10);

        return response()->json($inventories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory = Inventory::where('branch_id', $request->branch_id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($inventory) {
            $inventory->update([
                'quantity' => $inventory->quantity + $request->quantity,
            ]);

            return response()->json([
                'message' => 'Estoque atualizado com sucesso.',
                'inventory' => $inventory,
            ]);
        }

        $inventory = Inventory::create($request->all());

        return response()->json([
            'message' => 'Estoque cadastrado com sucesso.',
            'inventory' => $inventory,
        ], 201);
    }

    public function show($branchId, $productId)
    {
        $inventory = Inventory::with(['branch', 'product'])
            ->where('branch_id', $branchId)
            ->where('product_id', $productId)
            ->first();

        if (!$inventory) {
            return response()->json([
                'error' => 'ESTOQUE_NAO_ENCONTRADO',
                'message' => 'Estoque não encontrado para esta unidade e produto.',
            ], 404);
        }

        return response()->json($inventory);
    }

    public function reduce(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $inventory = Inventory::where('branch_id', $request->branch_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (!$inventory || $inventory->quantity < $request->quantity) {
            return response()->json([
                'error' => 'ESTOQUE_INSUFICIENTE',
                'message' => 'Quantidade insuficiente em estoque.',
            ], 409);
        }

        $inventory->update([
            'quantity' => $inventory->quantity - $request->quantity,
        ]);

        return response()->json([
            'message' => 'Estoque reduzido com sucesso.',
            'inventory' => $inventory,
        ]);
    }
}