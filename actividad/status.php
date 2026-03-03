<?php
require_once '../config/database.php';
require_once '../helpers/response.php';

// Validar que el método sea PATCH (o PUT según preferencias REST) para actualizaciones parciales
if (!in_array($_SERVER['REQUEST_METHOD'], ['PUT', 'PATCH'])) {
    sendJsonResponse(false, ['message' => 'Método HTTP no permitido'], 405);
}

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (!$data) {
    sendJsonResponse(false, ['message' => 'Cuerpo JSON inválido o vacío'], 400);
}

// Obtenemos los valores
$id = $data['id'] ?? null;
$estatus = $data['estatus'] ?? null; 
$errors = [];

// REGLAS DE VALIDACIÓN
// El id debe existir y ser entero positivo
if ($id === null) {
    $errors[] = 'El id es obligatorio.';
} elseif (!is_numeric($id) || (int)$id <= 0) {
    $errors[] = 'El id debe ser un número entero positivo.';
}

// El estatus si se manda, solo admite ACTIVO y DESACTIVADO
if ($estatus !== null && !in_array($estatus, ['ACTIVO', 'DESACTIVADO'])) {
    $errors[] = 'El estatus permitido solo es ACTIVO o DESACTIVADO.';
}

if (!empty($errors)) {
    sendJsonResponse(false, [
        'message' => 'Errores de validación en la carga de datos',
        'errors' => $errors
    ], 400);
}

$id = (int) $id;

try {
    // 1. Verificamos que la actividad con este ID efectivamente exista
    $stmtCheck = $pdo->prepare("SELECT id, estatus FROM actividad WHERE id = :id");
    $stmtCheck->execute([':id' => $id]);
    $actividad = $stmtCheck->fetch();

    if (!$actividad) {
        // Retornamos 404 (Not Found)
        sendJsonResponse(false, [
            'message' => 'La actividad solicitada no existe',
            'errors' => ['Resource Not Found']
        ], 404);
    }

    // 2. Si no se especificó un estatus deseado, procedemos a cambiar el actual por el adverso (Alternar ACTIVO <-> DESACTIVADO)
    if ($estatus === null) {
        $estatus = ($actividad['estatus'] === 'ACTIVO') ? 'DESACTIVADO' : 'ACTIVO';
    }

    // 3. Efectuamos el UPDATE
    // Aprovechando MariaDB, un UPDATE a un contenido ignorará escribir la celda si el valor ya es idéntico,
    // ahorrando writes. Adicionalmente, updated_at se actualizará on update gracias al setup inicial de la DD.
    $stmtUpdate = $pdo->prepare("UPDATE actividad SET estatus = :estatus WHERE id = :id");
    $stmtUpdate->execute([
        ':estatus' => $estatus,
        ':id' => $id
    ]);

    // Retorna la actualización exitosa (200 OK)
    sendJsonResponse(true, [
        'message' => 'El estatus fue actualizado correctamente.',
        'data' => [
            'id' => $id,
            'estatus' => $estatus
        ]
    ], 200);

} catch (PDOException $e) {
    sendJsonResponse(false, [
        'message' => 'Ocurrió un error en el servidor al actualizar el estatus',
        'errors' => [$e->getMessage()]
    ], 500);
}
