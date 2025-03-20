<?php
// データベースに接続
$servername = "localhost";
$username = "root";
$password = "";  // MySQLのパスワード
$dbname = "contact_form_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

// フォームから送信されたデータを取得
$username = $_POST['username'];
$password = $_POST['password'];

// データベースからユーザー情報を取得
$sql = "SELECT * FROM users WHERE name = '$username' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // ユーザーが見つかった場合、パスワードを照合
    $row = $result->fetch_assoc();
    $hashed_password = $row['password'];

    // パスワードの照合
    if (password_verify($password, $hashed_password)) {
        // ログイン成功
        session_start();
        $_SESSION['logged_in'] = true;  // セッションにログイン状態を保存
        header('Location: admin_dashboard.php');  // 管理者ダッシュボードページにリダイレクト
        exit;
    } else {
        // パスワードが間違っている場合
        $error_message = 'ユーザー名またはパスワードが間違っています。';
    }
} else {
    // ユーザーが見つからない場合
    $error_message = 'ユーザー名またはパスワードが間違っています。';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン失敗</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>管理者ログイン</h2>
        <p style="color: red;"><?php echo isset($error_message) ? $error_message : ''; ?></p> <!-- エラーメッセージ表示 -->
        <form method="POST" action="authenticate.php">
            <label for="username">ユーザー名:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">パスワード:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">ログイン</button>
        </form>
    </div>
</body>
</html>
