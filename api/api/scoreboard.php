<?php
require 'auth.php';
require '../inc/db.php';
$year = $_GET['year'] ?? date('Y');

$sql = "SELECT h.id, h.name, h.color, h.logo,
               COALESCE((SELECT SUM(points) FROM house_points WHERE house_id=h.id AND date LIKE '${year}%'),0) AS total
        FROM houses h ORDER BY total DESC";
json($db->query($sql)->fetchAll(PDO::FETCH_ASSOC));

function json($d,$c=200){http_response_code($c);echo json_encode($d);exit;}
?>
