<?php
require_once '../config/database.php';
require_once '../helpers/response.php';

// Validar que el HTTP request method sea GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJsonResponse(false, ['message' => 'Método HTTP no permitido'], 405);
}

try {
    // Consultar el count de registros totales de manera eficiente usando COUNT(*)
    $sql = "SELECT COUNT(*) as total FROM actividad";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    $result = $stmt->fetch();
    $total = (int) $result['total'];
    
    sendJsonResponse(true, [
        'total' => $total
    ], 200);

} catch (PDOException $e) {
    sendJsonResponse(false, [
        'message' => 'Error al obtener el recuento total de las actividades',
        'errors' => [$e->getMessage()]
    ], 500);
}
