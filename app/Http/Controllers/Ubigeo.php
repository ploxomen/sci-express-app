<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Ubigeo extends Controller
{
    public function getDepartamentos(Request $request)
    {
        if ($request->ajax()) {
            $url = storage_path('app/ubigeo/departamentos.json');
            $departamentos = file_get_contents($url);
            return response(json_decode($departamentos));
        }
    }
    public function getNameDepartamento($ubigeo)
    {
        $url = storage_path('app/ubigeo/departamentos.json');
        $departamentos = file_get_contents($url);
        $departamentos = json_decode($departamentos);
        foreach ($departamentos as $dep) {
            if ($dep->id_ubigeo == $ubigeo) {
                return $dep->nombre_ubigeo;
            }
        }
        return '';
    }
    public function getNameProvincia($ubigeo)
    {
        $url = storage_path('app/ubigeo/provincias.json');
        $provincias = json_decode(file_get_contents($url));
        foreach ($provincias as $k => $provincia) {
            foreach ($provincia as $data) {
                if ($data->id_ubigeo == $ubigeo) {
                    return $data->nombre_ubigeo;
                }
            }
        }
        return '';
    }
    public function getNameDistrito($ubigeo)
    {
        $url = storage_path('app/ubigeo/distritos.json');
        $distritos = json_decode(file_get_contents($url));
        foreach ($distritos as $k => $distrito) {
            foreach ($distrito as $data) {
                if ($data->id_ubigeo == $ubigeo) {
                    return $data->nombre_ubigeo;
                }
            }
        }
        return '';
    }
    public function getProvincias(Request $request)
    {
        if ($request->ajax()) {
            $url = storage_path('app/ubigeo/provincias.json');
            $id_ubigeo = $request->post('id_ubigeo');
            $provincias = json_decode(file_get_contents($url));
            $response = [];
            foreach ($provincias as $k => $provincia) {
                if ($k == $id_ubigeo) {
                    $response = $provincia;
                    break;
                }
            }
            return response($response);
        }
    }
    public function getDepartamentosClientes()
    {
        $url = storage_path('app/ubigeo/departamentos.json');
        $departamentos = file_get_contents($url);
        return json_decode($departamentos);
    }
    public function getTodosProvinciasCliente()
    {
        $url = storage_path('app/ubigeo/provincias.json');
        return json_decode(file_get_contents($url));
        
    }
    public static function getProvinciasCliente($ubigeo)
    {
        $url = storage_path('app/ubigeo/provincias.json');
        $provincias = json_decode(file_get_contents($url));
        $response = [];
        foreach ($provincias as $k => $provincia) {
            if ($k == $ubigeo) {
                $response = $provincia;
                break;
            }
        }
        return $response;
    }
    public static function getDistritosCliente($ubigeo)
    {
        $url = storage_path('app/ubigeo/distritos.json');
        $provincias = json_decode(file_get_contents($url));
        $response = [];
        foreach ($provincias as $k => $distrito) {
            if ($k == $ubigeo) {
                $response = $distrito;
                break;
            }
        }
        return $response;
    }
    public function getDistritos(Request $request)
    {
        if ($request->ajax()) {
            $url = storage_path('app/ubigeo/distritos.json');
            $id_ubigeo = $request->post('id_ubigeo');
            $distritos = json_decode(file_get_contents($url));
            $response = [];
            foreach ($distritos as $k => $distrito) {
                if ($k == $id_ubigeo) {
                    $response = $distrito;
                    break;
                }
            }
            return response($response);
        }
    }
}
