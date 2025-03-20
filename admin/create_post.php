<?php
// admin でのみアクセスできるように確認
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php"); // ログインページにリダイレクト
    exit;
}

// データベース接続
$servername = "localhost";
$username = "root";
$password = "";  // データベースパスワード
$dbname = "contact_form_db";

// 接続
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// フォームからデータが送信された場合、投稿をデータベースに追加
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // SQL文の準備
    $sql = "INSERT INTO posts (title, content) VALUES ('$title', '$content')";

    if ($conn->query($sql) === TRUE) {
        // 投稿が成功した場合、投稿管理ページにリダイレクト
        header("Location: post_management.php");
        exit;
    } else {
        echo "エラー: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新しい投稿作成</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="create-post-container">
        <h2>新しい投稿を作成</h2>
        <form method="POST" action="create_post.php">
            <label for="title">タイトル:</label>
            <input type="text" id="title" name="title" required>

            <label for="content">内容:</label>
            <textarea id="content" name="content" required></textarea>

            <button type="submit">投稿を作成</button>
        </form>
    </div>
</body>
</html>
