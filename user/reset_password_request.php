<?php
session_start();
require_once "../config.php"; // 必要な設定やデータベース接続
require_once "../vendor/autoload.php"; // Composerのオートロード（PHPMailer読み込み）

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';
$success = '';

// フォームが送信された場合
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // データベース接続
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    // メールアドレスが存在するか確認
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // ユーザーが存在する場合
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // トークン生成と有効期限設定
        $token = bin2hex(random_bytes(50)); // トークン生成
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // 1時間後の有効期限

        // データベース更新： reset_token と reset_token_expiry を保存
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
        $stmt->bind_param("ssi", $token, $expiry, $user_id);
        $stmt->execute();

        // リセットリンクの作成（実際のドメインに置き換えてください）
        $reset_link = "http://localhost/project/user/reset_password.php?token=" . $token;

        // PHPMailer を使用してメール送信
        $mail = new PHPMailer(true);

        try {
            // サーバー設定
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'kaihatuakaunnto@gmail.com'; // 送信元のGmailアカウント
            $mail->Password   = 'mzfp cack tgvy dilv'; // アプリパスワード（2段階認証の場合）
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // 文字エンコーディングをUTF-8に設定
            $mail->CharSet = 'UTF-8'; // これを追加

            // 送信者設定
            $mail->setFrom('kaihatuakaunnto@gmail.com', '開発アカウント');
            $mail->addAddress($email);

            // コンテンツ設定
            $mail->isHTML(true);
            $mail->Subject = 'パスワードリセットリンク';
            $mail->Body    = "以下のリンクをクリックしてパスワードをリセットしてください:<br><a href=\"$reset_link\">リセットリンク</a>";
            $mail->AltBody = "以下のリンクをクリックしてパスワードをリセットしてください:\n$reset_link";

            $mail->send();
            $success = "リセットリンクを送信しました。メールをご確認ください。";
        } catch (Exception $e) {
            $error = "メール送信に失敗しました。エラー: " . $mail->ErrorInfo;
        }
    } else {
        $error = "指定されたメールアドレスは存在しません。";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>パスワードリセットリクエスト</title>
</head>
<body>
    <h2>パスワードリセットリクエスト</h2>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="email">メールアドレス:</label>
        <input type="email" name="email" required><br>
        <button type="submit">リセットリンクを送信</button>
    </form>
</body>
</html>
