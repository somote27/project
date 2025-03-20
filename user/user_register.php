<?php
// フォーム送信後の処理
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // エラーメッセージ
    $error_message = '';

    // パスワードが8文字未満の場合
    if (strlen($password) < 8) {
        $error_message = 'パスワードは8文字以上で入力してください。';
    }

    // エラーがなければデータベースに登録（仮に処理する部分をここに書く）
    if (empty($error_message)) {
        // データベース処理をここで実行（仮の処理）
        echo 'ユーザー登録が完了しました。';
        // ここでメール送信処理などを追加
    }
}
?>

<!-- ユーザー登録フォーム -->
<form method="POST" action="user_register.php">
    <label for="name">名前:</label>
    <input type="text" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required><br>

    <label for="email">メールアドレス:</label>
    <input type="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required><br>

    <label for="password">パスワード:</label>
    <input type="password" name="password" required><br>

    <?php
    // エラーメッセージがあれば表示
    if (!empty($error_message)) {
        echo '<p style="color:red;">' . $error_message . '</p>';
    }
    ?>

    <button type="submit">登録</button>
</form>

<?php
// メール送信処理の例 (PHPMailerを使用)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ComposerでインストールしたPHPMailerのクラスを読み込む
require '../vendor/autoload.php';  // PHPMailerのオートロード

if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($error_message)) {
    // PHPMailerオブジェクトのインスタンス作成
    $mail = new PHPMailer(true);

    try {
        // サーバー設定
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // GmailのSMTPサーバー
        $mail->SMTPAuth = true;
        $mail->Username = 'kaihatuakaunnto@gmail.com';  // Gmailアカウント
        $mail->Password = 'mzfp cack tgvy dilv';  // アプリパスワード
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // TLS暗号化
        $mail->Port = 587;

        // デバッグモード設定（ログを出さない）
        $mail->SMTPDebug = 0;  // 0: ログなし, 1: 簡易ログ, 2: 詳細ログ

        // 文字エンコーディング設定（文字化け防止）
        $mail->CharSet = 'UTF-8';

        // 送信者設定
        $senderName = '開発アカウント';  // 送信者名
        $mail->setFrom('kaihatuakaunnto@gmail.com', $senderName);

        // 受信者設定（フォームから取得）
        $recipientEmail = $email;  // 登録されたメールアドレス
        $mail->addAddress($recipientEmail);

        // メール本文の取得 & エンコード対策
        $message = "名前: $name\n\nパスワードが設定されました。";

        // メール内容設定
        $mail->isHTML(true);
        $mail->Subject = 'ユーザー登録完了のお知らせ';
        $mail->Body    = "<strong>Name:</strong> $name <br><strong>Password:</strong> $password"; // 本文
        $mail->AltBody = "Name: $name\nPassword: $password"; // テキスト版

        // メール送信
        if ($mail->send()) {
            echo '確認メールが送信されました！';
        }
    } catch (Exception $e) {
        echo "メール送信に失敗しました。エラー: " . $mail->ErrorInfo . "<br>";
        echo "詳細なエラー内容: " . $e->getMessage();
    }
}
?>
