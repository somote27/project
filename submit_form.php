<?php
header("Access-Control-Allow-Origin: *"); // すべてのオリジンを許可
header("Access-Control-Allow-Methods: POST"); // 許可するメソッドを指定
header("Access-Control-Allow-Headers: Content-Type"); // 許可するヘッダーを指定

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    echo "このページは POST 用です。フォームから送信してください。";
    exit();
}

// フォームデータの受け取り
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$message = htmlspecialchars($_POST['message']);

$errorMessage = "";

// バリデーション
if (empty($name)) {
    $errorMessage .= "名前を入力してください。<br>";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errorMessage .= "正しいメールアドレスを入力してください。<br>";
}

if (empty($message)) {
    $errorMessage .= "メッセージを入力してください。<br>";
}

if ($errorMessage) {
    echo "<span style='color: red;'>$errorMessage</span>";
} else {
    // データベース接続設定
    $servername = "localhost";
    $username = "root"; // XAMPPの場合、デフォルトのユーザーは root です
    $password = ""; // デフォルトではパスワードなし
    $dbname = "contact_form_db"; // データベース名

    // データベース接続
    $conn = new mysqli($servername, $username, $password, $dbname);

    // 接続確認
    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    // データベースにデータを挿入
    $stmt = $conn->prepare("INSERT INTO users (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<span style='color: green;'>フォームが正常に送信されました！</span>";
        echo "<p>送信された内容:</p>";
        echo "<strong>名前:</strong> $name<br>";
        echo "<strong>メール:</strong> $email<br>";
        echo "<strong>メッセージ:</strong><br>$message<br>";
    } else {
        echo "<span style='color: red;'>送信エラー: データベースに保存できませんでした。</span>";
    }

    // 接続を閉じる
    $stmt->close();
    $conn->close();
}
?>
