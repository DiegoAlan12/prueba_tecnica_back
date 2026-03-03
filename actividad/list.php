<?php
require_once '../config/database.php';
require_once '../helpers/response.php';

// Validar que el método HTTP sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(false, ['message' => 'Método HTTP no permitido'], 405);
}

try {
    // Consultamos todas las actividades. Se requiere listar por id desc o created_at desc.
    $sql = "SELECT id, titulo, descripcion, estatus, created_at, updated_at 
            FROM actividad 
            ORDER BY id DESC";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    // Obtenemos todos los registros como un array asociativo
    $actividades = $stmt->fetchAll();
    
    sendJsonResponse(true, [
        'data' => $actividades
    ], 200);

} catch (PDOException $e) {
    sendJsonResponse(false, [
        'message' => 'Error al intentar listar las actividades',
        'errors' => [$e->getMessage()]
    ], 500);
}
