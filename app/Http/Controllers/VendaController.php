<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Produto;
use App\Models\Venda;
use App\Models\VendaProduto;
use App\Models\Parcela;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class VendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Venda::with(['cliente', 'parcelas', 'vendedor']);

        // Filtro por cliente
        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->cliente . '%');
            });
        }

        // Filtro por status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por período
        if ($request->filled('data_inicio')) {
            $query->whereDate('data_venda', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('data_venda', '<=', $request->data_fim);
        }

        // Filtro por vendedor
        if ($request->filled('vendedor')) {
            $query->whereHas('vendedor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->vendedor . '%');
            });
        }

        // Filtro por valor
        if ($request->filled('valor_minimo')) {
            $query->where('valor_total', '>=', str_replace(['R$', '.', ','], ['', '', '.'], $request->valor_minimo));
        }
        if ($request->filled('valor_maximo')) {
            $query->where('valor_total', '<=', str_replace(['R$', '.', ','], ['', '', '.'], $request->valor_maximo));
        }

        $vendas = $query->latest()->get();
        $clientes = Cliente::orderBy('nome')->get();
        $vendedores = User::orderBy('name')->get();

        return view('vendas.index', compact('vendas', 'clientes', 'vendedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::all();
        $produtos = Produto::all();
        return view('vendas.create', compact('clientes', 'produtos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Log dos dados recebidos
            Log::info('Dados da venda recebidos:', [
                'request_all' => $request->all(),
                'request_produtos' => $request->input('produtos'),
                'request_cliente_id' => $request->input('cliente_id'),
                'request_numero_parcelas' => $request->input('numero_parcelas')
            ]);

            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'produtos' => 'required|array|min:1',
                'produtos.*.id' => 'required|exists:produtos,id',
                'produtos.*.quantidade' => 'required|integer|min:1',
                'numero_parcelas' => 'required|integer|min:1|max:12',
            ]);

            Log::info('Dados validados:', $validated);

            DB::beginTransaction();

            try {
                // Criar a venda
                $venda = Venda::create([
                    'cliente_id' => $validated['cliente_id'],
                    'user_id' => auth()->id(),
                    'valor_total' => 0, // Será calculado automaticamente
                    'numero_parcelas' => $validated['numero_parcelas'],
                    'data_venda' => now(),
                    'status' => 'pendente'
                ]);

                Log::info('Venda criada:', ['id' => $venda->id]);

                // Adicionar produtos
                $valorTotal = 0;
                foreach ($validated['produtos'] as $produtoData) {
                    $produto = Produto::find($produtoData['id']);
                    if (!$produto) {
                        throw new \Exception("Produto não encontrado: {$produtoData['id']}");
                    }

                    $valorProduto = $produto->valor * $produtoData['quantidade'];
                    $valorTotal += $valorProduto;

                    VendaProduto::create([
                        'venda_id' => $venda->id,
                        'produto_id' => $produto->id,
                        'quantidade' => $produtoData['quantidade'],
                        'valor_unitario' => $produto->valor,
                        'valor_total' => $valorProduto
                    ]);
                }

                Log::info('Produtos adicionados, valor total:', [
                    'valor_total' => $valorTotal,
                    'produtos' => $validated['produtos']
                ]);

                // Atualizar valor total da venda
                $venda->valor_total = $valorTotal;
                $venda->save();

                // Criar parcelas
                $valorParcela = $venda->valor_total / $venda->numero_parcelas;
                for ($i = 1; $i <= $venda->numero_parcelas; $i++) {
                    Parcela::create([
                        'venda_id' => $venda->id,
                        'numero' => $i,
                        'valor' => $valorParcela,
                        'data_vencimento' => Carbon::now()->addMonths($i-1),
                        'status' => 'pendente'
                    ]);
                }

                Log::info('Parcelas criadas');

                DB::commit();
                Log::info('Transação concluída com sucesso');

                return redirect()->route('vendas.show', $venda)
                    ->with('status', 'Venda criada com sucesso!');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Erro durante a transação:', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Erro de validação:', [
                'errors' => $e->errors(),
                'request' => $request->all()
            ]);
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Erro ao criar venda:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return back()
                ->withErrors(['error' => 'Erro ao criar venda: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Venda $venda)
    {
        $venda->load(['cliente', 'vendaProdutos.produto', 'parcelas']);
        return view('vendas.show', compact('venda'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venda $venda)
    {
        if ($venda->status !== 'pendente') {
            return back()->withErrors(['error' => 'Apenas vendas pendentes podem ser editadas.']);
        }

        $clientes = Cliente::all();
        $produtos = Produto::all();
        $venda->load(['cliente', 'vendaProdutos.produto', 'parcelas']);

        return view('vendas.edit', compact('venda', 'clientes', 'produtos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venda $venda)
    {
        if ($venda->status !== 'pendente') {
            return back()->withErrors(['error' => 'Apenas vendas pendentes podem ser editadas.']);
        }

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'produtos' => 'required|array',
            'produtos.*.id' => 'required|exists:produtos,id',
            'produtos.*.quantidade' => 'required|integer|min:1',
            'numero_parcelas' => 'required|integer|min:1|max:12',
        ]);

        try {
            DB::beginTransaction();

            // Atualizar dados básicos da venda
            $venda->update([
                'cliente_id' => $validated['cliente_id'],
                'user_id' => auth()->id(),
                'numero_parcelas' => $validated['numero_parcelas']
            ]);

            // Remover produtos antigos
            $venda->vendaProdutos()->delete();

            // Adicionar novos produtos
            $valorTotal = 0;
            foreach ($validated['produtos'] as $produtoData) {
                $produto = Produto::find($produtoData['id']);
                $valorProduto = $produto->valor * $produtoData['quantidade'];
                $valorTotal += $valorProduto;

                VendaProduto::create([
                    'venda_id' => $venda->id,
                    'produto_id' => $produto->id,
                    'quantidade' => $produtoData['quantidade'],
                    'valor_unitario' => $produto->valor,
                    'valor_total' => $valorProduto
                ]);
            }

            // Atualizar valor total da venda
            $venda->valor_total = $valorTotal;
            $venda->save();

            // Remover parcelas antigas
            $venda->parcelas()->delete();

            // Criar novas parcelas
            $valorParcela = $venda->valor_total / $venda->numero_parcelas;
            for ($i = 1; $i <= $venda->numero_parcelas; $i++) {
                Parcela::create([
                    'venda_id' => $venda->id,
                    'numero' => $i,
                    'valor' => $valorParcela,
                    'data_vencimento' => Carbon::now()->addMonths($i-1),
                    'status' => 'pendente'
                ]);
            }

            DB::commit();

            return redirect()->route('vendas.show', $venda)
                ->with('status', 'Venda atualizada com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erro ao atualizar venda: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venda $venda)
    {
        if ($venda->status !== 'pendente') {
            return back()->withErrors(['error' => 'Apenas vendas pendentes podem ser canceladas.']);
        }

        $venda->status = 'cancelada';
        $venda->save();

        return redirect()->route('vendas.index')
            ->with('status', 'Venda cancelada com sucesso!');
    }

    public function pagarParcela(Parcela $parcela)
    {
        if ($parcela->status !== 'pendente') {
            return back()->withErrors(['error' => 'Esta parcela não pode ser paga.']);
        }

        $parcela->status = 'paga';
        $parcela->data_pagamento = now();
        $parcela->save();

        // O observer vai atualizar o status da venda automaticamente

        return back()->with('status', 'Parcela paga com sucesso!');
    }
}
