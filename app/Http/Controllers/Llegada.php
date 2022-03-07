<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Llegada extends Controller
{
    public function viewLlegada()
    {
        $modulos = User::getModulos(Auth::id());
        return view('pedidos.llegada',compact('modulos'));
    }
}
