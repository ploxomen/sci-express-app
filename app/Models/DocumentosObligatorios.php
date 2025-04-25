<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentosObligatorios extends Model
{
    protected $table = 'documentos_obligatorios';
    protected $fillable = ['opcion'];
}
