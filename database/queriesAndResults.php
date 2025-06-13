<?php
    include "decodeUserId.php";
    
    if ($_SERVER["REQUEST_METHOD"] !== "POST"){
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid request.'
        ]);
        exit;
    }
    
    $sqlQuery = "INSERT INTO queries (\"ownerId\", \"jsonData\") VALUES ($1, $2)";
    $result = pg_query_params($conn, $sqlQuery, array($userId, $_POST['jsonData']));

    if ($result) {
        echo json_encode([
            'success' => true,
             'message' => 'Query saved successfully.'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
             'message' => 'Failed to save query.'.$userId
        ]);
    }

?>