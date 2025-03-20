<?php
// verify.php

// セッションスタート
session_start();

// DB接続
require '../config/db_connect.php'; // db_connect.phpをインクルード

// URLパラメータからメールアドレスを取得
if (isset($_GET['email'])) {
    $email = filter_var($_GET['email'], FILTER_SANITIZE_EMAIL);
    
    // メールアドレスの検証
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("無効なメールアドレスです。");
    }

    // ユーザーの確認状態を「verified」に更新
    $sql = "UPDATE users SET message = 'verified' WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        // 確認完了メッセージを表示
        echo "アカウントが確認されました。ログインしてください。";
    } else {
        echo "確認に失敗しました。";
    }

    // リソース解放
    $stmt->close();
    $conn->close();
} else {
    die("無効なリクエストです。");
}
?>
