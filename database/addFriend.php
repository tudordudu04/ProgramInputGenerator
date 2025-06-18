<?php
    include "decodeUserId.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: ".pg_last_error($conn)
        ]);
        exit;
    }

    header('Content-Type: application/json');


    $sqlQuery = "SELECT id FROM users WHERE username = $1";
    $result = pg_query_params($conn, $sqlQuery, array($_POST['username']));

    if(!$result){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "User not found"
        ]);
        exit;
    }

    $row = pg_fetch_assoc($result);

    if($_POST['request'] === 'true')
        $sqlQuery = "INSERT INTO friend_requests (id1, id2) VALUES ($1, $2)";
    else
        $sqlQuery = "INSERT INTO friends (id1, id2) VALUES ($1, $2)";
     
    $result = pg_query_params($conn, $sqlQuery, array($userId, $row['id']));

    if(!$result){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: ".pg_last_error($conn)
        ]);
        exit;
    }

    else {
        echo json_encode([
            "success" => true,
            "message" => "Friend request sent!"
        ]);
        exit;
    }
?>
