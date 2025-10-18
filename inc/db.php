<?php
//共享 PDO 实例
static $db;
if (!$db) {
    $db = new PDO('sqlite:'.__DIR__.'/../db/taufik.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
?>
