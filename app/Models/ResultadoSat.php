<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoSat extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'periodo',
        'resultado',
        'detalles',
        'pdf_path'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}