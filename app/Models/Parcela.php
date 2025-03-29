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
        $oldStatus = $this->status;

        if ($this->data_pagamento) {
            $this->status = 'paga';
        } elseif ($this->data_vencimento < now()->startOfDay()) {
            $this->status = 'vencida';
        } else {
            $this->status = 'pendente';
        }

        // Se o status mudou, salva e atualiza a venda
        if ($oldStatus !== $this->status) {
            $this->save();
            $this->venda->verificarStatus();
        }
    }

    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function getStatusFormatadoAttribute()
    {
        $status = [
            'pendente' => 'Pendente',
            'paga' => 'Paga',
            'vencida' => 'Vencida'
        ];

        return $status[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pendente' => 'warning',
            'paga' => 'success',
            'vencida' => 'danger'
        ];

        return $colors[$this->status] ?? 'primary';
    }
}
