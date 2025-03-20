<?php
session_start();

// ログイン試行回数を制限（ブルートフォース対策）
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SESSION['login_attempts'] >= 5) {
    die("試行回数が多すぎます。しばらく待ってから再試行してください。");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 入力値取得＆サニタイズ
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email) {
        die("無効なメールアドレスです。");
    }

    // データベース接続
    $conn = new mysqli("localhost", "root", "", "contact_form_db");
    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    // ユーザー情報を取得
    $sql = "SELECT id, password FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // パスワードの確認
        if (password_verify($password, $user['password'])) {
            // セッション管理の強化
            session_regenerate_id(true);

            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            
            // ログイン試行回数をリセット
            $_SESSION['login_attempts'] = 0;
            
            header("Location: user_dashboard.php");
            exit;
        } else {
            $_SESSION['login_attempts']++;
        }
    } else {
        $_SESSION['login_attempts']++;
    }

    $error = "ログインに失敗しました。";
    
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
