<?php
// セッション開始
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// データベース接続
$servername = "localhost";
$username = "root";
$password = "";  // MySQLのパスワード
$dbname = "contact_form_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 投稿IDを取得
$id = isset($_GET['id']) ? $_GET['id'] : 0;

if ($id) {
    // 投稿を削除
    $delete_sql = "DELETE FROM posts WHERE id = $id";
    if ($conn->query($delete_sql) === TRUE) {
        header('Location: post_management.php');
        exit;
    } else {
        echo "エラー: " . $conn->error;
    }
}

$conn->close();
?>
