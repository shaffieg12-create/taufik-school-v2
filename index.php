<?php
session_start();
require 'inc/auth.php';               // JWT check
require 'inc/db.php';                 // PDO $db
$year = date('Y');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Taufik Junior School & Qur'an Centre</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="manifest" href="manifest.json">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100">
<nav class="bg-green-700 text-white p-4 shadow">
  <div class="max-w-7xl mx-auto flex justify-between items-center">
    <span class="font-bold text-xl">Taufik School</span>
    <span><?= htmlspecialchars($_SESSION['user_name'] ?? 'Guest') ?></span>
  </div>
</nav>

<div class="max-w-7xl mx-auto p-6 grid md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- Card: attendance -->
  <div class="bg-white p-4 rounded shadow"><h3 class="font-bold mb-2">Today Attendance</h3><canvas id="attChart"></canvas></div>
  <!-- Card: fees -->
  <div class="bg-white p-4 rounded shadow"><h3 class="font-bold mb-2">Fees Collection</h3><canvas id="feesChart"></canvas></div>
  <!-- Card: house scoreboard -->
  <?php require 'inc/scoreboard-widget.php'; ?>
</div>

<script>
/* dummy charts */
const attCtx = document.getElementById('attChart'); new Chart(attCtx,{type:'doughnut',data:{labels:['Present','Absent'],datasets:[{data:[94,6],backgroundColor:['#10b981','#ef4444']}]}});
const feesCtx = document.getElementById('feesChart'); new Chart(feesCtx,{type:'bar',data:{labels:['Collected','Balance'],datasets:[{data:[18.4,3.6],backgroundColor:['#3b82f6','#f59e0b']}]}});
</script>
</body>
</html>
