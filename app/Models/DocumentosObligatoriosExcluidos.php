<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentosObligatoriosExcluidos extends Model
{
    protected $table = 'documentos_obligatorios_excluidos';
    protected $fillable = ['doc_oblig_id','cliente_id'];
}
