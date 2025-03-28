<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcela extends Model
{
    use HasFactory;

    protected $fillable = [
        'venda_id',
        'numero',
        'valor',
        'data_vencimento',
        'data_pagamento',
        'status'
    ];

    protected $casts = [
        'data_vencimento' => 'date',
        'data_pagamento' => 'date',
        'valor' => 'decimal:2'
    ];

    // Relacionamentos
    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    // Métodos
    public function verificarStatus()
    {
        if ($this->data_pagamento) {
            $this->status = 'paga';
        } elseif ($this->data_vencimento < now()) {
            $this->status = 'vencida';
        } else {
            $this->status = 'pendente';
        }
        $this->save();

        // Atualiza o status da venda
        $this->venda->verificarStatus();
    }

    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }
}
