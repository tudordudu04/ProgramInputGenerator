<?php
    include "decodeUserId.php";
    header('Content-Type: application/json');

    if($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: ".pg_last_error($conn)
        ]);
        exit;
    }


    $sqlQuery = "SELECT id, name FROM queries WHERE \"ownerId\" = $1";
    if(isset($_GET['id']))
        $result = pg_query_params($conn, $sqlQuery, array($_GET['id']));
    else if (isset($_POST['id'])){
        $sqlQuery = "SELECT * FROM queries WHERE id = $1";
        $result = pg_query_params($conn, $sqlQuery, array($_POST['id']));
        if(!$result){
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Queries not found"
            ]);
            exit;
        }
        $query = pg_fetch_assoc($result);
        $jsonData = json_decode($query['jsonData']);
        echo json_encode([
            "success" => true,
            "data" => $jsonData
        ]);
        exit;
    }
    else
        $result = pg_query_params($conn, $sqlQuery, array($userId));


    if(!$result){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Queries not found"
        ]);
        exit;
    }
    
    $queries = [];
    while($row = pg_fetch_assoc($result)){
        $queries[] = [
            'id' => $row['id'],
            'name' => $row['name']
        ];
    }

    echo json_encode($queries);
?>