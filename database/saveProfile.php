<?php
    include "decodeUserId.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }
    // Aici pune validari mai bune
    if(trim($_POST['hidden'] ?? '') === 'on')
        $hidden = 'TRUE';
    else $hidden = 'FALSE';  
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');

    $sqlQuery = "UPDATE profiles
            SET \"firstName\" = $1,
                \"lastName\" = $2,
                \"phoneNumber\" = $3,
                \"address\" = $4,
                \"country\" = $5,
                \"city\" = $6,
                \"hidden\" = $7
            WHERE \"ownerId\" = $8";
    $result = pg_query_params($conn, $sqlQuery, array($firstName, $lastName, $phoneNumber, $address, $country, $city, $hidden, $userId));
    $sqlQuery = "UPDATE users
            SET \"username\" = $1,
                \"email\" = $2
            WHERE \"id\" = $3";
    $secondResult = pg_query_params($conn, $sqlQuery, array($username, $email, $userId));

    //aici ar trebui sa dau rollback cumva daca nu iese unul din query-uri
    if ($result && $secondResult) {
        echo json_encode([
            "success" => true,
            "message" => "Profile updated successfully."
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Database error: " . pg_last_error($conn)
        ]);
    }
?>