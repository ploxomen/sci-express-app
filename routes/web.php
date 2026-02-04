<?php

use App\Http\Controllers\Clientes;
use App\Http\Controllers\Cluster;
use App\Http\Controllers\DestinatariosController;
use App\Http\Controllers\DiasTransito;
use App\Http\Controllers\Embarque;
use App\Http\Controllers\Entrega;
use App\Http\Controllers\Facturacion;
use App\Http\Controllers\FacturacionSUNATController;
use App\Http\Controllers\Fletes;
use App\Http\Controllers\Gias;
use App\Http\Controllers\Llegada;
use App\Http\Controllers\Manifiesto;
use App\Http\Controllers\MisComprobantesSUNATController;
use App\Http\Controllers\Recojo;
use App\Http\Controllers\Solicitud;
use App\Http\Controllers\Ubigeo;
use App\Http\Controllers\Usuario;
use App\Http\Controllers\VerificacionGuiaController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'share'])->prefix('intranet')->group(function () {
    Route::prefix('home')->group(function () {
        Route::post('manager', [Usuario::class, 'filtrosManager']);
    });
    Route::prefix('destinatario')->name('destinatario')->group(function () {
        Route::get('mostrar', [DestinatariosController::class, 'index'])->name('Mostrar');
        Route::post('obtener', [DestinatariosController::class, 'list']);
        Route::put('actualizar/{destinatario}', [DestinatariosController::class, 'update']);
        Route::post('agregar', [DestinatariosController::class, 'store']);
        Route::get('crear', [DestinatariosController::class, 'crear'])->name('Crear');
        Route::delete('eliminar/{destinatario}', [DestinatariosController::class, 'drop']);
        Route::get('agregar-modificar/{destinatario?}', [DestinatariosController::class, 'show']);
    });
    Route::prefix('cliente')->group(function () {
        Route::get('solicitud/agregar', [Clientes::class, 'agregarSolicitudCliente'])->name('solicitudCliente');
        Route::get('agregar-modificar', [Clientes::class, 'viewAgreModi'])->name('cliAgreModi');
        Route::get('archivos', [Clientes::class, 'viewArchivos'])->name('cliArchi');
        Route::get('mostrar', [Clientes::class, 'misClientes'])->name('viewMisClientes');
        Route::post('agregar', [Clientes::class, 'agregarCli']);
        Route::post('eliminar', [Clientes::class, 'eliminarCli']);
        Route::post('eliminar-proveedor', [Clientes::class, 'eliminarProveedor']);
        Route::post('obtener', [Clientes::class, 'obtenerClientes']);
        Route::post('obtener-editar', [Clientes::class, 'obtenerCliEdit']);
        Route::post('get-archivos', [Clientes::class, 'obtenerArchivosCli']);
        Route::post('change-files', [Clientes::class, 'cambiarEstadoArchi']);
        Route::post('documentos-verificacion', [Clientes::class, 'documentosObligatorios']);
    });
    Route::prefix('ubigeo')->group(function () {
        Route::post('departamentos', [Ubigeo::class, 'getDepartamentos']);
        Route::post('provincias', [Ubigeo::class, 'getProvincias']);
        Route::post('distritos', [Ubigeo::class, 'getDistritos']);
        Route::post('ciudades', [Ubigeo::class, 'dbCiudades']);
    });
    Route::prefix('manifiesto')->group(function () {
        Route::get('todos', [Manifiesto::class, 'viewManifiestosTodos'])->name('misManifiestos');
        Route::get('nuevo', [Manifiesto::class, 'viewNuevoManifiesto'])->name('viewNuevoManif');
        Route::post('actualizar', [Manifiesto::class, 'actualizarManifiesto']);
        Route::post('eliminar', [Manifiesto::class, 'eliminarManifiesto']);
        Route::post('ver-guias', [Manifiesto::class, 'viewGuias']);
        Route::post('agregar', [Manifiesto::class, 'agregarManifiesto']);
        Route::post('obtener-todos', [Manifiesto::class, 'obtenerTodosManifiestos']);
        Route::post('editar-obtener', [Manifiesto::class, 'obtenerUnManifiesto']);
        Route::post('eliminar-guia', [Manifiesto::class, 'eliminarGuiaManifiesto']);
        Route::post('agregar-guia-manual', [Manifiesto::class, 'agregarGuiaManual']);
        Route::get('reporte/{tipo}/{fechaInicio}/{fechaFin}', [Manifiesto::class, 'reportesManifiesto']);
    });
    Route::prefix('facturacion-sunat')->group(function () {
        Route::post('guia-remision-remitente', [FacturacionSUNATController::class, 'guiaRemisionRemitente']);
        Route::post('guia-remision-transportista', [FacturacionSUNATController::class, 'guiaRemisionTransportista']);
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
    Route::prefix('usuarios')->group(function () {
        Route::get('agregar-modificar', [Usuario::class, 'viewCrearModificar'])->name('usuAgreMod');
        Route::get('cambiar/rol/{id}', [Usuario::class, 'cambiarRol'])->name('cambiarRol');
        Route::get('miperfil', [Usuario::class, 'miPerfil'])->name('miPerfil');
        Route::post('agregar', [Usuario::class, 'agregar']);
        Route::post('obtener-centro-costo', [Usuario::class, 'getCentroCosto']);
        Route::post('miperfil/modificar', [Usuario::class, 'miPerfilModificar']);
        Route::post('obtener', [Usuario::class, 'misUsuarios']);
        Route::post('obtener/roles', [Usuario::class, 'obtenerRoles']);
        Route::post('modificar', [Usuario::class, 'modificarUsuarios']);
        Route::get('lista', [Usuario::class, 'viewLista'])->name('usuLista');
        Route::get('logout', [Usuario::class, 'cerrarSesion'])->name('closeSesion');
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
        Route::get('embarcar/guias-rechazadas/{guia}', [Embarque::class, 'guiaRechazada']);
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
    Route::prefix('verificacion')->name('verificacion')->group(function () {
        Route::get('mostrar', [VerificacionGuiaController::class, 'indexVerificacion'])->name('Crear');
        Route::post('guias', [VerificacionGuiaController::class, 'obtenerGuias']);
        Route::post('guias/opciones-denegar', [VerificacionGuiaController::class, 'opcionesDenegar']);
        Route::post('guias/denegar', [VerificacionGuiaController::class, 'desaprobarGuia']);
        Route::post('guias/aprobar', [VerificacionGuiaController::class, 'aprobarGuia']);
    });
    Route::prefix('pedidos')->group(function () {
        Route::prefix('recojos')->group(function () {
            Route::get('programar', [Recojo::class, 'viewPediReco'])->name('pedidoRecojoProgramar');
            Route::get('todos', [Recojo::class, 'viewRecojoAdmin'])->name('recojoAdmin');
            Route::get('retornos/cluster', [Recojo::class, 'recojoRetornoCluster'])->name('retornosCluster');
            Route::get('pendientes', [Recojo::class, 'recojoPendiente'])->name('recojoPendiTraba');
            Route::post('retornos/acciones', [Recojo::class, 'accionesRetornoEntrega']);
            Route::post('mis-recojos', [Recojo::class, 'verRecojosAdmin']);
            Route::post('modificaciones-admin', [Recojo::class, 'modificarAdminRecojo']);
            Route::post('actualizar-trabajador', [Recojo::class, 'actualizarRecoTra']);
            Route::post('obtener', [Recojo::class, 'obtenerRecojo']);
            Route::post('agregar', [Recojo::class, 'agregarRecojo']);
            Route::post('obtener-programacion', [Solicitud::class, 'obtenerParaProgramar']);
            Route::post('modificar', [Recojo::class, 'modificarRecojo']);
            Route::post('modificar-estados', [Recojo::class, 'modificarEstados']);
            Route::get('reporte/{tipo}/{fechaInicio}/{fechaFin}', [Recojo::class, 'reportesRecojo']);
        });
        Route::prefix('embarque')->group(function () {
            Route::get('todos', [Embarque::class, 'viewEmbarquesTodo'])->name('misEmbarques');
            Route::get('pendientes', [Embarque::class, 'viewListEmba'])->name('pedidoListEmbar');
            Route::post('obtener-todos', [Embarque::class, 'misEmbarques']);
            Route::post('obtener-detalle-informe-editar', [Embarque::class, 'verEditarInformeEmbarque']);
            Route::post('editar-embarque', [Embarque::class, 'editarEmbarque']);
            Route::post('editar-fletes', [Embarque::class, 'editarFletes']);
            Route::post('editar-informe', [Embarque::class, 'editarInforme']);
            Route::post('mostrar-editar-fletes', [Embarque::class, 'mostrarEditarFletes']);
            Route::post('eliminar', [Embarque::class, 'eliminarEmbarque']);
            Route::post('editar-obtener', [Embarque::class, 'mostrarEditarEmbarque']);
            Route::post('eliminar-manifiesto', [Embarque::class, 'eliminarManifiestoEmbarque']);
            Route::post('agregar-manifiesto', [Embarque::class, 'agregarManifiestoEmbarque']);
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
            Route::get('reporte/{tipo}/{fechaInicio}/{fechaFin}', [Embarque::class, 'reportesEmbarque']);
        });
        Route::prefix('llegada')->group(function () {
            Route::get('/', [Llegada::class, 'viewLlegada'])->name('pedidoLlegada');
            Route::get('manual', [Llegada::class, 'viewLlegadaManual'])->name('embarquePorArribarManual');
            Route::post('embarques/arribar', [Llegada::class, 'obtenerEmbarqueArribar']);
            Route::get('imagenes/{llegada}', [Llegada::class, 'verImagenesLegada']);

            Route::post('arribar-guias', [Llegada::class, 'actualizarGuiasLlegada']);
            Route::post('mis-guias', [Llegada::class, 'clusterMisGuias']);
            Route::post('cambiar-estado-guias', [Llegada::class, 'changeEstadoGuias']);
            Route::post('guia-detalle-cluster', [Llegada::class, 'detalleMisGuias']);
        });
        Route::prefix('entrega')->group(function () {
            Route::get('/', [Entrega::class, 'viewEntrega'])->name('pedidoEntrega');
            Route::post('lista', [Entrega::class, 'listaEntregas']);
            Route::post('actualizar', [Entrega::class, 'actualizarEntrega']);
            Route::get('reporte/{tipo}/{fechaInicio}/{fechaFin}/{cliente}/{cluster}', [Entrega::class, 'reporteEntregas']);
        });
    });
    Route::prefix('flete')->group(function () {
        Route::get('todos', [Fletes::class, 'viewMisFletes'])->name('misFletes');
        Route::post('ver', [Fletes::class, 'getFletes']);
        Route::post('eliminar', [Fletes::class, 'deleteFlete']);
        Route::post('actualizar', [Fletes::class, 'updateFlete']);
        Route::post('agregar', [Fletes::class, 'addFlete']);
    });
    Route::prefix('transito')->group(function () {
        Route::get('todos', [DiasTransito::class, 'viewDiasTransito'])->name('diasDeTransito');
        Route::post('ver', [DiasTransito::class, 'getDiasTransito']);
        Route::post('eliminar', [DiasTransito::class, 'deleteDiaTransito']);
        Route::post('actualizar', [DiasTransito::class, 'updateDiasTransito']);
        Route::post('agregar', [DiasTransito::class, 'addDiasTransito']);
    });
    Route::prefix('comprobante')->group(function () {
        Route::prefix('guias')->group(function () {
            Route::get('/', [Gias::class, 'viewGuias'])->name('compGuias');
            Route::get('pre', [Gias::class, 'viewPreGuias'])->name('generarPreGuias');
            Route::get('obtener-todos', [Gias::class, 'misGuias'])->name('misGuiasTotal');
            Route::get('completar', [Gias::class, 'viewComplearGuias'])->name('completarPreGuias');
            Route::get('view-retornadas', [Gias::class, 'viewGuiasRetornadas'])->name('verGuiasRetornadas');
            Route::get('seleccion', [Gias::class, 'guiasSeleccionadas']);
            Route::get('seleccionadas', [Gias::class, 'comprobantesClientes']);
            Route::get('consulta/{guia}', [Gias::class, 'consultaGuiaSunat']);
            Route::post('preguias-modificacion', [Gias::class, 'modificacionPreGuias']);
            Route::post('preguias-completar', [Gias::class, 'modificacionCompletarPreGuias']);
            Route::post('preguias-completar-editar', [Gias::class, 'editarCompletarPreGuias']);
            Route::post('obtener-asignacion-global', [Gias::class, 'obtenerAsignacionGlobal']);
            Route::post('asignar-global', [Gias::class, 'asignarMuchasGuiasUsuario']);
            Route::post('update-total-guia', [Gias::class, 'updateGuia']);
            Route::post('delete-img-guia', [Gias::class, 'deleteImgGuia']);
            Route::post('upload-img-entrega', [Gias::class, 'uploadImgDetalleGuia']);
            Route::post('obtener-detalle-tabla', [Gias::class, 'obtenerDetalleTabla']);
            Route::post('eliminar-detalle-guia', [Gias::class, 'eliminarDetalleGuia']);
            Route::post('retornadas', [Gias::class, 'guiasRetornadas']);
            Route::post('obtener-detalles', [Gias::class, 'detalleMisGuias']);
            Route::post('actualizar-informes', [Gias::class, 'actualizarInformes']);
            Route::post('obtener-dias', [Gias::class, 'obtenerDiasTransito']);
            Route::post('asignar-personal', [Gias::class, 'asignarUsuario']);
            Route::post('cambiar-estado', [Gias::class, 'cambiarEstadoGuia']);
            Route::post('cambiar-cluster', [Gias::class, 'cambiarCluster']);
            Route::post('anular-guia', [Gias::class, 'anularGuia']);
            Route::post('mis-guias', [Gias::class, 'obtenerMisGuias']);
            Route::post('obtener-clipro', [Gias::class, 'obtenerCliOrProvDeta']);
            Route::post('consulta-distante', [Gias::class, 'obtenerComplejoDistante']);
            Route::post('detalle-cli-de-cli', [Gias::class, 'detalleCliDeCli']);
            Route::post('agregar-guia', [Gias::class, 'addGuia']);
            Route::get('reporte/{tipo}/{fechaInicio}/{fechaFin}/{estado}', [Gias::class, 'reporteGuias']);
        });
        Route::prefix('sunat')->group(function () {
            Route::get('/', [MisComprobantesSUNATController::class, 'index']);
            Route::get('lista', [MisComprobantesSUNATController::class, 'obtenerComprobantes']);
            Route::get('sin-asignar', [MisComprobantesSUNATController::class, 'consultarComprobantesSinAsignar']);
            Route::post('asociar', [MisComprobantesSUNATController::class, 'asignarComprobanteSUNAT']);
            Route::get('consulta/{tipoImpresion}/{idGuia}', [MisComprobantesSUNATController::class, 'consultaComprobantePDFSUNAT']);
            Route::put('desasociar/{idGuia}', [MisComprobantesSUNATController::class, 'anulacionComprobanteSUNAT']);
        });
    });

    Route::prefix('facturacion')->group(function () {
        Route::get('guias/mostrar', [Facturacion::class, 'viewFacturacionGuia'])->name('facturacionMisGuias');
        Route::get('cluster/mostrar', [Facturacion::class, 'viewFacturacionCluster'])->name('facturacionMisClusters');
        Route::post('guias/ver', [Facturacion::class, 'obtenerFacturacionGuia']);
        Route::post('cluster/ver', [Facturacion::class, 'obtenerFacturacionClusterFac']);
        Route::get('guias/expor/excel', [Facturacion::class, 'exportarExcelFacturacionGuia'])->name('exportExcelFacturacionGuias');
        Route::prefix('configuracion')->group(function () {
            Route::get('cliente', [Facturacion::class, 'viewConfiguracionCliente'])->name('configFacturacionCliente');
            Route::get('cluster', [Facturacion::class, 'viewConfiguracionCluster'])->name('configFacturacionCluster');
            Route::post('acciones', [Facturacion::class, 'obtenerFacturacionCliente']);
            Route::post('cluster/acciones', [Facturacion::class, 'facturacionCluster']);
        });
    });

    Route::prefix('solicitud')->group(function () {
        Route::post('ver-proveedor', [Solicitud::class, 'obtenerProveedor']);
        Route::post('asignar-global', [Solicitud::class, 'asignacionGlobalSolicitud']);
        Route::post('actualizar/notificacion', [Solicitud::class, 'modificarEstadoNotificacion']);

        Route::post('obtener-cliente', [Solicitud::class, 'obtenerDetalleCli']);
        Route::post('obtener-cliente-cc', [Solicitud::class, 'obtnerCentroCostos']);
        Route::post('obtener-cliente-invitado', [Solicitud::class, 'obtenerDetalleCliInvitado']);
        Route::post('obtener', [Solicitud::class, 'obtenerSolicitud']);
        Route::post('asignar-personal', [Solicitud::class, 'asignarPersonal']);
        Route::post('ver-detalle', [Solicitud::class, 'verDetalleSoli']);
        Route::post('cambiar-estado', [Solicitud::class, 'cambiarEstadoSoli']);
        Route::post('editar', [Solicitud::class, 'editarSolicitud']);
        Route::post('eliminar-detalle', [Solicitud::class, 'deleteDetalle']);
        Route::post('eliminar-solicitud', [Solicitud::class, 'deleteSolicitud']);
        Route::post('nuevo', [Solicitud::class, 'insertarNuevaSoli']);
        Route::post('nuevo-cliente', [Solicitud::class, 'insertarNuevaSoliCliente']);
        Route::get('mostrar', [Solicitud::class, 'mostrarSolicitud'])->name('misSolicitudes');
        Route::get('agregar', [Solicitud::class, 'viewSolicitud'])->name('solicitud');
    });
    Route::get('storage/img/entrega/{filename}', [Entrega::class, 'obtenerImg'])->name('obtenerImgEntregas');
    Route::get('storage/img/retorno/{filename}', [Entrega::class, 'retornoImg'])->name('obtenerImgRetorno');

    Route::prefix('cliente/consulta/guia')->group(function () {
        Route::get('pendiente-general', [Clientes::class, 'viewConsultaTrabajador'])->name('consultaGuiaClienteTrabajaro');
        Route::get('pendiente-detallado', [Clientes::class, 'viewConsultaTrabajadorDetalle'])->name('consultaGuiaClienteTrabajadorDetallado');
        Route::get('/', [Clientes::class, 'viewConsultaGeneral'])->name('consultaClienteDetallada');
        Route::post('datos', [Gias::class, 'consultaGuia']);
        Route::post('remitentes', [Gias::class, 'consultaGuiaRemitentes']);
        Route::get('remitente', [Clientes::class, 'viewConsultaDetallada'])->name('consultaClienteGeneral');
    });
    Route::get('inicio', [Usuario::class, 'viewHome'])->name('home');
});
Route::middleware(['guest'])->prefix('intranet')->group(function () {
    Route::get('login', [Usuario::class, 'viewLogin'])->name('login');
    Route::post('/login/restore', [Usuario::class, 'restablecerUsuario']);
    Route::post('/login/restore-cancelar', [Usuario::class, 'restablecerUsuarioCancelar']);
    Route::post('usuarios/autenticacion', [Usuario::class, 'autenticacion']);
});
Route::get('/', function () {
    return redirect(route('login'));
});
