<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venda extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'valor_total',
        'numero_parcelas',
        'status',
        'data_venda',
        'forma_pagamento'
    ];

    protected $casts = [
        'data_venda' => 'date',
        'valor_total' => 'decimal:2'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'venda_produtos')
            ->withPivot(['quantidade', 'valor_unitario', 'valor_total'])
            ->withTimestamps();
    }

    public function parcelas()
    {
        return $this->hasMany(Parcela::class);
    }

    public function vendaProdutos()
    {
        return $this->hasMany(VendaProduto::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itens()
    {
        return $this->hasMany(VendaItem::class);
    }

    public function atualizarValorTotal()
    {
        $this->valor_total = $this->vendaProdutos()->sum('valor_total');
        $this->save();

        
        $this->atualizarParcelas();
    }

    public function atualizarParcelas()
    {
        
        $valorParcela = $this->valor_total / $this->numero_parcelas;

        
        $this->parcelas->each(function ($parcela) use ($valorParcela) {
            $parcela->valor = $valorParcela;
            $parcela->save();
        });
    }

    public function verificarStatus()
    {
        
        if ($this->parcelas()->where('status', '!=', 'paga')->doesntExist()) {
            $this->status = 'paga';
        }
        
        elseif ($this->parcelas()->where('status', 'vencida')->exists()) {
            $this->status = 'vencida';
        }
        
        else {
            $this->status = 'pendente';
        }

        $this->save();
    }

    public function getValorTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }

    public function getStatusFormatadoAttribute()
    {
        return match($this->status) {
            'pendente' => 'Pendente',
            'paga' => 'Paga',
            'vencida' => 'Vencida',
            'cancelada' => 'Cancelada',
            default => $this->status
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pendente' => 'warning',
            'paga' => 'success',
            'vencida' => 'danger',
            'cancelada' => 'secondary',
            default => 'primary'
        };
    }
}
