<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ComposerでインストールしたPHPMailerのクラスを読み込む
require '../vendor/autoload.php';  // PHPMailerのオートロード

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
    $recipientEmail = isset($_POST['recipient_email']) ? $_POST['recipient_email'] : '';
    if (empty($recipientEmail)) {
        throw new Exception('受信者のメールアドレスが指定されていません。');
    }
    $mail->addAddress($recipientEmail);

    // メール本文の取得 & エンコード対策
    $name = isset($_POST['name']) ? $_POST['name'] : '名無し';
    $message = isset($_POST['message']) ? $_POST['message'] : 'メッセージがありません。';

    $name = mb_convert_encoding($name, 'UTF-8', 'auto');
    $message = mb_convert_encoding($message, 'UTF-8', 'auto');

    // メール内容設定
    $mail->isHTML(true);
    $mail->Subject = mb_encode_mimeheader('テストメール', 'UTF-8');
    $mail->Body    = "<strong>Name:</strong> $name <br><strong>Message:</strong> $message";
    $mail->AltBody = "Name: $name\nMessage: $message";

    // メール送信
    if ($mail->send()) {
        echo 'メールが送信されました！';
    }
} catch (Exception $e) {
    echo "メール送信に失敗しました。エラー: " . $mail->ErrorInfo . "<br>";
    echo "詳細なエラー内容: " . $e->getMessage();
}
?>
