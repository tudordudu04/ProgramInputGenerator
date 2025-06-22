<!-- Placeholder until we actually make the inputs :PP -->
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
    
    $sqlQuery = "INSERT INTO results (\"ownerId\", body) VALUES ($1, $2)";
    $result = pg_query_params($conn, $sqlQuery, array($userId, $_POST['inputs']));

    if ($result) {
        echo json_encode([
            'success' => true,
             'message' => 'Inputs saved successfully.'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
             'message' => 'Failed to save inputs.'.$userId
        ]);
    }

?>