<?php

namespace App\Observers;

use App\Models\Produto;

class ProdutoObserver
{
    public function updated(Produto $produto)
    {
        
        if ($produto->isDirty('valor')) {
            $produto->vendas()
                ->whereHas('venda', function ($query) {
                    $query->whereIn('status', ['pendente']);
                })
                ->each(function ($vendaProduto) use ($produto) {
                    $vendaProduto->valor_unitario = $produto->valor;
                    $vendaProduto->save(); 
                });
        }
    }

    public function deleted(Produto $produto)
    {
        
        $produto->vendas()
            ->whereHas('venda', function ($query) {
                $query->whereIn('status', ['pendente']);
            })
            ->each(function ($vendaProduto) {
                $vendaProduto->delete();
            });
    }
}
