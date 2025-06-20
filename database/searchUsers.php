<?php
    include "decodeUserId.php";

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([]);
        exit;
    }

    header('Content-Type: application/json');

    $sqlQuery = "SELECT s.username FROM users s
                 JOIN profiles p ON s.id = p.\"ownerId\"
                 WHERE s.id != $1
                 AND p.hidden = FALSE
                 AND NOT EXISTS (
                     SELECT 1 FROM friends f
                     WHERE (f.id1 = $1 AND f.id2 = s.id) OR (f.id2 = $1 AND f.id1 = s.id)
                 )
                 AND NOT EXISTS (
                     SELECT 1 FROM friend_requests fr
                     WHERE (fr.id1 = $1 AND fr.id2 = s.id) OR (fr.id2 = $1 AND fr.id1 = s.id)
                 )
                 ";
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