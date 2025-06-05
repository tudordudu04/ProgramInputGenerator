<?php
    require_once "../util/vendor/autoload.php";
    include '../database/db_connection.php';

    use Firebase\JWT\Key;
    use Firebase\JWT\JWT;
    $key = "cevacevacevacevacevacevacevacevacevacevacevacevacevacevacevaceva";
    header('Content-Type: application/json');

    if (!isset($_COOKIE['jwt'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not authenticated']);
        exit;
    }
    $jwt = $_COOKIE['jwt'];

    if ($_SERVER["REQUEST_METHOD"] !== "POST"){
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid request.'
        ]);
        exit;
    }

    try {
        $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
        $userId = $decoded->user_id;

        } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid token']);
    }

    if (!$userId) {
        echo json_encode([
            "success" => false,
            "message" => "User ID not found in token."
        ]);
        exit;
    }
    
    $sqlQuery = "INSERT INTO queries (\"ownerId\", \"jsonData\") VALUES ($1, $2)";
    $result = pg_query_params($conn, $sqlQuery, array($userId, $_POST['jsonData']));

    if ($result) {
        echo json_encode([
            'success' => true,
             'message' => 'Query saved successfully.'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
             'message' => 'Failed to save query.'
        ]);
    }

?>