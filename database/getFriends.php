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


    $sqlQuery = "SELECT username FROM users s JOIN friends f on s.id = f.id2 WHERE f.id1 = $1";
    $result = pg_query_params($conn, $sqlQuery, array($userId));

    if(!$result){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "User not found"
        ]);
        exit;
    }

    $people = [];
    while($row = pg_fetch_assoc($result)){
        $people[] = $row['username'];
    }

    $sqlQuery = "SELECT username FROM users s JOIN friends f on s.id = f.id1 WHERE f.id2 = $1";
    $result = pg_query_params($conn, $sqlQuery, array($userId));
    
    while($row = pg_fetch_assoc($result)){
        $people[] = $row['username'];
    }
    
    if(sizeof($people) === 0)
        echo json_encode('');
    else echo json_encode($people);
?>