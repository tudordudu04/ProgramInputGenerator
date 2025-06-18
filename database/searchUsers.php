<?php
    include "decodeUserId.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([]);
        exit;
    }

    header('Content-Type: application/json');

    $sqlQuery = "SELECT username FROM users s JOIN profiles p ON s.id = p.\"ownerId\" WHERE s.id != $1 AND p.hidden = FALSE";
    $result = pg_query_params($conn, $sqlQuery, array($userId));

    if(!$result){
        echo json_encode([]);
        exit;
    }

    $people = [];
    while ($row = pg_fetch_assoc($result)) {
        $people[] = $row['username'];
    }

    $q = isset($_POST['query']) ? trim($_POST['query']) : '';
    $suggestions = [];

    if ($q !== '') {
        $qLower = strtolower($q);
        foreach ($people as $person) {
            if (stripos($person, $qLower) !== false) {
                $suggestions[] = $person;
            }
        }
    }

    echo json_encode($suggestions);
?>