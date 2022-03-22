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
        Route::post('eliminar', [Clientes::class, 'eliminarCli']);
        Route::post('eliminar-proveedor', [Clientes::class, 'eliminarProveedor']);
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
    Route::prefix('cluster')->group(function () {
        Route::post('obtener-provincia', [Manifiesto::class, 'obtenerClusterProv']);
    });
    Route::prefix('pedidos')->group(function(){
        Route::prefix('recojos')->group(function () {
            Route::get('/', [Recojo::class, 'viewPediReco'])->name('pedidoRecojo');
            Route::get('pendientes', [Recojo::class, 'recojoPendiente'])->name('recojoPendiTraba');
            Route::post('actualizar-trabajador', [Recojo::class, 'actualizarRecoTra']);
            Route::post('obtener', [Recojo::class, 'obtenerRecojo']);
            Route::post('agregar', [Recojo::class, 'agregarRecojo']);
            Route::post('modificar', [Recojo::class, 'modificarRecojo']);
            Route::post('modificar-estados', [Recojo::class, 'modificarEstados']);
        });
        Route::prefix('embarque')->group(function () {
            Route::get('pendientes', [Embarque::class, 'viewListEmba'])->name('pedidoListEmbar');
            Route::post('manifiesto/pendientes', [Embarque::class, 'obtenerManiPend']);
            Route::post('manifiesto/embarcar', [Embarque::class, 'embarcarMani']);
            Route::get('transporte', [Embarque::class, 'transporteEmbarque'])->name('embarqueTrabajo');
            Route::post('actualizar-estado', [Embarque::class, 'actualizarEmbarque']);
            Route::get('informe', [Manifiesto::class, 'viewProgEmba'])->name('viewProgEmba');
            Route::post('obtener-informe', [Embarque::class, 'obtenerInformeEmbarque']);
            Route::post('obtener-informe-detalle', [Embarque::class, 'obtenerInfoEmbDeta']);
            Route::post('agregar-informe', [Embarque::class, 'agregarInforme']);
        });
        Route::prefix('llegada')->group(function () {
            Route::get('/', [Llegada::class, 'viewLlegada'])->name('pedidoLlegada');
            Route::post('embarques/arribar',[Llegada::class,'obtenerEmbarqueArribar']);
            Route::post('arribar-guias', [Llegada::class, 'actualizarGuiasLlegada']);

        });
        Route::get('entrega',[Entrega::class,'viewEntrega'])->name('pedidoEntrega');
    });
    Route::prefix('comprobante')->group(function(){
        Route::prefix('guias')->group(function () {
            Route::get('/', [Gias::class, 'viewGuias'])->name('compGuias');
            Route::post('obtener-clipro', [Gias::class, 'obtenerCliOrProvDeta']);
            Route::post('detalle-cli-de-cli', [Gias::class, 'detalleCliDeCli']);
            Route::post('agregar-guia', [Gias::class, 'addGuia']);
        });
    });
    Route::prefix('solicitud')->group(function () {
        Route::post('ver-proveedor', [Solicitud::class, 'obtenerProveedor']);
        Route::post('obtener-cliente', [Solicitud::class, 'obtenerDetalleCli']);
        Route::post('obtener', [Solicitud::class, 'obtenerSolicitud']);
        Route::post('asignar-personal', [Solicitud::class, 'asignarPersonal']);
        Route::post('ver-detalle', [Solicitud::class, 'verDetalleSoli']);
        Route::post('cambiar-estado', [Solicitud::class, 'cambiarEstadoSoli']);
        Route::post('eliminar-solicitud', [Solicitud::class, 'deleteSolicitud']);
        Route::post('nuevo', [Solicitud::class, 'insertarNuevaSoli']);
        Route::get('mostrar', [Solicitud::class, 'mostrarSolicitud'])->name('misSolicitudes');
        Route::get('agregar', [Solicitud::class, 'viewSolicitud'])->name('solicitud');
    });
    Route::get('inicio',[Usuario::class,'viewHome'])->name('home');
});
Route::middleware(['guest'])->prefix('intranet')->group(function(){
    Route::get('login',[Usuario::class,'viewLogin'])->name('login');
    Route::post('usuarios/autenticacion',[Usuario::class,'autenticacion']);
});

