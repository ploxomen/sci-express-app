<?php
namespace App\Services;
class UbigeoService{
    protected $ubigeos;

    public function __construct()
    {
        $path = storage_path('app/ubigeo/ubigeos.json');
        $this->ubigeos = json_decode(file_get_contents($path), true);
    }
    private function normalize($value)
    {
        $value = trim($value);
        $value = mb_strtoupper($value, 'UTF-8');

        // Quitar acentos
        $replacements = [
            'Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U',
            'Ü' => 'U', 'Ñ' => 'N'
        ];

        return strtr($value, $replacements);
    }

    /**
     * Obtener ubigeo por departamento, provincia y distrito
     */
    public function obtenerNumeroUbigeo($departamento, $provincia, $distrito)
    {
        $departamento = $this->normalize($departamento);
        $provincia = $this->normalize($provincia);
        $distrito = $this->normalize($distrito);

        foreach ($this->ubigeos as $item) {

            $itemDept = $this->normalize($item['NOMDPTO']);
            $itemProv = $this->normalize($item['NOMPROV']);
            $itemDist = $this->normalize($item['NOMDIST']);

            if (
                $itemDept === $departamento &&
                $itemProv === $provincia &&
                $itemDist === $distrito
            ) {
                return str_pad($item['CODIGO_UBIGEO'],6,"0",STR_PAD_LEFT); // ubigeo encontrado
            }
        }

        return null; // no encontrado
    }
}