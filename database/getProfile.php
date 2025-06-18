<?php
    include "decodeUserId.php";

    try {
        $checkUserQuery = "SELECT * FROM users WHERE id = $1";
        $checkUserResult = pg_query_params($conn, $checkUserQuery, array($userId));

        $checkProfileQuery = "SELECT * FROM profiles WHERE \"ownerId\" = $1";
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
        
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(['error' => 'Database error: '.pg_last_error($conn)]);
    }

?>