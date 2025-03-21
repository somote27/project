<?php
session_start();
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: http://localhost/project/user/user_login.php');  // ログインページにリダイレクト
    exit;
}

// データベース接続（ユーザー情報取得用）
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "contact_form_db";
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

// エラーメッセージをログに記録する（運用時のため）
if ($conn->connect_error) {
    error_log("接続失敗: " . $conn->connect_error);  // ログに記録
    die("接続に失敗しました。");  // ユーザーには一般的なメッセージを表示
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// ユーザー情報を取得
$user = $result->fetch_assoc();

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザーダッシュボード</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>ユーザーダッシュボード</h2>
    <p>ようこそ、<?php echo htmlspecialchars($user['name']); ?> さん</p>
    <p>メールアドレス: <?php echo htmlspecialchars($user['email']); ?></p>
    <!-- 今後、ユーザーの投稿やプロフィール編集などの機能を追加可能 -->
    <a href="http://localhost/project/user/user_logout.php">ログアウト</a>  <!-- 絶対URLに修正 -->
</body>
</html>
