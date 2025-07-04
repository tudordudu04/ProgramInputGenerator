<?php
    include "decodeUserId.php";
    header('Content-Type: application/json');
    
    if ($_SERVER["REQUEST_METHOD"] !== "POST"){
        http_response_code(401);
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid request.'
        ]);
        exit;
    }
    
    if (isset($_POST['id'])) {
        $sqlQuery = "SELECT \"jsonData\", \"name\" FROM queries WHERE id = $1";
        $result = pg_query_params($conn, $sqlQuery, array($_POST['id']));

        if ($row = pg_fetch_assoc($result)) {
            $sqlInsert = "INSERT INTO queries (\"ownerId\", \"jsonData\", \"name\") VALUES ($1, $2, $3)";
            $insertResult = pg_query_params($conn, $sqlInsert, array($userId, $row['jsonData'], $row['name']));
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Query not found.'
            ]);
        }
    } else {
        $sqlInsert = "INSERT INTO queries (\"ownerId\", \"jsonData\") VALUES ($1, $2)";
        $result = pg_query_params($conn, $sqlInsert, array($userId, $_POST['jsonData']));
    }

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