<?php
require_once '../config/database.php';
require_once '../helpers/response.php';

// Validar que el método HTTP sea POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, ['message' => 'Método no permitido'], 405);
}

// Obtener los datos del cuerpo de la petición. json_decode nos ayuda a leer payload formato JSON
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    sendJsonResponse(false, ['message' => 'Cuerpo JSON inválido o vacío'], 400);
}

// Extraemos los valores. Usamos trim para eliminar espacios accidentales al inicio y fin
$titulo = trim($data['titulo'] ?? '');
$descripcion = trim($data['descripcion'] ?? '');

$errors = [];

// REGLAS DE VALIDACIÓN
// Título obligatorio, longitud entre 3 y 150 caracteres
if (empty($titulo)) {
    $errors[] = 'El título es obligatorio.';
} elseif (strlen($titulo) < 3 || strlen($titulo) > 150) {
    $errors[] = 'El título debe tener entre 3 y 150 caracteres.';
}

// Descripción obligatoria, al menos 5 caracteres
if (empty($descripcion)) {
    $errors[] = 'La descripción es obligatoria.';
} elseif (strlen($descripcion) < 5) {
    $errors[] = 'La descripción debe tener al menos 5 caracteres.';
}

// Si la validación falla (400)
if (!empty($errors)) {
    sendJsonResponse(false, [
        'message' => 'Errores de validación en los datos provistos',
        'errors' => $errors
    ], 400);
}

try {
    // PREPARED STATEMENTS: Se usan sentencias preparadas para prevenir SQL Injection, 
    // separando la consulta de la información insertada por el usuario.
    // Omitimos 'estatus' ya que por defecto MariaDB asignará 'ACTIVO'
    $sql = "INSERT INTO actividad (titulo, descripcion) VALUES (:titulo, :descripcion)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ':titulo' => $titulo,
        ':descripcion' => $descripcion
    ]);
    
    // Obtenemos el ID último insertado de la base de datos
    $newId = (int) $pdo->lastInsertId();
    
    // Retornamos éxito (201 Created)
    sendJsonResponse(true, [
        'message' => 'Actividad creada con éxito',
        'data' => [
            'id' => $newId,
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'estatus' => 'ACTIVO'
        ]
    ], 201);
    
} catch (PDOException $e) {
    // En caso de que falle la operación del servidor o DB respondemos con status HTTP 500
    sendJsonResponse(false, [
        'message' => 'Ocurrió un error al persistir la actividad',
        'errors' => [$e->getMessage()]
    ], 500);
}
