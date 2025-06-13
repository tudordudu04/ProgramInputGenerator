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
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $city = trim($_POST['city'] ?? '');

    try {
        $sqlQuery = "UPDATE profiles
             SET \"firstName\" = $1,
                 \"lastName\" = $2,
                 \"phoneNumber\" = $3,
                 \"address\" = $4,
                 \"country\" = $5,
                 \"city\" = $6
             WHERE \"ownerId\" = $7";
        $result = pg_query_params($conn, $sqlQuery, array($firstName, $lastName, $phoneNumber, $address, $country, $city, $userId));
        $sqlQuery = "UPDATE users
             SET \"username\" = $1,
                 \"email\" = $2
             WHERE \"id\" = $3";
        $secondResult = pg_query_params($conn, $sqlQuery, array($username, $email, $userId));
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
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
?>