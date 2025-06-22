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

    if(isset($_GET['id'])){
        $friendId = $_GET['id'];
        $sql = "SELECT 1 FROM friends WHERE (id1 = $1 AND id2 = $2) OR (id1 = $2 AND id2 = $1) LIMIT 1";
        $result = pg_query_params($conn, $sql, array($userId, $friendId));
        if (pg_num_rows($result) === 0 && !$isAdmin) {
            http_response_code(401);
            echo json_encode([
                "success" => false,
                "message" => "Access denied."]);
            exit;
        }
    }
    
    $sqlQuery = "SELECT * FROM users s JOIN profiles p ON s.id = p.\"ownerId\" WHERE s.id = $1";
    if(isset($_GET['id']))
        $result = pg_query_params($conn, $sqlQuery, array($friendId));
    else
        $result = pg_query_params($conn, $sqlQuery, array($userId));

    if(!$result){
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: '.pg_last_error($conn)]);
        exit;
    }

    $row = pg_fetch_assoc($result);
    $profile = [
        'username'        => $row['username'],
        'email'           => $row['email'],
        'hidden'          => $row['hidden'] === 't' ? true : false,
        'profilePhotoUrl' => $row['profilePhotoUrl'],
        'firstName'       => $row['firstName'],
        'lastName'        => $row['lastName'],
        'phoneNumber'     => $row['phoneNumber'],
        'address'         => $row['address'],
        'country'         => $row['country'],
        'city'            => $row['city'],
        'isAdmin'         => $row['isAdmin'] === 't' ? true : false,
    ];
    echo json_encode($profile);
?>