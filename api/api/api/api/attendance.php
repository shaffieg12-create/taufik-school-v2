<?php
require 'auth.php';
require '../inc/db.php';
$date = $_GET['date'] ?? date('Y-m-d');

$stmt = $db->prepare("SELECT a.*, s.first_name||' '||s.last_name AS student
                      FROM attendance a JOIN students s ON s.id=a.student_id
                      WHERE a.date=? ORDER BY a.time");
$stmt->execute([$date]);
json($stmt->fetchAll(PDO::FETCH_ASSOC));

function json($d,$c=200){http_response_code($c);echo json_encode($d);exit;}
?>
