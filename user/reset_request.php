<?php
session_start();
require_once "../config.php"; // 必要な設定やデータベース接続

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // データベース接続
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME); // config.php から設定値を利用
    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    // トークンと有効期限を確認するクエリ
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    // 有効なトークンが見つかった場合、ユーザー情報を取得
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // POSTリクエストの場合（フォーム送信時）
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            // パスワードが一致するか確認
            if ($password === $confirm_password) {
                // パスワードのハッシュ化
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // パスワード更新、トークンと有効期限をクリア
                $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $user['id']);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $success = "パスワードが正常にリセットされました。";
                } else {
                    $error = "パスワードの更新に失敗しました。";
                }
            } else {
                $error = "パスワードが一致しません。";
            }
        }
    } else {
        $error = "無効なリセットリンクまたはリンクの有効期限が切れています。";
    }

    $stmt->close();
    $conn->close();
} else {
    $error = "無効なリセットリンクです。";
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>新しいパスワードの設定</title>
</head>
<body>
    <h2>新しいパスワードの設定</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (!$success && empty($error)): ?>
    <form method="POST" action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>">
        <label for="password">新しいパスワード:</label>
        <input type="password" id="password" name="password" required><br>

        <label for="confirm_password">確認用パスワード:</label>
        <input type="password" id="confirm_password" name="confirm_password" required><br>

        <button type="submit">パスワードを変更</button>
    </form>
    <?php endif; ?>
</body>
</html>
