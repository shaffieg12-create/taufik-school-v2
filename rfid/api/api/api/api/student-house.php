<?php
require 'auth.php';
require '../inc/db.php';
$in = json_decode(file_get_contents('php://input'),true);

$st = $db->prepare("REPLACE INTO student_house(student_id,house_id,year,role) VALUES(?,?,?,?)");
$st->execute([$in['student_id'],$in['house_id'],$in['year'],$in['role']??'Member']);
json(['status'=>'ok']);

function json($d,$c=200){http_response_code($c);echo json_encode($d);exit;}
?>
