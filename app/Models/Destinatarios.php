<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Destinatarios extends Model
{
    protected $table = 'clientes_de_clientes';
    public $timestamps = false;
    protected $fillable = [
        'tipo_doc',
        'nro_doc',
        'nombres',
        'direccion',
        'departamento',
        'provincia',
        'distrito',
        'id_ciudad_final',
        'activo'
    ];
    public function scopeDocumentos($query){
        return $query->leftJoin('tipo_documento','tipo_documento.id','=','tipo_doc');
    }

}
