<?php
require '../inc/auth.php';
require '../inc/db.php';
$students = $db->query("SELECT id, first_name, last_name, code FROM students LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
header('Content-Type: application/json');
echo json_encode($students);
