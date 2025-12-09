<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'rfc',
        'contrasena_fiel',
        'certificado_path',
        'llave_path'
    ];

    protected $hidden = [
        'contrasena_fiel',
    ];
}