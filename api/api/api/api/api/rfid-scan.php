<?php
// no session gate – called by Node-RED or Python
require '../inc/db.php';
$card   = $_POST['card']   ?? '';
$reader = $_POST['reader_id'] ?? 'gate';
$ts     = $_POST['ts']     ?? date('Y-m-d H:i:s');

$stu = $db->prepare("SELECT id, first_name, parent_phone, boarding FROM students WHERE rfid_card=? AND status=1");
$stu->execute([$card]);
if (!$row=$stu->fetch()) { http_response_code(404); exit('Unknown card'); }

$dir = $db->prepare("SELECT direction FROM attendance WHERE student_id=? ORDER BY id DESC LIMIT 1");
$dir->execute([$row['id']]);
$last = $dir->fetchColumn();
$direction = ($last==='IN') ? 'OUT':'IN';

$ins = $db->prepare("INSERT INTO attendance(student_id,date,time,direction,reader_id) VALUES(?,?,?,?,?)");
$ins->execute([$row['id'], substr($ts,0,10), substr($ts,11,5), $direction, $reader]);

// SMS parent on gate-IN
if ($reader==='gate' && $direction==='IN') {
    $text = "Dear parent, ".$row['first_name']." has ARRIVED at school at ".substr($ts,11,5).".";
    // sendSMS($row['parent_phone'], $text);   // plug your SMS gateway
}
echo 'OK';
