<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');  // ログインしていない場合はログインページにリダイレクト
    exit;
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ダッシュボード</title>
    <link rel="stylesheet" href="../css/style.css"> <!-- 修正したCSSのパス -->
</head>
<body>
    <div class="dashboard-container">
        <h2>管理者ダッシュボード</h2>
        <p>ようこそ、管理者さん！</p>
        
        <!-- 新たに追加したリンク -->
        <ul>
            <li><a href="view_contacts.php">お問い合わせ内容一覧</a></li>
            <li><a href="post_management.php">投稿管理</a></li>
            <li><a href="user_management.php">ユーザー管理</a></li>
        </ul>
        
        <a href="logout.php">ログアウト</a>
    </div>
</body>
</html>
