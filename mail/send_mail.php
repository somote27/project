$mail = new PHPMailer(true);

try {
    // サーバー設定
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'kaihatuakaunnto@gmail.com';
    $mail->Password = 'mzfp cack tgvy dilv';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // デバッグモードをオフにする（本番環境向け）
    $mail->SMTPDebug = 0; 

    // 文字エンコーディング設定
    $mail->CharSet = 'UTF-8';

    // 送信者設定
    $mail->setFrom('kaihatuakaunnto@gmail.com', '開発アカウント');

    // 受信者設定
    $recipientEmail = $_POST['recipient_email'] ?? '';
    if (empty($recipientEmail)) {
        throw new Exception('受信者のメールアドレスが指定されていません。');
    }
    $mail->addAddress($recipientEmail);

    // メール本文の取得
    $name = $_POST['name'] ?? '名無し';
    $message = $_POST['message'] ?? 'メッセージがありません。';

    // 件名
    $mail->Subject = 'パスワードリセットリンク';

    // メール内容（HTML）
    $mail->isHTML(true);
    $mail->Body    = "<strong>名前:</strong> $name <br><strong>メッセージ:</strong> $message";
    $mail->AltBody = "名前: $name\nメッセージ: $message";

    // メール送信
    if ($mail->send()) {
        echo 'メールが送信されました！';
    }
} catch (Exception $e) {
    echo "メール送信に失敗しました。エラー: " . $mail->ErrorInfo . "<br>";
    echo "詳細なエラー内容: " . $e->getMessage();
}
