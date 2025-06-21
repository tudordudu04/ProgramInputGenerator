<?php
    include "isAdmin.php";

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
    
    $checkUserQuery = "SELECT * FROM users WHERE id = $1";
    if(isset($_GET['id']))
        $checkUserResult = pg_query_params($conn, $checkUserQuery, array($friendId));
    else
        $checkUserResult = pg_query_params($conn, $checkUserQuery, array($userId));

    $checkProfileQuery = "SELECT * FROM profiles WHERE \"ownerId\" = $1";
    if(isset($_GET['id']))
        $checkProfileResult = pg_query_params($conn, $checkProfileQuery, array($friendId));
    else
        $checkProfileResult = pg_query_params($conn, $checkProfileQuery, array($userId));

    if($checkUserResult === false || $checkProfileResult === false){
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Database error: '.pg_last_error($conn)]);
        exit;
    }

    $rowUser = pg_fetch_assoc($checkUserResult);
    $rowProfile = pg_fetch_assoc($checkProfileResult);
    $profile = [
        'username'        => $rowUser['username'],
        'email'           => $rowUser['email'],
        'hidden'          => $rowProfile['hidden'] === 't' ? true : false,
        'profilePhotoUrl' => $rowProfile['profilePhotoUrl'],
        'firstName'       => $rowProfile['firstName'],
        'lastName'        => $rowProfile['lastName'],
        'phoneNumber'     => $rowProfile['phoneNumber'],
        'address'         => $rowProfile['address'],
        'country'         => $rowProfile['country'],
        'city'            => $rowProfile['city'],
        'isAdmin'         => $rowProfile['isAdmin'] === 't' ? true : false,
    ];
    echo json_encode($profile);
?>