<?php
    require_once "../util/vendor/autoload.php";
    include '../database/db_connection.php';
    header('Content-Type: application/json');

    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;

    $dotenv = Dotenv\Dotenv::createImmutable('../');
    $dotenv->load();

    if (!isset($_COOKIE['jwt'])) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "User not authenticated."
        ]);
        exit;
    }

    $key = $_ENV['JWT_SECRET'];
    $jwt = $_COOKIE['jwt'];

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userId = $decoded->user_id ?? null;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid authentication token."
        ]);
        exit;
    }

    if (!$userId) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "User ID not found in token."
        ]);
        exit;
    }
?>