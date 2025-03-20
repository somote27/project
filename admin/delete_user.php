<?php
// セッション開始
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "投稿IDが指定されていません。";
    exit;
}

$id = $_GET['id'];

// データベース接続
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "contact_form_db";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 投稿を削除するクエリ
$delete_sql = "DELETE FROM posts WHERE id = $id";

if ($conn->query($delete_sql) === TRUE) {
    echo "投稿が削除されました。";
    header("Location: post_management.php");
    exit;
} else {
    echo "エラー: " . $conn->error;
}

$conn->close();
?>
