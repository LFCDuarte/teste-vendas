<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\VendaProduto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    
    public function index()
    {
        $produtos = Produto::all();
        return view('produtos.index', compact('produtos'));
    }

   
    public function create()
    {
        return view('produtos.create');
    }

    
    public function store(Request $request)
    {
        $valor = str_replace(['R$', '.', ' '], '', $request->valor);
        $valor = str_replace(',', '.', $valor);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $validated['valor'] = $valor;

        Produto::create($validated);

        return redirect()->route('produtos.index')
            ->with('status', 'Produto criado com sucesso!');
    }

    public function show(string $id)
    {
        
    }

    public function edit(Produto $produto)
    {
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        
        $valor = str_replace(['R$', '.', ' '], '', $request->valor);
        $valor = str_replace(',', '.', $valor);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $validated['valor'] = $valor;

        $produto->update($validated);

        return redirect()->route('produtos.index')
            ->with('status', 'Produto atualizado com sucesso!');
    }

    public function destroy(Produto $produto)
    {
        try {
            $vendasComProduto = VendaProduto::where('produto_id', $produto->id)->exists();

            if ($vendasComProduto) {
                return back()->with('error', 'Não é possível excluir este produto pois ele está vinculado a uma ou mais vendas. Para manter o histórico de vendas, considere desativar o produto ao invés de excluí-lo.');
            }

            $produto->delete();
            return redirect()->route('produtos.index')->with('status', 'Produto excluído com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Não foi possível excluir o produto. Ele pode estar sendo usado em outras partes do sistema.');
        }
    }

    public function toggleAtivo(Produto $produto)
    {
        $produto->ativo = !$produto->ativo;
        $produto->save();

        $status = $produto->ativo ? 'ativado' : 'desativado';
        return back()->with('status', "Produto {$status} com sucesso!");
    }
}
