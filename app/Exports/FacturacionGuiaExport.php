<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FacturacionGuiaExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $dato;
    
    function __construct(array $dato)
    {
        $this->dato = $dato;
    }

    public function view(): View
    {
        return view('facturacion.export.guia', [
            'guias' => $this->dato
        ]);
    }
}
