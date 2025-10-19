<?php
require 'auth.php';
require '../inc/db.php';
$in  = json_decode(file_get_contents('php://input'), true);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $year = $_GET['year'] ?? date('Y');
        $sql = "SELECT h.*, COALESCE((SELECT SUM(points) FROM house_points WHERE house_id=h.id AND date LIKE '${year}%'),0) AS total
                FROM houses h ORDER BY h.name";
        json($db->query($sql)->fetchAll(PDO::FETCH_ASSOC));
        break;

    case 'POST':   // create / edit
        if ($jwt->role > 2) json(['error'=>'Forbidden'],403);
        if (isset($in['id'])) {        // update
            $st = $db->prepare("UPDATE houses SET name=?, color=?, motto=?, patron_id=? WHERE id=?");
            $st->execute([$in['name'],$in['color'],$in['motto'],$in['patron_id']?:NULL,$in['id']]);
        } else {                       // insert
            $st = $db->prepare("INSERT INTO houses(name,color,motto,patron_id) VALUES(?,?,?,?)");
            $st->execute([$in['name'],$in['color'],$in['motto'],$in['patron_id']?:NULL]);
            $in['id'] = $db->lastInsertId();
        }
        json($in);
        break;

    case 'PATCH':  // award points
        $st = $db->prepare("INSERT INTO house_points(house_id,date,points,reason,teacher_id) VALUES(?,?,?,?,?)");
        $st->execute([$in['house_id'], $in['date']??date('Y-m-d'), $in['points'], $in['reason'], $jwt->uid]);
        json(['id'=>$db->lastInsertId()]);
        break;

    case 'DELETE':
        if ($jwt->role > 2) json(['error'=>'Forbidden'],403);
        $st = $db->prepare("DELETE FROM houses WHERE id=? AND NOT EXISTS(SELECT 1 FROM student_house WHERE house_id=houses.id)");
        $st->execute([$in['id']]);
        json(['affected'=>$st->rowCount()]);
        break;
}
function json($d,$c=200){http_response_code($c);echo json_encode($d);exit;}
?>
