<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::where('is_active', true)->get();

        return response()->json($branches);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:2',
            'address' => 'required|string|max:255',
        ]);

        $branch = Branch::create($request->all());

        return response()->json([
            'message' => 'Unidade cadastrada com sucesso.',
            'branch' => $branch,
        ], 201);
    }

    public function show($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'error' => 'UNIDADE_NAO_ENCONTRADA',
                'message' => 'Unidade não encontrada.',
            ], 404);
        }

        return response()->json($branch);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'error' => 'UNIDADE_NAO_ENCONTRADA',
                'message' => 'Unidade não encontrada.',
            ], 404);
        }

        $branch->update($request->all());

        return response()->json([
            'message' => 'Unidade atualizada com sucesso.',
            'branch' => $branch,
        ]);
    }

    public function destroy($id)
    {
        $branch = Branch::find($id);

        if (!$branch) {
            return response()->json([
                'error' => 'UNIDADE_NAO_ENCONTRADA',
                'message' => 'Unidade não encontrada.',
            ], 404);
        }

        $branch->update(['is_active' => false]);

        return response()->json([
            'message' => 'Unidade desativada com sucesso.',
        ]);
    }
}