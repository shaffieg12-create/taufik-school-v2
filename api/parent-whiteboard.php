<?php
require '../inc/db.php';
require '../inc/auth.php';
$year  = date('Y');
$phone = $_SESSION['parent_phone'];
$kids  = $db->prepare("SELECT id, first_name, last_name, class_id FROM students WHERE parent_phone = ? AND status = 1");
$kids->execute([$phone]);
$kids  = $kids->fetchAll(PDO::FETCH_ASSOC);
$kidIds= array_column($kids, 'id');
?>
<!doctype html>
<html lang="en"><head><meta charset="utf-8"><title>Parent Notice Board</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
<style>.notice-card{background:#fff;border-left:4px solid #10b981}.fee-alert{background:#fef3c7;border-left:4px solid #f59e0b}.event-card{background:#dbeafe;border-left:4px solid #3b82f6}</style></head>
<body class="bg-gray-100 p-4"><div class="max-w-3xl mx-auto space-y-6">
<section class="notice-card shadow p-4 rounded"><h2 class="font-bold text-lg mb-2">My Children</h2><ul class="list-disc ml-6">
<?php foreach ($kids as $k): ?>
<li><?= htmlspecialchars($k['first_name'].' '.$k['last_name']) ?> <span class="text-sm text-gray-600">(<?= $k['class_id'] ?>)</span></li>
<?php endforeach; ?></ul></section>
<section class="fee-alert shadow p-4 rounded"><h2 class="font-bold text-lg mb-2">Fee Reminders</h2><?php
$fee = $db->prepare("SELECT i.term, i.balance, s.first_name FROM fee_invoices i JOIN students s ON s.id = i.student_id WHERE i.student_id IN (".implode(',',$kidIds).") AND i.balance > 0 ORDER BY i.due_date");
$fee->execute(); $rows=$fee->fetchAll(); if($rows): foreach($rows as $r): ?>
<p class="mb-1"><?= $r['first_name'] ?> – <?= $r['term'] ?>: UGX <?= number_format($r['balance']) ?></p>
<?php endforeach; else: echo '<p class="text-green-700">All fees up-to-date 👍</p>'; endif; ?></section>
<section class="event-card shadow p-4 rounded"><h2 class="font-bold text-lg mb-2">Upcoming Events</h2><?php
$evt = $db->query("SELECT title, edate, venue FROM events WHERE edate >= date('now') ORDER BY edate LIMIT 5"); $rows=$evt->fetchAll(); if($rows): foreach($rows as $e): ?>
<p class="mb-1"><b><?= htmlspecialchars($e['title']) ?></b> – <?= date('d M Y',strtotime($e['edate'])) ?> <span class="text-sm text-gray-600">(<?= htmlspecialchars($e['venue']) ?>)</span></p>
<?php endforeach; else: echo '<p>No events scheduled yet.</p>'; endif; ?></section>
<section class="bg-white shadow p-4 rounded"><h2 class="font-bold text-lg mb-2">House Points <?= $year ?></h2><?php
$sb = $db->prepare("SELECT h.name, h.color, h.logo, COALESCE((SELECT SUM(points) FROM house_points WHERE house_id=h.id AND date LIKE :y),0) AS total FROM houses h ORDER BY total DESC");
$sb->execute(['y'=>$year.'%']); foreach($sb->fetchAll() as $r): ?>
<div class="flex items-center mb-2" style="border-left:6px solid <?= htmlspecialchars($r['color']) ?>
