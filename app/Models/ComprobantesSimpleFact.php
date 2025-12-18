<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComprobantesSimpleFact extends Model
{
    protected $fillable = ['tipo','serie','correlativo'];
    protected function scopePorTipo($query,$tipo)
    {
        return $query->where('tipo',$tipo);
    }
    protected function scopePorSerie($query, $serie)
    {
        return $query->where('serie',$serie);
    }

}
