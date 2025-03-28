<?php

namespace App\Observers;

use App\Models\VendaProduto;

class VendaProdutoObserver
{
    public function created(VendaProduto $vendaProduto)
    {
        $vendaProduto->calcularValorTotal();
        $vendaProduto->venda->atualizarValorTotal();
    }

    public function updated(VendaProduto $vendaProduto)
    {
        $vendaProduto->calcularValorTotal();
        $vendaProduto->venda->atualizarValorTotal();
    }

    public function deleted(VendaProduto $vendaProduto)
    {
        $vendaProduto->venda->atualizarValorTotal();
    }
}
