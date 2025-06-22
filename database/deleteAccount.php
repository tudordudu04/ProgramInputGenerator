<?php
    include 'isAdmin.php';
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }

    $sqlQuery = "DELETE FROM users WHERE id = $1";
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
        if($isAdmin)
            $result = pg_query_params($conn, $sqlQuery, array($_POST['userId']));
        else{
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'User isnt an admin.'
            ]);
        }
    else 
        $result = pg_query_params($conn, $sqlQuery, array($userId));

    if ($result) {
        if($_SERVER['REQUEST_METHOD'] === 'GET')
            setcookie('jwt', '', -1, '/');
        echo json_encode([
            'success' => true,
             'message' => 'Account deleted successfully.'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
             'message' => 'Failed to delete account: '.$userId
        ]);
    }
?>