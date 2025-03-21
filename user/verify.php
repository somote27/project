<?php
session_start();

// DB接続
require '../db_connect.php'; // db_connect.phpのパスが正しいか確認してください

// URLパラメータからトークンを取得
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // トークンが存在し、かつ未確認のユーザーを検索
    $sql = "SELECT * FROM users WHERE token = ? AND verified = 0";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('SQL準備エラー: ' . $conn->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        // トークンが有効な場合：ユーザーを確認状態に更新（tokenをNULLにして再利用防止）
        $update_sql = "UPDATE users SET verified = 1, token = NULL WHERE token = ?";
        $update_stmt = $conn->prepare($update_sql);
        if ($update_stmt === false) {
            die('SQL準備エラー: ' . $conn->error);
        }
        $update_stmt->bind_param("s", $token);
        if ($update_stmt->execute()) {
            // 確認成功メッセージとリンクを表示
            echo "アカウントが確認されました。<br>";
            echo "<a href='user_login.php' style='display:inline-block; padding:10px 20px; background-color:#4CAF50; color:white; text-decoration:none; margin:10px;'>ログイン画面へ</a>";
            echo "<a href='dashboard.php' style='display:inline-block; padding:10px 20px; background-color:#2196F3; color:white; text-decoration:none; margin:10px;'>ユーザーダッシュボードへ</a>";
        } else {
            echo "確認に失敗しました。再度試してください。";
        }
        $update_stmt->close();
    } else {
        // トークンが無効または既に確認された場合
        echo "無効なトークンまたは既に確認されたアカウントです。";
    }
    
    $stmt->close();
    $conn->close();
} else {
    die("無効なリクエストです。");
}
?>
