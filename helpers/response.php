<?php
// Habilitamos CORS para todas las respuestas (al ser una API REST y posiblemente consumida por un frontend en otro host)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/**
 * Función helper para enviar la respuesta JSON con consistencia en todos los endpoints
 * 
 * @param bool $ok Indica si la petición fue exitosa o no
 * @param array $data Opcional: la data que se regresa en el response, o mensajes/errores complementarios
 * @param int $statusCode Opcional: código HTTP de respuesta, default 200
 */
function sendJsonResponse(bool $ok, array $data = [], int $statusCode = 200) {
    http_response_code($statusCode);
    
    // Inicializamos con el estado base
    $response = ['ok' => $ok];
    
    // Mezclamos con la demás información. Usamos iteración en lugar de array_merge para asegurar la posición de 'ok'
    foreach ($data as $key => $value) {
        $response[$key] = $value;
    }
    
    echo json_encode($response);
    exit; // Aseguramos detener ejecución del script posterior a dar la respuesta
}

// Manejo de peticiones OPTIONS que envía el navegador antes de un PUT/POST por CORS (Preflight request)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}
