<?php
$servername = "localhost";
$username = "root";  // XAMPP の MySQL ユーザー名
$password = "";      // XAMPP のデフォルトの MySQL パスワード
$dbname = "contact_form_db"; // 既存のデータベース名に変更

// データベース接続
$conn = new mysqli($servername, $username, $password, $dbname);

// 接続確認
if ($conn->connect_error) {
    die("接続に失敗しました: " . $conn->connect_error);
} else {
    // echo "データベースに接続成功しました。";  // 接続成功メッセージは不要なのでコメントアウト
}
?>
