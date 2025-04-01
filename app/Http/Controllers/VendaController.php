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
use Barryvdh\DomPDF\Facade\Pdf;

class VendaController extends Controller
{
    
    public function index(Request $request)
    {
        $query = Venda::with(['cliente', 'vendedor', 'parcelas']);

        if ($request->filled('cliente')) {
            $query->whereHas('cliente', function ($q) use ($request) {
                $q->where('nome', 'like', '%' . $request->cliente . '%');
            });
        }

        if ($request->filled('vendedor')) {
            $query->whereHas('vendedor', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->vendedor . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_venda', '>=', $request->data_inicio);
        }
        if ($request->filled('data_fim')) {
            $query->whereDate('data_venda', '<=', $request->data_fim);
        }

        if ($request->filled('valor_minimo')) {
            $valorMinimo = str_replace(['R$', '.', ','], ['', '', '.'], $request->valor_minimo);
            $query->where('valor_total', '>=', $valorMinimo);
        }
        if ($request->filled('valor_maximo')) {
            $valorMaximo = str_replace(['R$', '.', ','], ['', '', '.'], $request->valor_maximo);
            $query->where('valor_total', '<=', $valorMaximo);
        }

        $vendas = $query->latest()->paginate(10);
        $clientes = Cliente::orderBy('nome')->get();
        $vendedores = User::orderBy('name')->get();
        

        return view('vendas.index', compact('vendas', 'clientes', 'vendedores'));
    }

    public function create()
    {
        $clientes = Cliente::where('ativo', true)->get();
        $produtos = Produto::where('ativo', true)->get();
        
        
        return view('vendas.create', compact('clientes', 'produtos'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Dados da venda recebidos:', [
                'request_all' => $request->all(),
                'request_produtos' => $request->input('produtos'),
                'request_cliente_id' => $request->input('cliente_id'),
                'request_numero_parcelas' => $request->input('numero_parcelas'),
                'request_forma_pagamento' => $request->input('forma_pagamento')
            ]);

            $validated = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'produtos' => 'required|array|min:1',
                'produtos.*.id' => [
                    'required',
                    'exists:produtos,id',
                    function ($attribute, $value, $fail) {
                        $produto = Produto::where('id', $value)
                                        ->where('status', 'ativo')
                                        ->first();
                        
                        if (!$produto) {
                            $fail('O produto selecionado está inativo e não pode ser vendido.');
                        }
                    },
                ],
                'produtos.*.quantidade' => 'required|integer|min:1',
                'numero_parcelas' => 'required|integer|min:1|max:12',
                'forma_pagamento' => 'required|in:dinheiro,pix,debito,credito,boleto'
            ]);

            
            $parcelasSemParcelamento = ['dinheiro', 'pix', 'debito'];
            if (in_array($validated['forma_pagamento'], $parcelasSemParcelamento)) {
                $validated['numero_parcelas'] = 1;
            }

            Log::info('Dados validados:', $validated);

            DB::beginTransaction();

            try {
                $venda = Venda::create([
                    'cliente_id' => $validated['cliente_id'],
                    'user_id' => auth()->id(),
                    'valor_total' => 0,
                    'numero_parcelas' => $validated['numero_parcelas'],
                    'data_venda' => now(),
                    'status' => 'pendente',
                    'forma_pagamento' => $validated['forma_pagamento']
                ]);

                Log::info('Venda criada:', ['id' => $venda->id]);

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

                $venda->valor_total = $valorTotal;
                $venda->save();

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

    
    public function show(Venda $venda)
    {
        $venda->load(['cliente', 'vendaProdutos.produto', 'parcelas']);
        return view('vendas.show', compact('venda'));
    }

    
    public function edit(Venda $venda)
    {
        if ($venda->status !== 'pendente') {
            return redirect()->route('vendas.show', $venda)
                ->with('error', 'Apenas vendas pendentes podem ser editadas.');
        }

        $clientes = Cliente::where('ativo', true)->get();
        $produtos = Produto::where('ativo', true)->get();

        $venda->load(['cliente', 'vendaProdutos.produto', 'parcelas']);
        return view('vendas.edit', compact('venda', 'clientes', 'produtos'));
    }

   
    public function update(Request $request, Venda $venda)
    {
        if ($venda->status !== 'pendente') {
            return back()->withErrors(['error' => 'Apenas vendas pendentes podem ser editadas.']);
        }

        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'produtos' => 'required|array',
            'produtos.*.id' => [
                'required',
                'exists:produtos,id',
                function ($attribute, $value, $fail) {
                    $produto = Produto::where('id', $value)
                                    ->where('status', 'ativo')
                                    ->first();
                    
                    if (!$produto) {
                        $fail('O produto selecionado está inativo e não pode ser vendido.');
                    }
                },
            ],
            'produtos.*.quantidade' => 'required|integer|min:1',
            'numero_parcelas' => 'required|integer|min:1|max:12',
            'forma_pagamento' => 'required|in:dinheiro,pix,debito,credito,boleto'
        ]);

        
        $parcelasSemParcelamento = ['dinheiro', 'pix', 'debito'];
        if (in_array($validated['forma_pagamento'], $parcelasSemParcelamento)) {
            $validated['numero_parcelas'] = 1;
        }

        try {
            DB::beginTransaction();

            $venda->update([
                'cliente_id' => $validated['cliente_id'],
                'user_id' => auth()->id(),
                'numero_parcelas' => $validated['numero_parcelas'],
                'forma_pagamento' => $validated['forma_pagamento']
            ]);

            $venda->vendaProdutos()->delete();

            $valorTotal = 0;
            foreach ($validated['produtos'] as $produtoData) {
                $produto = Produto::where('id', $produtoData['id'])
                                ->where('status', 'ativo')
                                ->first();
                
                if (!$produto) {
                    throw new \Exception("O produto não está mais disponível para venda.");
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

            $venda->valor_total = $valorTotal;
            $venda->save();

            $venda->parcelas()->delete();

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

        

        return back()->with('status', 'Parcela paga com sucesso!');
    }

    public function downloadPDF($id)
    {
        $venda = Venda::with(['cliente', 'vendedor', 'vendaProdutos.produto'])
            ->findOrFail($id);

        $pdf = PDF::loadView('pdf.venda', compact('venda'));

        return $pdf->download('venda-' . $id . '.pdf');
    }

    public function relatorioVendas()
    {
        $vendas = Venda::with(['cliente', 'vendedor'])
            ->orderBy('created_at', 'desc')
            ->get();

        $pdf = PDF::loadView('pdf.relatorio-vendas', compact('vendas'));

        return $pdf->download('relatorio-vendas.pdf');
    }
}
