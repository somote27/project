<?php
session_start(); // セッション開始

// セッション変数を全て削除
$_SESSION = [];

// セッションを破棄
session_destroy();

// ログインページにリダイレクト（絶対URL指定）
header("Location: http://localhost/project/user/user_login.php");
exit;
?>
