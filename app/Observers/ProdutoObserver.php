<?php

namespace App\Observers;

use App\Models\Produto;

class ProdutoObserver
{
    public function updated(Produto $produto)
    {
        // Se o valor do produto foi alterado, atualiza todas as vendas ativas que contêm este produto
        if ($produto->isDirty('valor')) {
            $produto->vendas()
                ->whereHas('venda', function ($query) {
                    $query->whereIn('status', ['pendente']);
                })
                ->each(function ($vendaProduto) use ($produto) {
                    $vendaProduto->valor_unitario = $produto->valor;
                    $vendaProduto->save(); // Isso vai disparar o VendaProdutoObserver
                });
        }
    }

    public function deleted(Produto $produto)
    {
        // Quando um produto é deletado, atualiza todas as vendas que o contêm
        $produto->vendas()
            ->whereHas('venda', function ($query) {
                $query->whereIn('status', ['pendente']);
            })
            ->each(function ($vendaProduto) {
                $vendaProduto->delete(); // Isso vai disparar o VendaProdutoObserver
            });
    }
}
