<?php
    include "decodeUserId.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: ".pg_last_error($conn)
        ]);
        exit;
    }

    header('Content-Type: application/json');


    $friends = [];
    $sqlQuery = "SELECT username FROM users s JOIN friends f on s.id = f.id2 WHERE f.id1 = $1";
    $result = pg_query_params($conn, $sqlQuery, array($userId));

    while($row = pg_fetch_assoc($result)){
        $friends[] = $row['username'];
    }

    $sqlQuery = "SELECT username FROM users s JOIN friends f on s.id = f.id1 WHERE f.id2 = $1";
    $result = pg_query_params($conn, $sqlQuery, array($userId));
    
    while($row = pg_fetch_assoc($result)){
        $friends[] = $row['username'];
    }

    $friend_requests = [];
    $sqlQuery = "SELECT username FROM users s JOIN friend_requests f ON s.id = f.id1 WHERE f.id2 = $1";
    $result = pg_query_params($conn, $sqlQuery, array($userId));

    while($row = pg_fetch_assoc($result)){
        $friend_requests[] = $row['username'];
    }
    
    $response = [
        'friends' => $friends,
        'friend_requests' => $friend_requests
    ];
    echo json_encode($response);
?>