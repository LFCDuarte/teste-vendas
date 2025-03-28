<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $produtos = Produto::all();
        return view('produtos.index', compact('produtos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('produtos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Limpa o valor antes da validação
        $valor = str_replace(['R$', '.', ' '], '', $request->valor);
        $valor = str_replace(',', '.', $valor);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        // Adiciona o valor limpo aos dados validados
        $validated['valor'] = $valor;

        Produto::create($validated);

        return redirect()->route('produtos.index')
            ->with('status', 'Produto criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Produto $produto)
    {
        // Limpa o valor antes da validação
        $valor = str_replace(['R$', '.', ' '], '', $request->valor);
        $valor = str_replace(',', '.', $valor);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        // Adiciona o valor limpo aos dados validados
        $validated['valor'] = $valor;

        $produto->update($validated);

        return redirect()->route('produtos.index')
            ->with('status', 'Produto atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Produto $produto)
    {
        $produto->delete();

        return redirect()->route('produtos.index')
            ->with('status', 'Produto excluído com sucesso!');
    }
}
