<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Embarque extends Controller
{
    public function viewListEmba(Request $request)
    {
        $modulos = User::getModulos(Auth::id());
        return view('pedidos.listaembarque',compact('modulos'));
    }
}
