<?php
    include 'decodeUserId.php';
    header('Content-Type: application/json');

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: ".pg_last_error($conn)
        ]);
        exit;
    }

    $title = trim($_POST['ticketTitle']);
    $type = trim($_POST['ticketReason']);
    $body = trim($_POST['ticketBody']);

    $sqlQuery = "INSERT INTO tickets (\"ownerId\", title, type, body) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $sqlQuery, array($userId, $title, $type, $body));

    if($result){
        echo json_encode([
            "success" => true,
            "message" => "Ticket submitted succesfully"
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Ticket couldn't be submitted."
        ]);
    }
    
?>