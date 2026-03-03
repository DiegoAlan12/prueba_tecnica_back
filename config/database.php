<?php
// Configuración de la base de datos y conexión PDO
$host = 'localhost';
$db_name = 'Expediente_de_Datos';
$username = 'root'; // Usuario por defecto de XAMPP
$password = '';     // Contraseña por defecto de XAMPP (vacía)

try {
    // Definimos el DSN con charset utf8mb4 para soportar todo tipo de caracteres (como emojis)
    $dsn = "mysql:host={$host};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Manejo de errores basado en excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Resultados siempre devolviendo un arreglo asociativo por defecto
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactivamos la emulación de prepares para mejor seguridad y delegar al motor
    ];

    // Instanciamos el objeto PDO que será usado en el resto de la aplicación
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    // Exigimos respuestas JSON incluso si falla la DB
    http_response_code(500);
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        'ok' => false,
        'message' => 'Error de conexión a la base de datos',
        'errors' => [$e->getMessage()] // NOTA: En un ambiente de producción real no deberíamos exponer mensajes internos del motor
    ]);
    exit;
}
