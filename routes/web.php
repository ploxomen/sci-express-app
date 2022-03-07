<?php

use App\Http\Controllers\Clientes;
use App\Http\Controllers\Embarque;
use App\Http\Controllers\Entrega;
use App\Http\Controllers\Gias;
use App\Http\Controllers\Llegada;
use App\Http\Controllers\Manifiesto;
use App\Http\Controllers\Recojo;
use App\Http\Controllers\Solicitud;
use App\Http\Controllers\Ubigeo;
use App\Http\Controllers\Usuario;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('intranet')->group(function (){
    Route::prefix('cliente')->group(function(){
        Route::get('agregar-modificar',[Clientes::class,'viewAgreModi'])->name('cliAgreModi');
        Route::get('archivos',[Clientes::class,'viewArchivos'])->name('cliArchi');
        Route::get('mostrar', [Clientes::class, 'misClientes'])->name('viewMisClientes');
        Route::post('agregar',[Clientes::class,'agregarCli']);
        Route::post('obtener', [Clientes::class, 'obtenerClientes']);
        Route::post('obtener-editar', [Clientes::class, 'obtenerCliEdit']);
        Route::post('get-archivos', [Clientes::class, 'obtenerArchivosCli']);
        Route::post('change-files', [Clientes::class, 'cambiarEstadoArchi']);
    });
    Route::prefix('ubigeo')->group(function(){
        Route::post('departamentos',[Ubigeo::class,'getDepartamentos']);
        Route::post('provincias', [Ubigeo::class, 'getProvincias']);
        Route::post('distritos', [Ubigeo::class, 'getDistritos']);
    });
    Route::prefix('manifiesto')->group(function () {
        Route::get('ver', [Manifiesto::class, 'viewManifiesto'])->name('viewManifi');
        Route::get('nuevo', [Manifiesto::class, 'viewNuevoManifiesto'])->name('viewNuevoManif');
        Route::get('programar-embarque', [Manifiesto::class, 'viewProgEmba'])->name('viewProgEmba');
        Route::post('ver-guias', [Manifiesto::class, 'viewGuias']);
        Route::post('agregar', [Manifiesto::class, 'agregarManifiesto']);
    });
    Route::prefix('usuarios')->group(function(){
        Route::get('agregar-modificar',[Usuario::class,'viewCrearModificar'])->name('usuAgreMod');
        Route::post('agregar',[Usuario::class,'agregar']);
        Route::post('modificar',[Usuario::class,'modificar']);
        Route::get('lista',[Usuario::class,'viewLista'])->name('usuLista');
        Route::get('logout',[Usuario::class,'cerrarSesion'])->name('closeSesion');
    });
    Route::prefix('pedidos')->group(function(){
        Route::prefix('recojos')->group(function () {
            Route::get('/', [Recojo::class, 'viewPediReco'])->name('pedidoRecojo');
            Route::post('obtener', [Recojo::class, 'obtenerRecojo']);
            Route::post('agregar', [Recojo::class, 'agregarRecojo']);
            Route::post('modificar', [Recojo::class, 'modificarRecojo']);
            Route::post('modificar-estados', [Recojo::class, 'modificarEstados']);
        });
        Route::get('lista-embaque',[Embarque::class,'viewListEmba'])->name('pedidoListEmbar');
        Route::get('llegada',[Llegada::class,'viewLlegada'])->name('pedidoLlegada');
        Route::get('entrega',[Entrega::class,'viewEntrega'])->name('pedidoEntrega');
    });
    Route::prefix('comprobante')->group(function(){
        Route::get('guias',[Gias::class,'viewGuias'])->name('compGuias');
        Route::post('agregar-guia', [Gias::class, 'addGuia']);
    });
    Route::prefix('solicitud')->group(function () {
        Route::post('obtener-cliente', [Solicitud::class, 'obtenerDetalleCli']);
        Route::post('obtener', [Solicitud::class, 'obtenerSolicitud']);
        Route::post('nuevo', [Solicitud::class, 'insertarNuevaSoli']);
        Route::get('agregar', [Solicitud::class, 'viewSolicitud'])->name('solicitud');
    });
    Route::get('inicio',[Usuario::class,'viewHome'])->name('home');
});
Route::middleware(['guest'])->prefix('intranet')->group(function(){
    Route::get('login',[Usuario::class,'viewLogin'])->name('login');
    Route::post('usuarios/autenticacion',[Usuario::class,'autenticacion']);
});

