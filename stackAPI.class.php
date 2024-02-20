<?php

// Definición de la clase stackAPI para interactuar con la API de Pexels
class stackAPI
{
    // Propiedad privada para almacenar la clave de API de Pexels
    private $apiKey;

    // Constructor de la clase: se ejecuta automáticamente al instanciar la clase
    function __construct()
    {
        // Asignación de la clave de API a la propiedad privada $apiKey
        // La clave de API se necesita para autenticar las solicitudes a la API de Pexels
        $this->apiKey = "tu-api";
    }

    // Método público para obtener imágenes de Pexels, ya sea a través de una selección curada o mediante búsqueda con palabras clave
    public function get_stack($type = "curated", $qry = [])
    {
        // Construcción de la URL base para la solicitud a la API, utilizando el tipo de solicitud especificado
        $url = "https://api.pexels.com/v1/{$type}";

        // Inicialización de la variable que almacenará los parámetros de la consulta
        $http_build = "";
        // Si el arreglo $qry no está vacío, se convierte en una cadena de consulta URL codificada
        if (!empty($qry))
            $http_build = "?" . http_build_query($qry);

        // Inicialización de una sesión cURL
        $curl = curl_init();
        // Configuración de la URL completa (incluyendo parámetros de consulta) para la solicitud cURL
        curl_setopt($curl, CURLOPT_URL, "{$url}{$http_build}");
        // Configuración de cURL para que retorne el resultado de la solicitud, en lugar de mostrarlo directamente
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Añadiendo el encabezado HTTP necesario para la autenticación, utilizando la clave de API
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: {$this->apiKey}"
        ]);

        // Ejecución de la solicitud cURL y almacenamiento del resultado
        $result = curl_exec($curl);
        // Obtención del código de estado HTTP de la respuesta
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        // Cierre de la sesión cURL
        curl_close($curl);

        // Verificación del código de estado HTTP para determinar el estado de la solicitud
        $status = ($httpCode == 200) ? "success" : "error";

        // Retorno de un arreglo asociativo con el estado de la solicitud, el código de estado HTTP y el resultado de la solicitud
        // El resultado se decodifica de JSON a un arreglo PHP
        return [
            "status" => $status,
            "status_code" => $httpCode,
            "result" => json_decode($result, true)
        ];
    }
}
