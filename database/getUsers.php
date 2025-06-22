<?php
    include 'isAdmin.php';
    header('Content-Type: application/json');

    if($_SERVER['REQUEST_METHOD'] !== 'GET'){
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request method.'
        ]);
        exit;
    }

    if(!$isAdmin){
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "User isn't admin."
        ]);
        exit;
    }

    $sqlQuery = "SELECT * FROM users WHERE id != $1";
    $response = pg_query_params($conn, $sqlQuery, array($userId));

    if(!$response){
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Couldn\'t retrieve users.'
        ]);
        exit;
    }

    $users = [];
    while($row = pg_fetch_assoc($response)){
        $users[] = [
            'id' => $row['id'],
            'username' => $row['username']
        ];
    }
    echo json_encode($users);
?>