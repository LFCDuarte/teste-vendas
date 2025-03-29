<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

    public function desativar()
    {
        $this->ativo = false;
        $this->save();
    }
}
