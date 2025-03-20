<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 入力された値を取得
    $email = $_POST['email'];
    $password = $_POST['password'];

    // データベース接続設定
    $servername = "localhost";
    $dbUsername = "root";
    $dbPassword = "";  // MySQLのパスワード
    $dbname = "contact_form_db";

    // データベースに接続
    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);
    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    // ユーザー情報を取得するクエリ（メールアドレスで検索）
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // ハッシュ化されたパスワードとの照合
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            header("Location: user_dashboard.php");
            exit;
        } else {
            $error = "パスワードが間違っています。";
        }
    } else {
        $error = "メールアドレスが見つかりません。";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザーログイン</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h2>ユーザーログイン</h2>
    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST" action="user_login.php">
        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">ログイン</button>
    </form>
</body>
</html>
