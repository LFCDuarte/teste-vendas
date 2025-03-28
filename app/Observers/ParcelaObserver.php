<?php

namespace App\Observers;

use App\Models\Parcela;

class ParcelaObserver
{
    public function created(Parcela $parcela)
    {
        $parcela->verificarStatus();
    }

    public function updated(Parcela $parcela)
    {
        $parcela->verificarStatus();
    }

    public function deleted(Parcela $parcela)
    {
        $parcela->venda->verificarStatus();
    }
}
