<?php
// api/house.php  –  CRUD for houses & house-points
require '../inc/auth.php'; // JWT + CORS headers
$db  = new PDO('sqlite:../db/taufik.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$uid  = $jwt->uid;
$role = $jwt->role;          // 1=Super 2=Admin 3=Teacher …
$in   = json_decode(file_get_contents('php://input'), true);

function json($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {

    /* ========== LIST ALL HOUSES (with live totals) ========== */
    case 'GET':
        $year = $_GET['year'] ?? date('Y');
        $sql  = "SELECT h.*, u.name AS patron_name,
                        COALESCE((
                            SELECT SUM(points) 
                            FROM house_points 
                            WHERE house_id = h.id
                              AND date BETWEEN :start AND :end
                        ),0) AS total_points
                 FROM houses h
                 LEFT JOIN users u ON u.id = h.patron_id
                 ORDER BY h.name";
        $stmt = $db->prepare($sql);
        $stmt->execute(['start' => "$year-01-01", 'end' => "$year-12-31"]);
        json($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    /* ========== CREATE / EDIT HOUSE ========== */
    case 'POST':
        if ($role > 2) json(['error' => 'Forbidden'], 403);

        if (isset($in['id'])) {                       // UPDATE
            $stmt = $db->prepare("UPDATE houses
                                  SET name = ?, color = ?, motto = ?, patron_id = ?
                                  WHERE id = ?");
            $stmt->execute([$in['name'], $in['color'], $in['motto'],
                            $in['patron_id'] ?: null, $in['id']]);
            json(['affected' => $stmt->rowCount()]);
        } else {                                      // CREATE
            $stmt = $db->prepare("INSERT INTO houses(name,color,motto,patron_id)
                                  VALUES(?,?,?,?)");
            $stmt->execute([$in['name'], $in['color'],
                            $in['motto'], $in['patron_id'] ?: null]);
            json(['id' => $db->lastInsertId()]);
        }
        break;

    /* ========== DELETE HOUSE (only if empty) ========== */
    case 'DELETE':
        if ($role > 2) json(['error' => 'Forbidden'], 403);
        $stmt = $db->prepare("DELETE FROM houses
                              WHERE id = ?
                                AND NOT EXISTS(SELECT 1
                                               FROM student_house
                                               WHERE house_id = houses.id)");
        $stmt->execute([$in['id']]);
        json(['affected' => $stmt->rowCount()]);
        break;

    /* ========== AWARD / DEDUCT HOUSE POINTS ========== */
    case 'PATCH':
        // any teacher may award; could tighten to patron only
        $stmt = $db->prepare("INSERT INTO house_points(house_id,date,points,reason,teacher_id)
                              VALUES(?,?,?,?,?)");
        $stmt->execute([$in['house_id'], $in['date'] ?? date('Y-m-d'),
                        $in['points'], $in['reason'], $uid]);
        json(['id' => $db->lastInsertId()]);
        break;
}
