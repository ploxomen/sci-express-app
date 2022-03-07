<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Entrega extends Controller
{
   public function viewEntrega()
   {
        $modulos = User::getModulos(Auth::id());
        return view('pedidos.entregas',compact('modulos'));
   }
}
