<?php
// 极简 JWT 校验示例
session_start();
if (!isset($_SESSION['uid'])) {
    header('Location: login.php'); exit;
}
// 伪造 JWT 对象供 api 使用
$jwt = (object)['uid'=>$_SESSION['uid'],'role'=>$_SESSION['role']];
?>
