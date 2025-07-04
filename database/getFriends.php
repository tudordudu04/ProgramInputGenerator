<?php
    include "isAdmin.php";
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: ".pg_last_error($conn)
        ]);
        exit;
    }
    $ok = false;
    $friends = [];
    $sqlQuery = "SELECT s.id, s.username FROM users s JOIN friends f ON s.id = f.id2 WHERE f.id1 = $1";
    if(isset($_GET['id']) && $_GET['id'] !== '')
        $result = pg_query_params($conn, $sqlQuery, array($_GET['id']));
    else 
        $result = pg_query_params($conn, $sqlQuery, array($userId));
    while($row = pg_fetch_assoc($result)){
        if($row['id'] === $userId)
            $ok = true;
        $friends[] = [
            'id' => $row['id'],
            'username' => $row['username']
        ];
    }

    $sqlQuery = "SELECT s.id, s.username FROM users s JOIN friends f ON s.id = f.id1 WHERE f.id2 = $1";
    if(isset($_GET['id']) && $_GET['id'] !== '')
        $result = pg_query_params($conn, $sqlQuery, array($_GET['id']));
    else 
        $result = pg_query_params($conn, $sqlQuery, array($userId));
    while($row = pg_fetch_assoc($result)){
        if($row['id'] === $userId)
            $ok = true;
        $friends[] = [
            'id' => $row['id'],
            'username' => $row['username']
        ];
    }

    if(isset($_GET['id']) && $_GET['id'] !== ''){
        if($ok || $isAdmin){
        echo json_encode($friends);
        exit;
        } else {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Not friends with user."
            ]);
            exit;
        }
    }

    $friend_requests = [];
    $sqlQuery = "SELECT s.id, s.username FROM users s JOIN friend_requests f ON s.id = f.id1 WHERE f.id2 = $1";
    $result = pg_query_params($conn, $sqlQuery, array($userId));
    while($row = pg_fetch_assoc($result)){
        $friend_requests[] = [
            'id' => $row['id'],
            'username' => $row['username']
        ];
    }

    $response = [
        'friends' => $friends,
        'friend_requests' => $friend_requests
    ];
    echo json_encode($response);
?>