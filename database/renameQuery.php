<?php
    include 'decodeUserId.php';
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: ".pg_last_error($conn)
        ]);
        exit;
    }

    $sqlQuery = "UPDATE queries SET name = $1 WHERE id = $2";
    $result = pg_query_params($conn, $sqlQuery, array($_POST['name'],$_POST['id']));

    if($result && pg_affected_rows($result) > 0){
        echo json_encode([
            "success" => true,
            "message" => "Query renamed successfully."
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Query not found"
        ]);
    }
?>