// register.php
<?php
// パスワードをハッシュ化
$hashed_password = password_hash('password123', PASSWORD_DEFAULT); // 管理者用パスワードをハッシュ化

// データベースに接続
$servername = "localhost";
$username = "root";
$password = "";  // MySQLのパスワード
$dbname = "contact_form_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// 新しいユーザーを追加するクエリ
$sql = "INSERT INTO users (name, email, message, password) VALUES ('admin', 'admin@example.com', '管理者用メッセージ', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "新しいユーザーが作成されました。";
} else {
    echo "エラー: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
