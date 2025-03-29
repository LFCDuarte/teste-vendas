<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendaItem extends Model
{
    protected $table = 'venda_itens'; // ou o nome que você definiu para sua tabela

    protected $fillable = [
        'venda_id',
        'produto_id',
        'quantidade',
        'preco_unitario'
    ];

    public function venda()
    {
        return $this->belongsTo(Venda::class);
    }

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }
}
