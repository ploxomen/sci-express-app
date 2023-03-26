<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class Reportes implements FromView
{
   
    private $dato;
    private $blade;
    private $titulo;

    function __construct(string $blade, array $dato,string $titulo)
    {
        $this->blade = $blade;
        $this->dato = $dato;
        $this->titulo = $titulo;
    }

    public function view(): View
    {
        return view($this->blade, [
            'datos' => $this->dato,
            'titulo' => $this->titulo
        ]);
    }
}
