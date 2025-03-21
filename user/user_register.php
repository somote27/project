<?php
require '../db_connect.php'; // データベース接続

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';  // PHPMailerのオートロード

// フォーム送信後の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $error_message = '';

    // パスワードのバリデーション（8文字以上）
    if (strlen($password) < 8) {
        $error_message = 'パスワードは8文字以上で入力してください。';
    }

    // メールアドレスの重複チェック
    if (empty($error_message)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error_message = 'このメールアドレスは既に登録されています。';
        }
        $stmt->close();
    }

    // ユーザー登録処理
    if (empty($error_message)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $token = bin2hex(random_bytes(16)); // メール認証用トークン
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, verified, token) VALUES (?, ?, ?, 0, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $token);

        if ($stmt->execute()) {
            // 確認メール送信
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'kaihatuakaunnto@gmail.com';
                $mail->Password = 'mzfp cack tgvy dilv'; // 安全のため環境変数を利用
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                // 送信者設定
                $mail->setFrom('kaihatuakaunnto@gmail.com', '開発アカウント');
                $mail->addAddress($email);

                // メール内容
                $verification_link = "http://localhost/project/user/verify.php?token=$token";
                $mail->isHTML(true);
                $mail->Subject = 'ユーザー登録の確認';
                $mail->Body = "こんにちは $name さん！<br>以下のリンクをクリックして登録を完了してください。<br><a href='$verification_link'>確認リンク</a>";
                $mail->AltBody = "以下のURLを開いて登録を完了してください。\n$verification_link";

                $mail->send();
                echo '仮登録が完了しました！ 確認メールを送信しましたので、メールをご確認ください。';
            } catch (Exception $e) {
                echo "メール送信に失敗しました。エラー: " . $mail->ErrorInfo;
            }
        } else {
            echo '登録に失敗しました。';
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!-- ユーザー登録フォーム -->
<form method="POST" action="user_register.php">
    <label for="name">名前:</label>
    <input type="text" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required><br>

    <label for="email">メールアドレス:</label>
    <input type="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required><br>

    <label for="password">パスワード:</label>
    <input type="password" name="password" required><br>

    <?php
    if (!empty($error_message)) {
        echo '<p style="color:red;">' . htmlspecialchars($error_message) . '</p>';
    }
    ?>

    <button type="submit">登録</button>
</form>
