<?php
require 'auth.php';
require '../inc/db.php';

$stmt = $db->prepare("SELECT id, code, first_name, last_name, class_id, boarding, status FROM students WHERE status = 1 ORDER BY first_name");
$stmt->execute();
json($stmt->fetchAll(PDO::FETCH_ASSOC));

function json($d, $c = 200) {
    http_response_code($c);
    echo json_encode($d, JSON_UNESCAPED_UNICODE);
    exit;
}
?>
