<?php
// taufik-school/setup.php  (house-aware installer)
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_POST) {
    // 1. ensure db folder exists
    if (!is_dir('../db')) mkdir('../db', 0777, true);

    // 2. create / open database
    $db = new PDO('sqlite:../db/taufik.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 3. run full schema (includes houses, student_house, house_points)
    $sql = file_get_contents(__DIR__ . '/db/schema.sql');
    $db->exec($sql);

    // 4. create super-user
    $stmt = $db->prepare("INSERT INTO users(name,email,password,role) VALUES(?,?,?,1)");
    $stmt->execute([$_POST['head'], $_POST['email'], password_hash($_POST['pw'], PASSWORD_DEFAULT)]);

    // 5. seed default houses
    $db->exec("INSERT INTO houses(name,color,motto) VALUES
              ('Golooba House','#DC2626','Unity & Strength'),
              ('Mayanja House','#2563EB','Knowledge is Light')");

    // 6. lock installer
    touch('../db/installed.lock');
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Taufik School – 2-step Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="max-w-xl mx-auto mt-20 p-8 bg-white rounded shadow">
    <img src="assets/img/logo-placeholder.png" class="h-16 mx-auto mb-4" alt="Logo"/>
    <h1 class="text-2xl font-bold text-green-700 mb-2 text-center">Taufik Junior School & Qur'an Centre</h1>
    <p class="mb-6 text-sm text-gray-600 text-center">Fill once, start using in 60 seconds.</p>

    <form method="post" autocomplete="off">
        <input required name="head" placeholder="Head-teacher full name" class="w-full mb-3 p-2 border rounded">
        <input required name="email" type="email" placeholder="Admin email" class="w-full mb-3 p-2 border rounded">
        <input required name="pw" type="password" placeholder="Create password" class="w-full mb-6 p-2 border rounded">
        <button class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">Install & Launch</button>
    </form>
</div>
</body>
</html>
