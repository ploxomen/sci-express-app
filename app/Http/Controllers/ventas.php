<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ventas extends Controller
{
    public function viewAgregarVenta()
    {
        return view('intranet.ventas.agregar');
    }
}
