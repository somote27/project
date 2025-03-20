<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 入力値の取得（XSS対策としてhtmlspecialcharsを使用）
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];  // パスワードはそのまま取得

    // パスワードのハッシュ化
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

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

    // 新しいユーザーを登録するSQL文
    // ※ messageカラムは空文字にしています（必要に応じて変更してください）
    $sql = "INSERT INTO users (name, email, password, message) VALUES (?, ?, ?, '')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "ユーザー登録が完了しました。";
    } else {
        echo "エラー: " . $stmt->error;
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
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="css/style.css"> <!-- CSSファイルへのパスはプロジェクトの構成に合わせる -->
</head>
<body>
    <h2>ユーザー登録</h2>
    <form method="POST" action="user_register.php">
        <label for="name">名前:</label>
        <input type="text" id="name" name="name" required><br>

        <label for="email">メールアドレス:</label>
        <input type="email" id="email" name="email" required><br>

        <label for="password">パスワード:</label>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">登録</button>
    </form>
</body>
</html>
