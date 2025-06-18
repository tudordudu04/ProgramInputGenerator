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

    $sqlQuery = "SELECT id, name FROM queries WHERE \"ownerId\" = $1";
    if(isset($_GET['forUserId'])) 
        $result = pg_query_params($conn, $sqlQuery, array($_GET['forUserId']));
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

    // if(sizeof($queries) === 0)
    //     echo json_encode('');
    echo json_encode($queries);
?>