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
        'data_venda'
    ];

    protected $casts = [
        'data_venda' => 'date',
        'valor_total' => 'decimal:2'
    ];

    // Relacionamentos
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

    // Métodos
    public function atualizarValorTotal()
    {
        $this->valor_total = $this->vendaProdutos()->sum('valor_total');
        $this->save();

        // Atualiza as parcelas
        $this->atualizarParcelas();
    }

    public function atualizarParcelas()
    {
        // Calcula o valor de cada parcela
        $valorParcela = $this->valor_total / $this->numero_parcelas;

        // Atualiza cada parcela
        $this->parcelas->each(function ($parcela) use ($valorParcela) {
            $parcela->valor = $valorParcela;
            $parcela->save();
        });
    }

    public function verificarStatus()
    {
        // Se todas as parcelas estão pagas
        if ($this->parcelas()->where('status', '!=', 'paga')->doesntExist()) {
            $this->status = 'paga';
        }
        // Se existe alguma parcela vencida
        elseif ($this->parcelas()->where('status', 'vencida')->exists()) {
            $this->status = 'vencida';
        }
        // Se não está paga nem vencida, está pendente
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
        $status = [
            'pendente' => 'Pendente',
            'paga' => 'Paga',
            'vencida' => 'Vencida',
            'cancelada' => 'Cancelada'
        ];

        return $status[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pendente' => 'warning',
            'paga' => 'success',
            'vencida' => 'danger',
            'cancelada' => 'secondary'
        ];

        return $colors[$this->status] ?? 'primary';
    }
}
