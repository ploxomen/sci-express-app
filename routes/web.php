<?php

use App\Http\Controllers\Clientes;
use App\Http\Controllers\Cluster;
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
        Route::post('ciudades', [Ubigeo::class, 'dbCiudades']);
    });
    Route::prefix('manifiesto')->group(function () {
        Route::get('ver', [Manifiesto::class, 'viewManifiesto'])->name('viewManifi');
        Route::get('nuevo', [Manifiesto::class, 'viewNuevoManifiesto'])->name('viewNuevoManif');
        Route::post('ver-guias', [Manifiesto::class, 'viewGuias']);
        Route::post('agregar', [Manifiesto::class, 'agregarManifiesto']);
    });
    Route::prefix('vehiculos')->group(function () {
        Route::get('todos', [Embarque::class, 'viewVehiculos'])->name('viewVehiculos');
        Route::post('obtener', [Embarque::class, 'obtenerVehiculosTransporte']);
        Route::post('nuevo', [Embarque::class, 'agregarVehiculoTransporte']);
        Route::post('actualizar', [Embarque::class, 'actualizarVehiculoTransporte']);
        Route::post('eliminar', [Embarque::class, 'eliminarVehiculoTransporte']);
        Route::post('ver-guias', [Embarque::class, 'viewGuias']);
        Route::post('agregar', [Embarque::class, 'agregarManifiesto']);
    });
    Route::prefix('usuarios')->group(function(){
        Route::get('agregar-modificar',[Usuario::class,'viewCrearModificar'])->name('usuAgreMod');
        Route::get('cambiar/rol/{id}', [Usuario::class, 'cambiarRol'])->name('cambiarRol');
        Route::get('miperfil', [Usuario::class, 'miPerfil'])->name('miPerfil');
        Route::post('agregar',[Usuario::class,'agregar']);
        Route::post('miperfil/modificar', [Usuario::class, 'miPerfilModificar']);
        Route::post('obtener', [Usuario::class, 'misUsuarios']);
        Route::post('obtener/roles', [Usuario::class, 'obtenerRoles']);
        Route::post('modificar',[Usuario::class,'modificarUsuarios']);
        Route::get('lista',[Usuario::class,'viewLista'])->name('usuLista');
        Route::get('logout',[Usuario::class,'cerrarSesion'])->name('closeSesion');
    });
    Route::prefix('cluster')->group(function () {
        Route::get('nuevo', [Cluster::class, 'crearCluster'])->name('crearCluster');
        Route::get('mostrar', [Cluster::class, 'mostrarClusters'])->name('mostrarClusters');
        Route::get('entregas', [Cluster::class, 'entregasClusters'])->name('verEntregaCluster');
        Route::post('agregar', [Cluster::class, 'agregarNuevoCluster']);
        Route::post('obtener', [Cluster::class, 'mostrarCluster']);
        Route::post('update', [Cluster::class, 'actualizarCluster']);
        Route::post('eliminar', [Cluster::class, 'eliminarCluster']);
        Route::post('obtener-provincia', [Manifiesto::class, 'obtenerClusterProv']);
        Route::post('modificar-pedidos', [Entrega::class, 'agregarPedidosEntregados']);
        Route::post('embarcar/por-retornar', [Embarque::class, 'obtenerDevolucionGuias']);
        Route::post('embarcar/retornar', [Embarque::class, 'retornarGuias']);
        Route::get('entregar-pedidos', [Entrega::class, 'entregaGuias'])->name('clusterEntregaPedidos');
        Route::get('embarcar/guias', [Entrega::class, 'devolverGuias'])->name('devolverGuias');

    });
    Route::prefix('empresa')->group(function () {
        Route::get('todo', [Embarque::class, 'vistaEmpresa'])->name('vistaEmpresa');
        Route::post('obtener', [Embarque::class, 'obtenerEmpresas']);
        Route::post('nuevo', [Embarque::class, 'agregarEmpresa']);
        Route::post('actualizar', [Embarque::class, 'actualizarEmpresa']);
        Route::post('eliminar', [Embarque::class, 'eliminarEmpresa']);

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
            Route::post('manifiesto/embarcar-fletes', [Embarque::class, 'verCiudadesFlestes']);
            Route::get('transporte', [Embarque::class, 'transporteEmbarque'])->name('embarqueTrabajo');
            Route::post('obtener-fletes', [Embarque::class, 'obtenerFletes']);
            Route::post('actualizar-estado', [Embarque::class, 'actualizarEmbarque']);
            Route::get('informe', [Manifiesto::class, 'viewProgEmba'])->name('viewProgEmba');
            Route::post('obtener-informe', [Embarque::class, 'obtenerInformeEmbarque']);
            Route::post('obtener-informe-detalle', [Embarque::class, 'obtenerInfoEmbDeta']);
            Route::post('agregar-informe', [Embarque::class, 'agregarInforme']);
            Route::post('obtener-guias-embarque-informe', [Embarque::class, 'obtenerGuiasEmbarque']);
        });
        Route::prefix('llegada')->group(function () {
            Route::get('/', [Llegada::class, 'viewLlegada'])->name('pedidoLlegada');
            Route::get('manual', [Llegada::class, 'viewLlegadaManual'])->name('embarquePorArribarManual');
            Route::post('embarques/arribar',[Llegada::class,'obtenerEmbarqueArribar']);
            Route::post('arribar-guias', [Llegada::class, 'actualizarGuiasLlegada']);
            Route::post('mis-guias', [Llegada::class, 'clusterMisGuias']);
            Route::post('cambiar-estado-guias', [Llegada::class, 'changeEstadoGuias']);
            Route::post('guia-detalle-cluster', [Llegada::class, 'detalleMisGuias']);
        });
        Route::prefix('entrega')->group(function () {
            Route::get('/', [Entrega::class, 'viewEntrega'])->name('pedidoEntrega');
            Route::post('lista', [Entrega::class, 'listaEntregas']);
        });
    });
    Route::prefix('comprobante')->group(function(){
        Route::prefix('guias')->group(function () {
            Route::get('/', [Gias::class, 'viewGuias'])->name('compGuias');
            Route::get('obtener-todos', [Gias::class, 'misGuias'])->name('misGuiasTotal');
            Route::get('view-retornadas', [Gias::class, 'viewGuiasRetornadas'])->name('verGuiasRetornadas');
            Route::post('retornadas', [Gias::class, 'guiasRetornadas']);
            Route::post('obtener-detalles', [Gias::class, 'detalleMisGuias']);
            Route::post('obtener-dias', [Gias::class, 'obtenerDiasTransito']);
            Route::post('asignar-personal', [Gias::class, 'asignarUsuario']);
            Route::post('cambiar-estado', [Gias::class, 'cambiarEstadoGuia']);
            Route::post('cambiar-cluster', [Gias::class, 'cambiarCluster']);
            Route::post('anular-guia', [Gias::class, 'anularGuia']);
            Route::post('mis-guias', [Gias::class, 'obtenerMisGuias']);
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
    Route::post('/login/restore', [Usuario::class, 'restablecerUsuario']);
    Route::post('/login/restore-cancelar', [Usuario::class, 'restablecerUsuarioCancelar']);
    Route::post('usuarios/autenticacion',[Usuario::class,'autenticacion']);
});
Route::get('consulta/guia')
Route::get('/',function(){
    return redirect(route('login'));
});

