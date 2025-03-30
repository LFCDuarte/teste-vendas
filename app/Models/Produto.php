<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'valor',
        'status'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'status' => 'string'
    ];

    
    public function scopeAtivo($query)
    {
        return $query->where('status', 'ativo');
    }

    
    public function getValorFormatadoAttribute()
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }

    public function desativar()
    {
        $this->status = 'inativo';
        $this->save();
    }

    public function ativar()
    {
        $this->status = 'ativo';
        $this->save();
    }
}
