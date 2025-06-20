<?php
    include "decodeUserId.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        echo json_encode([
            "success" => false,
            "message" => "Invalid request method."
        ]);
        exit;
    }

    $sqlQuery = "DELETE FROM users WHERE id = $1";
    $result = pg_query_params($conn, $sqlQuery, array($userId));

    if ($result) {
        setcookie('jwt', '', -1, '/');
        echo json_encode([
            'success' => true,
             'message' => 'Account deleted successfully.'
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
             'message' => 'Failed to delete account.'.$userId
        ]);
    }
?>