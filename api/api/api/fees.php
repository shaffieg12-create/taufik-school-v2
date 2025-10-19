<?php
require 'auth.php';
require '../inc/db.php';
$in = json_decode(file_get_contents('php://input'),true);

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $db->prepare("SELECT i.*, s.first_name||' '||s.last_name AS student
                          FROM fee_invoices i JOIN students s ON s.id=i.student_id
                          WHERE i.term=? ORDER BY i.balance DESC");
    $stmt->execute([$_GET['term']]);
    json($stmt->fetchAll(PDO::FETCH_ASSOC));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') { // record payment
    $st = $db->prepare("INSERT INTO fee_payments(invoice_id,amount,channel,ref) VALUES(?,?,?,?)");
    $st->execute([$in['invoice_id'],$in['amount'],$in['channel'],$in['ref']]);
    json(['id'=>$db->lastInsertId()]);
}
function json($d,$c=200){http_response_code($c);echo json_encode($d);exit;}
?>
