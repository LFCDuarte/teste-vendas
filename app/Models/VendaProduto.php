<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendaProduto extends Model
{
    use HasFactory;

    protected $table = 'venda_produtos';

    protected $fillable = [
        'venda_id',
        'produto_id',
        'quantidade',
        'valor_unitario',
        'valor_total'
    ];

    protected $casts = [
        'valor_unitario' => 'decimal:2',
        'valor_total' => 'decimal:2'
    ];

    // Relacionamentos
    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    // Métodos
    public function calcularValorTotal()
    {
        $this->valor_total = $this->quantidade * $this->valor_unitario;
        $this->save();
    }

    public function getValorUnitarioFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_unitario, 2, ',', '.');
    }

    public function getValorTotalFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor_total, 2, ',', '.');
    }
}
