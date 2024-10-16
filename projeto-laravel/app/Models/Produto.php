<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome_produto',
        'categoria_id',
        'categoria',
        'subcategoria',
        'nome_fornecedor',
        'fornecedor_id', 
        'quantidade',
        'preco',
    ];

    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }

    public function vendas()
    {
        return $this->hasMany(Venda::class);
    }

}
