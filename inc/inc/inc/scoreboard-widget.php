<?php
$year = date('Y');
$db->query("SELECT h.name,h.color,h.logo,
      COALESCE((SELECT SUM(points) FROM house_points WHERE house_id=h.id AND date LIKE '$year%'),0) AS total
      FROM houses h ORDER BY total DESC");
$houses = $db->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="bg-white p-4 rounded shadow">
  <h5 class="font-bold mb-2 text-gray-700">House Points <?= $year ?></h5>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
  <?php foreach ($houses as $h): ?>
    <div class="flex items-center p-2 rounded" style="background:<?= $h['color'] ?>20">
      <img src="uploads/houses/<?= $h['logo'] ?? 'house.png' ?>" class="h-8 mr-2">
      <span class="font-semibold"><?= $h['name'] ?></span>
      <span class="ml-auto text-lg"><?= $h['total'] ?></span>
    </div>
  <?php endforeach; ?>
  </div>
</div>
