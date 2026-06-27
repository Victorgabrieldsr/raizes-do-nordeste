<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('is_active', true)
            ->paginate(10);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_seasonal' => 'boolean',
        ]);

        $product = Product::create($request->all());

        return response()->json([
            'message' => 'Produto cadastrado com sucesso.',
            'product' => $product,
        ], 201);
    }

    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'PRODUTO_NAO_ENCONTRADO',
                'message' => 'Produto não encontrado.',
            ], 404);
        }

        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'PRODUTO_NAO_ENCONTRADO',
                'message' => 'Produto não encontrado.',
            ], 404);
        }

        $product->update($request->all());

        return response()->json([
            'message' => 'Produto atualizado com sucesso.',
            'product' => $product,
        ]);
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'error' => 'PRODUTO_NAO_ENCONTRADO',
                'message' => 'Produto não encontrado.',
            ], 404);
        }

        $product->update(['is_active' => false]);

        return response()->json([
            'message' => 'Produto desativado com sucesso.',
        ]);
    }
}